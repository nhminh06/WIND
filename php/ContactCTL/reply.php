<?php
session_start();
include '../../db/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $loai = $_POST['loai'] ?? '';
    $lien_he_id = $_POST['lien_he_id'] ?? 0;
    $noi_dung = trim($_POST['noi_dung'] ?? '');
    $from = $_POST['from'] ?? 'contact';

    $log_mail = []; // Lưu log gửi mail

    if ($loai && $lien_he_id && $noi_dung) {
        // Lấy thông tin user liên hệ
        $table = ($loai == 'khieu_nai') ? 'khieu_nai' : 'gop_y';
        $stmt = $conn->prepare("SELECT user_id, email, ho_ten FROM $table WHERE id = ?");
        $stmt->bind_param("i", $lien_he_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user) {
            // Thêm phản hồi vào DB
            $stmt = $conn->prepare("INSERT INTO phan_hoi (loai, lien_he_id, user_id, noi_dung) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siis", $loai, $lien_he_id, $user['user_id'], $noi_dung);
            $stmt->execute();
            $stmt->close();

            // Cập nhật trạng thái liên hệ đã xử lý
            $conn->query("UPDATE $table SET trang_thai = 1 WHERE id = $lien_he_id");

            // Kiểm tra email hợp lệ
            if (!empty($user['email']) && filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
                $domain = substr(strrchr($user['email'], "@"), 1);
                $allowed_domains = ['gmail.com', 'yahoo.com', 'outlook.com']; // Domain tin cậy
                $mx_exists = checkdnsrr($domain, "MX");

                if ($mx_exists && in_array($domain, $allowed_domains)) {
                    try {
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'mnhat0034@gmail.com';
                        $mail->Password   = 'rqxr eqjw fkoe wejf';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;
                        $mail->CharSet    = 'UTF-8';

                        $mail->setFrom('mnhat0034@gmail.com', 'Wind Tour');
                        $mail->addAddress($user['email'], $user['ho_ten']);
                        $mail->isHTML(true);
                        $mail->Subject = 'Phản hồi từ Wind Tour';
                        $mail->Body = "
                            <h2>Xin chào {$user['ho_ten']},</h2>
                            <p>Chúng tôi đã gửi phản hồi về liên hệ của bạn:</p>
                            <div style='border:1px solid #ccc; padding:10px; margin:10px 0;'>{$noi_dung}</div>
                            <p>Trân trọng,<br>Đội ngũ Wind Tour</p>
                        ";
                        $mail->send();

                        $_SESSION['thanhcong'] = 1;
                        $_SESSION['ketqua'] = "Gửi mail thành công";
                        $log_mail[] = [
                            'email' => $user['email'],
                            'domain' => $domain,
                            'mx' => $mx_exists,
                            'send_success' => 1
                        ];
                    } catch (Exception $e) {
                        error_log("Mail error: " . $mail->ErrorInfo);
                        $_SESSION['thanhcong'] = 0;
                        $_SESSION['ketqua'] = "Gửi mail thất bại: " . $mail->ErrorInfo;
                        $log_mail[] = [
                            'email' => $user['email'],
                            'domain' => $domain,
                            'mx' => $mx_exists,
                            'send_success' => 0,
                            'error' => $mail->ErrorInfo
                        ];
                    }
                } else {
                    $_SESSION['thanhcong'] = 0;
                    $_SESSION['ketqua'] = "Domain email không hợp lệ hoặc không được phép gửi";
                    $log_mail[] = [
                        'email' => $user['email'],
                        'domain' => $domain,
                        'mx' => $mx_exists,
                        'send_success' => 0
                    ];
                }
            } else {
                $_SESSION['thanhcong'] = 0;
                $_SESSION['ketqua'] = "Không có email hợp lệ để gửi";
            }
        } else {
            $_SESSION['thanhcong'] = 0;
            $_SESSION['ketqua'] = "Không tìm thấy user liên hệ";
        }
    } else {
        $_SESSION['thanhcong'] = 0;
        $_SESSION['ketqua'] = "Dữ liệu phản hồi không hợp lệ";
    }

    $conn->close();

    // Lưu log mail vào session (nếu cần xem lại)
    $_SESSION['log_mail'] = $log_mail;

    // Redirect về đúng trang
    if ($from == 'storage') {
        header("Location: ../../html/Admin/storage.php");
    } else {
        header("Location: ../../html/Admin/ContactController.php");
    }
    exit;
}
?>
