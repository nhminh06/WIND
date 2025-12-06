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

// Lấy thông tin booking trước khi hủy
$sql_get = "SELECT dt.*, t.ten_tour, t.so_ngay, tc.diem_khoi_hanh 
            FROM dat_tour dt
            LEFT JOIN tour t ON dt.tour_id = t.id
            LEFT JOIN tour_chi_tiet tc ON t.id = tc.tour_id
            WHERE dt.id = ?";
$stmt_get = $conn->prepare($sql_get);
$stmt_get->bind_param("i", $booking_id);
$stmt_get->execute();
$result = $stmt_get->get_result();
$booking = $result->fetch_assoc();
$stmt_get->close();

if ($booking_id > 0 && $booking) {
    $sql = "UPDATE dat_tour SET trang_thai = 'cancelled' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Đã hủy đặt tour thành công!";
        
        // Gửi email thông báo hủy
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
                    $mail->Subject = '❌ Thông báo hủy đặt tour - Wind Tour';
                    
                    $mail->Body = "
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 10px; overflow: hidden;'>
                            <div style='background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 30px; text-align: center;'>
                                <h1 style='margin: 0; font-size: 28px;'>❌ THÔNG BÁO HỦY ĐẶT TOUR</h1>
                            </div>
                            
                            <div style='padding: 30px; background-color: #f9f9f9;'>
                                <p style='font-size: 16px; color: #333;'>Xin chào <strong>{$booking['ho_ten']}</strong>,</p>
                                <p style='font-size: 14px; color: #666; line-height: 1.6;'>
                                    Chúng tôi rất tiếc phải thông báo rằng đơn đặt tour của bạn đã bị <strong style='color: #dc3545;'>HỦY</strong>.
                                </p>
                                
                                <div style='background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); border-left: 4px solid #dc3545; padding: 20px; margin: 20px 0; border-radius: 4px;'>
                                    <p style='margin: 0; color: #721c24; font-size: 16px; font-weight: bold;'>
                                        ⚠️ Đơn đặt tour của bạn đã bị hủy
                                    </p>
                                </div>
                                
                                <div style='background-color: white; border-radius: 8px; padding: 20px; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                                    <h2 style='color: #dc3545; margin-top: 0; border-bottom: 2px solid #dc3545; padding-bottom: 10px;'>
                                        📋 THÔNG TIN ĐẶT TOUR BỊ HỦY
                                    </h2>
                                    
                                    <table style='width: 100%; border-collapse: collapse;'>
                                        <tr>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'><strong>Mã đặt tour:</strong></td>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee; color: #dc3545; font-weight: bold;'>{$booking['ma_dat_tour']}</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'><strong>Tên tour:</strong></td>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'>{$booking['ten_tour']}</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'><strong>Ngày khởi hành:</strong></td>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'>" . date('d/m/Y', strtotime($booking['ngay_khoi_hanh'])) . "</td>
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
                                                <span style='background-color: #dc3545; color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px;'>❌ ĐÃ HỦY</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 15px 0; font-size: 18px;'><strong>Số tiền đã thanh toán:</strong></td>
                                            <td style='padding: 15px 0; font-size: 18px; color: #e74c3c; font-weight: bold;'>" . number_format($booking['tong_tien'], 0, ',', '.') . " đ</td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <div style='background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;'>
                                    <p style='margin: 0; color: #856404; font-size: 14px;'>
                                        <strong>💰 VỀ VIỆC HOÀN TIỀN:</strong><br>
                                        Nếu bạn đã thanh toán, chúng tôi sẽ tiến hành hoàn tiền theo chính sách của công ty. 
                                        Vui lòng liên hệ với bộ phận chăm sóc khách hàng để được hỗ trợ.
                                    </p>
                                </div>
                                
                                <div style='background-color: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin: 20px 0; border-radius: 4px;'>
                                    <p style='margin: 0; color: #0c5460; font-size: 14px;'>
                                        <strong>📌 LÝ DO HỦY TOUR:</strong><br>
                                        Có thể do một số lý do như: thiếu số lượng khách tham gia, thời tiết xấu, 
                                        hoặc các lý do bất khả kháng khác. Chúng tôi xin lỗi vì sự bất tiện này.
                                    </p>
                                </div>
                                
                                <div style='background-color: #e8f4f8; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                                    <h3 style='color: #17a2b8; margin-top: 0;'>📞 LIÊN HỆ HỖ TRỢ</h3>
                                    <p style='margin: 5px 0; color: #333;'>Nếu bạn có bất kỳ thắc mắc nào, vui lòng liên hệ:</p>
                                    <p style='margin: 5px 0; color: #333;'><strong>Hotline:</strong> 1900 xxxx</p>
                                    <p style='margin: 5px 0; color: #333;'><strong>Email:</strong> support@windtour.com</p>
                                    <p style='margin: 5px 0; color: #333;'><strong>Website:</strong> www.windtour.com</p>
                                </div>
                                
                                <p style='font-size: 14px; color: #666; line-height: 1.6; margin-top: 20px;'>
                                    Chúng tôi rất tiếc về sự bất tiện này và hy vọng sẽ được phục vụ bạn trong những chuyến đi tiếp theo.
                                </p>
                                
                                <p style='font-size: 14px; color: #333; margin-top: 30px;'>
                                    Trân trọng,<br>
                                    <strong style='color: #dc3545;'>Đội ngũ Wind Tour</strong>
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
        $_SESSION['error'] = "Lỗi khi hủy đặt tour!";
    }
    $stmt->close();
}

$conn->close();
header('Location: ../../html/admin/manage_bookings.php');
exit();
?>