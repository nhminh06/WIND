<?php
session_start();
include '../../db/db.php';

// Kiểm tra quyền admin hoặc staff
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    header('Location: ../../index.php');
    exit();
}

// Lấy ID đặt tour từ URL
$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($booking_id <= 0) {
    header('Location: manage_bookings.php');
    exit();
}

// Xử lý xác nhận thanh toán
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_status'])) {
    $new_status = $_POST['payment_status'];
    $payment_date = !empty($_POST['payment_date']) ? $_POST['payment_date'] : null;

    $update_sql = "UPDATE dat_tour SET trang_thai_thanh_toan = ?, ngay_thanh_toan = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('ssi', $new_status, $payment_date, $booking_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Cập nhật trạng thái thanh toán thành công.';
    } else {
        $_SESSION['error'] = 'Lỗi khi cập nhật trạng thái thanh toán.';
    }
    $stmt->close();

    // Reload trang để hiển thị cập nhật
    header("Location: ../../html/Admin/manage_bookings.php");
    exit();
} ?>