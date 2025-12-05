<?php
// DEBUG MODE - Hiển thị mọi lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
header('Content-Type: application/json; charset=utf-8');

$debug = []; // Lưu log debug

// === Kết nối DB ===
$debug[] = "Bước 1: Kiểm tra file db.php";
$db_path = __DIR__ . '/db.php';
if (!file_exists($db_path)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Thiếu file db.php',
        'debug' => $debug
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
$debug[] = "✓ File db.php tồn tại";

require_once $db_path;
global $conn;

if ($conn->connect_error) {
    echo json_encode([
        'success' => false, 
        'message' => 'Kết nối DB thất bại: ' . $conn->connect_error,
        'debug' => $debug
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
$debug[] = "✓ Kết nối DB thành công";

// === Kiểm tra bảng password_reset_tokens ===
$result = $conn->query("SHOW TABLES LIKE 'password_reset_tokens'");
if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Bảng password_reset_tokens chưa được tạo!',
        'debug' => $debug,
        'fix' => 'Chạy SQL: CREATE TABLE password_reset_tokens...'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
$debug[] = "✓ Bảng password_reset_tokens tồn tại";

// === Nạp PHPMailer ===
$debug[] = "Bước 2: Kiểm tra PHPMailer";
$phpmailer_dir = __DIR__ . '/PHPMailer/src';
if (!is_dir($phpmailer_dir)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Thiếu thư mục PHPMailer/src',
        'debug' => $debug,
        'path' => $phpmailer_dir
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
$debug[] = "✓ Thư mục PHPMailer tồn tại";

require_once "$phpmailer_dir/PHPMailer.php";
require_once "$phpmailer_dir/SMTP.php";
require_once "$phpmailer_dir/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$debug[] = "✓ PHPMailer loaded";

// === Kiểm tra request ===
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false, 
        'message' => 'Phương thức không hợp lệ',
        'debug' => $debug
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);
$email = trim($data['email'] ?? '');

$debug[] = "Bước 3: Email nhận được: $email";

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Email không hợp lệ',
        'debug' => $debug
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
$debug[] = "✓ Email hợp lệ";

// === Tìm user ===
$debug[] = "Bước 4: Tìm user trong DB";
$sql = "SELECT id, ho_ten FROM user WHERE email = ? AND trang_thai = 1";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode([
        'success' => false, 
        'message' => 'Lỗi prepare: ' . $conn->error,
        'debug' => $debug
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode([
        'success' => false, 
        'message' => 'Email chưa đăng ký hoặc bị khóa',
        'debug' => $debug
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
$debug[] = "✓ Tìm thấy user: ID={$user['id']}, Tên={$user['ho_ten']}";

// === Kiểm tra cooldown ===
$debug[] = "Bước 5: Kiểm tra cooldown";
$sql = "SELECT created_at FROM password_reset_tokens WHERE email = ? ORDER BY created_at DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$last = $result->fetch_assoc();

if ($last && (time() - strtotime($last['created_at']) < 60)) {
    $wait = 60 - (time() - strtotime($last['created_at']));
    echo json_encode([
        'success' => false, 
        'message' => "Vui lòng đợi {$wait} giây",
        'debug' => $debug
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
$debug[] = "✓ Không có cooldown";

// === XÓA TOKEN CŨ (FIX UNIQUE CONSTRAINT) ===
$debug[] = "Bước 6: Xóa token cũ của email này";
$sql = "DELETE FROM password_reset_tokens WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
if (!$stmt->execute()) {
    $debug[] = "⚠ Lỗi xóa token cũ: " . $stmt->error;
} else {
    $debug[] = "✓ Đã xóa token cũ (nếu có)";
}

// === Tạo token mới ===
$debug[] = "Bước 7: Tạo token mới";
$token = bin2hex(random_bytes(32));
$expires = date('Y-m-d H:i:s', time() + 300); // 5 phút

$debug[] = "Token: " . substr($token, 0, 20) . "...";
$debug[] = "Hết hạn: $expires";

$sql = "INSERT INTO password_reset_tokens (user_id, email, token, expires_at) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi prepare INSERT: ' . $conn->error,
        'debug' => $debug
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->bind_param("isss", $user['id'], $email, $token, $expires);

if (!$stmt->execute()) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi INSERT token: ' . $stmt->error,
        'sql_error_code' => $stmt->errno,
        'debug' => $debug
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$debug[] = "✓ Token đã lưu vào DB (ID: {$stmt->insert_id})";

// === Gửi email ===
$debug[] = "Bước 8: Gửi email";
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'minhminh778894@gmail.com';
    $mail->Password   = 'ycks grup khbb kbyx';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';
    
    // Debug SMTP
    // $mail->SMTPDebug = 2; // Uncomment để xem log SMTP chi tiết

    $mail->setFrom('minhminh778894@gmail.com', 'Wind Tour');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Khôi phục mật khẩu';

    $resetLink = "http://localhost:3000/php/reset_password.php?token=" . $token;
    $mail->Body = "
        <h2>Xin chào {$user['ho_ten']},</h2>
        <p>Nhấn nút để đặt lại mật khẩu:</p>
        <div style='text-align:center;margin:25px 0;'>
            <a href='$resetLink' style='background:#00adcc;color:white;padding:14px 32px;text-decoration:none;border-radius:8px;font-weight:bold;font-size:16px;'>Đặt lại mật khẩu</a>
        </div>
        <p>Hoặc copy link này: <br><a href='$resetLink'>$resetLink</a></p>
        <p><small>Liên kết hết hạn sau <b>5 phút</b>.</small></p>
    ";

    $mail->send();
    $debug[] = "✓ Email đã gửi thành công";
    
    echo json_encode([
        'success' => true, 
        'message' => 'Đã gửi liên kết khôi phục đến email!',
        'debug' => $debug,
        'token_preview' => substr($token, 0, 20) . '...'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    $debug[] = "✗ Lỗi gửi email: " . $e->getMessage();
    error_log("Mail error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false, 
        'message' => 'Lỗi gửi email',
        'mail_error' => $mail->ErrorInfo,
        'debug' => $debug
    ], JSON_UNESCAPED_UNICODE);
}
?>