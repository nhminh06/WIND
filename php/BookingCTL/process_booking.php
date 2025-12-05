<?php
session_start();
include '../../db/db.php';

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
        
        // Tạo thư mục nếu chưa tồn tại
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_tmp = $_FILES['hinh_anh_thanh_toan']['tmp_name'];
        $file_name = $_FILES['hinh_anh_thanh_toan']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Kiểm tra định dạng file
        $allowed_ext = array('jpg', 'jpeg', 'png');
        if (!in_array($file_ext, $allowed_ext)) {
            $_SESSION['booking_error'] = 'Chỉ chấp nhận file ảnh JPG, JPEG, PNG!';
            header('Location: booking.php?id=' . $tour_id);
            exit();
        }
        
        // Kiểm tra kích thước file (5MB)
        if ($_FILES['hinh_anh_thanh_toan']['size'] > 5 * 1024 * 1024) {
            $_SESSION['booking_error'] = 'Kích thước file quá lớn. Tối đa 5MB!';
            header('Location: booking.php?id=' . $tour_id);
            exit();
        }
        
        // Tạo tên file mới
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
        $_SESSION['booking_success'] = 'Đặt tour thành công! Mã đặt tour của bạn là: ' . $ma_dat_tour;
        
        if ($phuong_thuc_thanh_toan === 'chuyen_khoan' && $hinh_anh_thanh_toan) {
            $_SESSION['booking_success'] .= '<br>Biên lai thanh toán của bạn đang chờ xác nhận từ admin.';
        } else {
            $_SESSION['booking_success'] .= '<br>Vui lòng thanh toán khi nhận tour hoặc tại văn phòng công ty.';
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