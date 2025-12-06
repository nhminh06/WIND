<?php
session_start();
include '../../db/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    $_SESSION['booking_error'] = 'Vui lòng đăng nhập!';
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = intval($_POST['user_id']);
    $tour_id = intval($_POST['tour_id']);
    $ho_ten = mysqli_real_escape_string($conn, $_POST['ho_ten']);
    $sdt = mysqli_real_escape_string($conn, $_POST['sdt']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $dia_chi = mysqli_real_escape_string($conn, $_POST['dia_chi'] ?? '');
    $ngay_khoi_hanh = mysqli_real_escape_string($conn, $_POST['ngay_khoi_hanh']);
    $so_nguoi_lon = intval($_POST['so_nguoi_lon']);
    $so_tre_em = intval($_POST['so_tre_em']);
    $so_tre_nho = intval($_POST['so_tre_nho']);
    $tong_tien = floatval($_POST['tong_tien']);
    $ghi_chu = mysqli_real_escape_string($conn, $_POST['ghi_chu'] ?? '');
    $phuong_thuc_thanh_toan = mysqli_real_escape_string($conn, $_POST['phuong_thuc_thanh_toan']);
    
    // Tạo mã đặt tour
    $ma_dat_tour = 'DT' . date('YmdHis') . rand(100, 999);
    
    // Xử lý upload ảnh thanh toán
    $hinh_anh_thanh_toan = null;
    $trang_thai_thanh_toan = 'cho_xac_nhan';
    
    if ($phuong_thuc_thanh_toan === 'chuyen_khoan' && isset($_FILES['hinh_anh_thanh_toan']) && $_FILES['hinh_anh_thanh_toan']['error'] === 0) {
        $upload_dir = '../../../uploads/payment/';
        
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_tmp = $_FILES['hinh_anh_thanh_toan']['tmp_name'];
        $file_name = $_FILES['hinh_anh_thanh_toan']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_ext = array('jpg', 'jpeg', 'png');
        if (!in_array($file_ext, $allowed_ext)) {
            $_SESSION['booking_error'] = 'Chỉ chấp nhận file ảnh JPG, JPEG, PNG!';
            header('Location: booking.php?id=' . $tour_id);
            exit();
        }
        
        if ($_FILES['hinh_anh_thanh_toan']['size'] > 5 * 1024 * 1024) {
            $_SESSION['booking_error'] = 'Kích thước file quá lớn. Tối đa 5MB!';
            header('Location: booking.php?id=' . $tour_id);
            exit();
        }
        
        $new_file_name = 'payment_' . $ma_dat_tour . '.' . $file_ext;
        $file_path = $upload_dir . $new_file_name;
        
        if (move_uploaded_file($file_tmp, $file_path)) {
            $hinh_anh_thanh_toan = 'payment/' . $new_file_name;
        } else {
            $_SESSION['booking_error'] = 'Lỗi khi tải lên ảnh thanh toán!';
            header('Location: booking.php?id=' . $tour_id);
            exit();
        }
    } elseif ($phuong_thuc_thanh_toan === 'tien_mat') {
        $trang_thai_thanh_toan = 'cho_xac_nhan';
    }
    
    // Thêm vào database
    $sql = "INSERT INTO dat_tour (
        ma_dat_tour, 
        user_id, 
        tour_id, 
        ho_ten, 
        sdt, 
        email, 
        dia_chi, 
        ngay_khoi_hanh, 
        so_nguoi_lon, 
        so_tre_em, 
        so_tre_nho, 
        tong_tien, 
        ghi_chu, 
        trang_thai,
        phuong_thuc_thanh_toan,
        hinh_anh_thanh_toan,
        ngay_thanh_toan,
        trang_thai_thanh_toan
    ) VALUES (
        '$ma_dat_tour',
        $user_id,
        $tour_id,
        '$ho_ten',
        '$sdt',
        '$email',
        '$dia_chi',
        '$ngay_khoi_hanh',
        $so_nguoi_lon,
        $so_tre_em,
        $so_tre_nho,
        $tong_tien,
        '$ghi_chu',
        'pending',
        '$phuong_thuc_thanh_toan',
        " . ($hinh_anh_thanh_toan ? "'$hinh_anh_thanh_toan'" : "NULL") . ",
        " . ($hinh_anh_thanh_toan ? "NOW()" : "NULL") . ",
        '$trang_thai_thanh_toan'
    )";
    
    if (mysqli_query($conn, $sql)) {
        // Lấy thông tin tour để gửi email
        $sql_tour = "SELECT t.ten_tour, t.so_ngay, t.gia, tc.diem_khoi_hanh 
                     FROM tour t 
                     LEFT JOIN tour_chi_tiet tc ON t.id = tc.tour_id 
                     WHERE t.id = $tour_id";
        $result_tour = mysqli_query($conn, $sql_tour);
        $tour_info = mysqli_fetch_assoc($result_tour);
        
        // Gửi email thông báo
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $domain = substr(strrchr($email, "@"), 1);
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
                    $mail->addAddress($email, $ho_ten);
                    $mail->isHTML(true);
                    $mail->Subject = 'Xác nhận đặt tour - Wind Tour';
                    
                    // Nội dung email
                    $phuong_thuc_text = ($phuong_thuc_thanh_toan == 'chuyen_khoan') ? 'Chuyển khoản ngân hàng' : 'Thanh toán tiền mặt';
                    $trang_thai_tt_text = ($phuong_thuc_thanh_toan == 'chuyen_khoan') ? 
                        'Đang chờ xác nhận từ admin' : 
                        'Thanh toán khi nhận tour hoặc tại văn phòng';
                    
                    $mail->Body = "
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 10px; overflow: hidden;'>
                            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center;'>
                                <h1 style='margin: 0; font-size: 28px;'>🎉 ĐẶT TOUR THÀNH CÔNG!</h1>
                            </div>
                            
                            <div style='padding: 30px; background-color: #f9f9f9;'>
                                <p style='font-size: 16px; color: #333;'>Xin chào <strong>{$ho_ten}</strong>,</p>
                                <p style='font-size: 14px; color: #666; line-height: 1.6;'>
                                    Cảm ơn bạn đã tin tưởng và đặt tour tại <strong>Wind Tour</strong>. 
                                    Chúng tôi đã nhận được yêu cầu đặt tour của bạn.
                                </p>
                                
                                <div style='background-color: white; border-radius: 8px; padding: 20px; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                                    <h2 style='color: #667eea; margin-top: 0; border-bottom: 2px solid #667eea; padding-bottom: 10px;'>
                                        📋 THÔNG TIN ĐẶT TOUR
                                    </h2>
                                    
                                    <table style='width: 100%; border-collapse: collapse;'>
                                        <tr>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'><strong>Mã đặt tour:</strong></td>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee; color: #667eea; font-weight: bold;'>{$ma_dat_tour}</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'><strong>Tên tour:</strong></td>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'>{$tour_info['ten_tour']}</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'><strong>Thời gian:</strong></td>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'>{$tour_info['so_ngay']} ngày</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'><strong>Ngày khởi hành:</strong></td>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'>" . date('d/m/Y', strtotime($ngay_khoi_hanh)) . "</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'><strong>Điểm khởi hành:</strong></td>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'>{$tour_info['diem_khoi_hanh']}</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'><strong>Số lượng khách:</strong></td>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'>
                                                Người lớn: {$so_nguoi_lon} | Trẻ em: {$so_tre_em} | Trẻ nhỏ: {$so_tre_nho}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'><strong>Phương thức thanh toán:</strong></td>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'>{$phuong_thuc_text}</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee;'><strong>Trạng thái thanh toán:</strong></td>
                                            <td style='padding: 10px 0; border-bottom: 1px solid #eee; color: #f39c12;'>{$trang_thai_tt_text}</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 15px 0; font-size: 18px;'><strong>Tổng tiền:</strong></td>
                                            <td style='padding: 15px 0; font-size: 18px; color: #e74c3c; font-weight: bold;'>" . number_format($tong_tien, 0, ',', '.') . " đ</td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <div style='background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;'>
                                    <p style='margin: 0; color: #856404; font-size: 14px;'>
                                        <strong>⚠️ Lưu ý:</strong><br>
                                        " . ($phuong_thuc_thanh_toan == 'chuyen_khoan' ? 
                                            'Chúng tôi đã nhận được biên lai chuyển khoản của bạn. Admin sẽ xác nhận trong vòng 24h.' : 
                                            'Vui lòng thanh toán khi nhận tour hoặc đến văn phòng công ty trước ngày khởi hành.') . "
                                    </p>
                                </div>
                                
                                <div style='text-align: center; margin: 30px 0;'>
                                    <a href='http://yourwebsite.com/booking_detail.php?code={$ma_dat_tour}' 
                                       style='display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                                              color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; 
                                              font-weight: bold; font-size: 14px;'>
                                        Xem chi tiết đơn đặt tour
                                    </a>
                                </div>
                                
                                <div style='background-color: #e8f4f8; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                                    <h3 style='color: #17a2b8; margin-top: 0;'>📞 THÔNG TIN LIÊN HỆ</h3>
                                    <p style='margin: 5px 0; color: #333;'><strong>Hotline:</strong> 1900 xxxx</p>
                                    <p style='margin: 5px 0; color: #333;'><strong>Email:</strong> support@windtour.com</p>
                                    <p style='margin: 5px 0; color: #333;'><strong>Website:</strong> www.windtour.com</p>
                                    <p style='margin: 5px 0; color: #333;'><strong>Địa chỉ:</strong> 123 Đường ABC, TP. HCM</p>
                                </div>
                                
                                <p style='font-size: 14px; color: #666; line-height: 1.6; margin-top: 20px;'>
                                    Nếu bạn có bất kỳ thắc mắc nào, vui lòng liên hệ với chúng tôi qua hotline hoặc email.
                                </p>
                                
                                <p style='font-size: 14px; color: #333; margin-top: 30px;'>
                                    Trân trọng,<br>
                                    <strong style='color: #667eea;'>Đội ngũ Wind Tour</strong>
                                </p>
                            </div>
                            
                            <div style='background-color: #333; color: #999; padding: 20px; text-align: center; font-size: 12px;'>
                                <p style='margin: 5px 0;'>© 2024 Wind Tour. All rights reserved.</p>
                                <p style='margin: 5px 0;'>Email này được gửi tự động, vui lòng không trả lời.</p>
                            </div>
                        </div>
                    ";
                    
                    $mail->send();
                    $_SESSION['email_sent'] = true;
                } catch (Exception $e) {
                    error_log("Mail error: " . $mail->ErrorInfo);
                    $_SESSION['email_sent'] = false;
                    $_SESSION['email_error'] = $mail->ErrorInfo;
                }
            }
        }
        
        $_SESSION['booking_success'] = 'Đặt tour thành công! Mã đặt tour của bạn là: ' . $ma_dat_tour;
        
        if ($phuong_thuc_thanh_toan === 'chuyen_khoan' && $hinh_anh_thanh_toan) {
            $_SESSION['booking_success'] .= '<br>Biên lai thanh toán của bạn đang chờ xác nhận từ admin.';
        } else {
            $_SESSION['booking_success'] .= '<br>Vui lòng thanh toán khi nhận tour hoặc tại văn phòng công ty.';
        }
        
        if (isset($_SESSION['email_sent']) && $_SESSION['email_sent']) {
            $_SESSION['booking_success'] .= '<br>📧 Email xác nhận đã được gửi đến: ' . $email;
        }
        
        header('Location: ../../html/views/index/booking_success.php?code=' . $ma_dat_tour);
    } else {
        $_SESSION['booking_error'] = 'Lỗi: ' . mysqli_error($conn);
        header('Location: ../../../html/views/index/booking.php?id=' . $tour_id);
    }
    
    mysqli_close($conn);
} else {
    header('Location: ../../../html/views/index/Webindex.php');
}
?>