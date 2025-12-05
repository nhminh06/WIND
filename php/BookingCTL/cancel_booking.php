<?php
session_start();
include '../../db/db.php';

// Kiểm tra quyền admin hoặc staff
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    header('Location: ../../index.php');
    exit();
}

// Lấy ID đặt tour
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($booking_id > 0) {
    $sql = "UPDATE dat_tour SET trang_thai = 'cancelled' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Đã hủy đặt tour thành công!";
    } else {
        $_SESSION['error'] = "Lỗi khi hủy đặt tour!";
    }
    $stmt->close();
}

// Chuyển về trang quản lý bookings (giống confirm_booking.php)
header('Location: ../../html/admin/manage_bookings.php');
exit();
?>
