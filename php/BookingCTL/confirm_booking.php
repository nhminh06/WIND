<?php
session_start();
include '../../db/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

// Kiểm tra quyền
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    header('Location: ../../index.php');
    exit();
}

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin booking
$sqll = "SELECT dt.*, t.ten_tour, t.so_ngay, tc.diem_khoi_hanh 
         FROM dat_tour dt
         LEFT JOIN tour t ON dt.tour_id = t.id
         LEFT JOIN tour_chi_tiet tc ON t.id = tc.tour_id
         WHERE dt.id = ?";
$stmtl = $conn->prepare($sqll);
$stmtl->bind_param("i", $booking_id);
$stmtl->execute();
$resultl = $stmtl->get_result();
$booking = $resultl->fetch_assoc();
$stmtl->close();

if (!$booking) {
    $_SESSION['error'] = "Không tìm thấy đơn đặt tour!";
    header('Location: ../../html/admin/manage_bookings.php');
    exit();
}

if ($booking['trang_thai_thanh_toan'] !== 'da_thanh_toan') {
    $_SESSION['error'] = "Không thể xác nhận đặt tour khi thanh toán chưa được xác nhận.";
    header('Location: ../../html/admin/manage_bookings.php');
    exit();
}

if ($booking_id > 0) {
    $sql = "UPDATE dat_tour SET trang_thai = 'confirmed' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Đã xác nhận đặt tour thành công!";
        
        // Gửi email thông báo xác nhận
        if (!empty($booking['email']) && filter_var($booking['email'], FILTER_VALIDATE_EMAIL)) {
            $domain = substr(strrchr($booking['email'], "@"), 1);
            $allowed_domains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com'];
            $mx_exists = checkdnsrr($domain, "MX");
            
            if ($mx_exists && in_array($domain, $allowed_domains)) {
                try {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'minhminh778894@gmail.com';
                    $mail->Password   = 'ycks grup khbb kbyx';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;
                    $mail->CharSet    = 'UTF-8';

                    $mail->setFrom('minhminh778894@gmail.com', 'Wind Tour');
                    $mail->addAddress($booking['email'], $booking['ho_ten']);
                    $mail->isHTML(true);
                    $mail->Subject = '✅ Đặt tour đã được xác nhận - Wind Tour';
                    
                    $mail->Body = "
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 10px; overflow: hidden;'>
                            <div style='background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 30px; text-align: center;'>
                                <h1 style='margin: 0; font-size: 28px;'>✅ ĐẶT TOUR ĐÃ ĐƯỢC XÁC NHẬN!</h1>
                            </div>
                            
                            <div style='padding: 30px; background-color: #f9f9f9;'>
                                <p style='font-size: 16px; color: #333;'>Xin chào <strong>{$booking['ho_ten']}</strong>,</p>
                                <p style='font-size: 14px; color: #666; line-height: 1.6;'>
                                    Chúng tôi vui mừng thông báo rằng đơn đặt tour của bạn đã được <strong style='color: #28a745;'>XÁC NHẬN THÀNH CÔNG</strong>!
                                </p>
                                
                                <div style='background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border-left: 4px solid #28a745; padding: 20px; margin: 20px 0; border-radius: 4px;'>
                                    <p style='margin: 0; color: #155724; font-size: 16px; font-weight: bold;'>
                                        🎉 Chuyến đi của bạn đã được xác nhận và đang được chuẩn bị!
                                    </p>
                                </div>
                                
                                <div style='background-color: white; border-radius: 8px; padding: 20px; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                                    <h2 style='color: #28a745; margin-top: 0; border-bottom: 2px solid #28a745; padding-bottom: 10px;'>
                                        📋 THÔNG TIN ĐẶT TOUR
                                    </h2>
                                    
                                    <table style='width: 100%; border-collapse: collapse;'>
                                        <tr>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'><strong>Mã đặt tour:</strong></td>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee; color: #28a745; font-weight: bold;'>{$booking['ma_dat_tour']}</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'><strong>Tên tour:</strong></td>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'>{$booking['ten_tour']}</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'><strong>Thời gian:</strong></td>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'>{$booking['so_ngay']} ngày</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'><strong>Ngày khởi hành:</strong></td>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee; color: #e74c3c; font-weight: bold;'>" . date('d/m/Y', strtotime($booking['ngay_khoi_hanh'])) . "</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'><strong>Điểm khởi hành:</strong></td>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'>{$booking['diem_khoi_hanh']}</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'><strong>Số lượng khách:</strong></td>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'>
                                                Người lớn: {$booking['so_nguoi_lon']} | Trẻ em: {$booking['so_tre_em']} | Trẻ nhỏ: {$booking['so_tre_nho']}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'><strong>Trạng thái:</strong></td>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'>
                                                <span style='background-color: #28a745; color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px;'>✅ ĐÃ XÁC NHẬN</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 15px 0; font-size: 18px;'><strong>Tổng tiền:</strong></td>
                                            <td style='padding: 15px 0; font-size: 18px; color: #e74c3c; font-weight: bold;'>" . number_format($booking['tong_tien'], 0, ',', '.') . " đ</td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <div style='background-color: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin: 20px 0; border-radius: 4px;'>
                                    <p style='margin: 0; color: #0c5460; font-size: 14px;'>
                                        <strong>📌 CHUẨN BỊ CHO CHUYẾN ĐI:</strong><br>
                                        • Vui lòng có mặt tại điểm khởi hành trước 30 phút<br>
                                        • Mang theo CMND/CCCD hoặc giấy tờ tùy thân<br>
                                        • Chuẩn bị hành lý theo hướng dẫn của chúng tôi<br>
                                        • Kiểm tra kỹ thông tin và liên hệ nếu có thay đổi
                                    </p>
                                </div>
                                
                                <div style='background-color: #e8f4f8; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                                    <h3 style='color: #17a2b8; margin-top: 0;'>📞 THÔNG TIN LIÊN HỆ</h3>
                                    <p style='margin: 5px 0; color: #333;'><strong>Hotline:</strong> 1900 xxxx</p>
                                    <p style='margin: 5px 0; color: #333;'><strong>Email:</strong> support@windtour.com</p>
                                    <p style='margin: 5px 0; color: #333;'><strong>Website:</strong> www.windtour.com</p>
                                </div>
                                
                                <p style='font-size: 14px; color: #666; line-height: 1.6; margin-top: 20px;'>
                                    Chúng tôi rất mong được phục vụ bạn và hy vọng bạn sẽ có một chuyến đi tuyệt vời!
                                </p>
                                
                                <p style='font-size: 14px; color: #333; margin-top: 30px;'>
                                    Trân trọng,<br>
                                    <strong style='color: #28a745;'>Đội ngũ Wind Tour</strong>
                                </p>
                            </div>
                            
                            <div style='background-color: #333; color: #999; padding: 20px; text-align: center; font-size: 12px;'>
                                <p style='margin: 5px 0;'>© 2024 Wind Tour. All rights reserved.</p>
                                <p style='margin: 5px 0;'>Email này được gửi tự động, vui lòng không trả lời.</p>
                            </div>
                        </div>
                    ";
                    
                    $mail->send();
                    $_SESSION['success'] .= ' Email thông báo đã được gửi đến khách hàng.';
                } catch (Exception $e) {
                    error_log("Mail error: " . $mail->ErrorInfo);
                }
            }
        }
    } else {
        $_SESSION['error'] = "Lỗi khi xác nhận đặt tour!";
    }
    $stmt->close();
}

$conn->close();
header('Location: ../../html/admin/manage_bookings.php');
exit();
?>
