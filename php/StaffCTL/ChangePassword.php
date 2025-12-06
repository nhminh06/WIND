<?php
session_start();
include('../../db/db.php');

// Đặt header trả về JSON
header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Vui lòng đăng nhập!'
    ]);
    exit();
}

$user_id = $_SESSION['user_id'];
$response = ['success' => false, 'message' => ''];

try {
    // Kiểm tra method POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Phương thức không hợp lệ!');
    }

    // Lấy dữ liệu từ POST
    $current_password = trim($_POST['current_password'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validate dữ liệu
    if (empty($current_password)) {
        throw new Exception('Vui lòng nhập mật khẩu hiện tại!');
    }

    if (empty($new_password)) {
        throw new Exception('Vui lòng nhập mật khẩu mới!');
    }

    if (empty($confirm_password)) {
        throw new Exception('Vui lòng xác nhận mật khẩu mới!');
    }

    // Kiểm tra độ dài mật khẩu mới
    if (strlen($new_password) < 6) {
        throw new Exception('Mật khẩu mới phải có ít nhất 6 ký tự!');
    }

    // Kiểm tra mật khẩu mới và xác nhận có khớp không
    if ($new_password !== $confirm_password) {
        throw new Exception('Mật khẩu mới và xác nhận mật khẩu không khớp!');
    }

    // Kiểm tra mật khẩu mới không trùng với mật khẩu cũ
    if ($current_password === $new_password) {
        throw new Exception('Mật khẩu mới không được trùng với mật khẩu hiện tại!');
    }

    // Bắt đầu transaction
    $conn->begin_transaction();

    // Lấy mật khẩu hiện tại từ database
    $sql = "SELECT password FROM user WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Không tìm thấy thông tin người dùng!');
    }

    $user = $result->fetch_assoc();
    $hashed_password = $user['password'];

    // Kiểm tra mật khẩu hiện tại
    if (!password_verify($current_password, $hashed_password)) {
        throw new Exception('Mật khẩu hiện tại không chính xác!');
    }

    // Hash mật khẩu mới
    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Cập nhật mật khẩu mới vào database
    $sql_update = "UPDATE user SET password = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $new_hashed_password, $user_id);

    if (!$stmt_update->execute()) {
        throw new Exception('Không thể cập nhật mật khẩu!');
    }

    // Commit transaction
    $conn->commit();

    $response['success'] = true;
    $response['message'] = 'Đổi mật khẩu thành công! Vui lòng đăng nhập lại.';

    // Xóa session để yêu cầu đăng nhập lại (tùy chọn)
    // session_destroy();

} catch (Exception $e) {
    // Rollback nếu có lỗi
    if (isset($conn)) {
        $conn->rollback();
    }
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// Trả về kết quả
echo json_encode($response, JSON_UNESCAPED_UNICODE);

// Đóng kết nối
if (isset($conn)) {
    $conn->close();
}
?>