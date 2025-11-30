<?php
session_start();
include '../../../db/db.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    $_SESSION['booking_error'] = "Vui lòng đăng nhập để đặt tour!";
    header('Location: ../../../pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy và làm sạch dữ liệu
    $tour_id = intval($_POST['tour_id']);
    
    // Lấy user_id từ session (an toàn hơn)
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    
    // Kiểm tra user_id có hợp lệ không
    if ($user_id <= 0) {
        $_SESSION['booking_error'] = "Không tìm thấy thông tin người dùng. Vui lòng đăng nhập lại!";
        header('Location: ../../../pages/login.php');
        exit();
    }
    
    $ho_ten = mysqli_real_escape_string($conn, trim($_POST['ho_ten']));
    $sdt = mysqli_real_escape_string($conn, trim($_POST['sdt']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $dia_chi = mysqli_real_escape_string($conn, trim($_POST['dia_chi']));
    $ngay_khoi_hanh = mysqli_real_escape_string($conn, $_POST['ngay_khoi_hanh']);
    $so_nguoi_lon = intval($_POST['so_nguoi_lon']);
    $so_tre_em = intval($_POST['so_tre_em']);
    $so_tre_nho = intval($_POST['so_tre_nho']);
    $ghi_chu = mysqli_real_escape_string($conn, trim($_POST['ghi_chu']));
    $tong_tien = intval($_POST['tong_tien']);
    
    // Validation
    if ($so_nguoi_lon < 1) {
        $_SESSION['booking_error'] = "Phải có ít nhất 1 người lớn!";
        header('Location: booking_form.php?id=' . $tour_id);
        exit();
    }
    
    // Tạo mã đặt tour unique
    $ma_dat_tour = 'DT' . date('Ymd') . strtoupper(substr(md5(uniqid()), 0, 6));
    
    // Insert vào database (thêm cột user_id)
    $sql = "INSERT INTO dat_tour (
        ma_dat_tour, tour_id, user_id, ho_ten, sdt, email, dia_chi, 
        ngay_khoi_hanh, so_nguoi_lon, so_tre_em, so_tre_nho, 
        tong_tien, ghi_chu, ngay_dat, trang_thai
    ) VALUES (
        '$ma_dat_tour', $tour_id, $user_id, '$ho_ten', '$sdt', '$email', '$dia_chi',
        '$ngay_khoi_hanh', $so_nguoi_lon, $so_tre_em, $so_tre_nho,
        $tong_tien, '$ghi_chu', NOW(), 'pending'
    )";
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['booking_success'] = true;
        $_SESSION['ma_dat_tour'] = $ma_dat_tour;
        $_SESSION['customer_name'] = $ho_ten;
        $_SESSION['customer_email'] = $email;
        
        // Có thể gửi email thông báo ở đây
        
        header('Location: booking_success.php?code=' . $ma_dat_tour . '&tour_id=' . $tour_id);
        exit();
    } else {
        $_SESSION['booking_error'] = "Có lỗi xảy ra: " . mysqli_error($conn);
        header('Location: booking_form.php?id=' . $tour_id);
        exit();
    }
} else {
    header('Location: ../../../index.php');
    exit();
}
?>