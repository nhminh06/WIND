<?php
session_start();
include '../../db/db.php';

// Kiểm tra quyền admin hoặc staff
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    header('Location: ../../index.php');
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "ID đặt tour không hợp lệ.";
    header("Location: ../../html/Admin/manage_bookings.php");
    exit();
}

$bookingId = (int)$_GET['id'];

// Chuẩn bị xóa đặt tour
$stmt = $conn->prepare("DELETE FROM dat_tour WHERE id = ?");
$stmt->bind_param("i", $bookingId);

if ($stmt->execute()) {
    $_SESSION['success'] = "Xóa đặt tour thành công!";
} else {
    $_SESSION['error'] = "Xóa đặt tour thất bại. Vui lòng thử lại.";
}

$stmt->close();
$conn->close();

// Quay lại trang quản lý
header("Location: ../../html/Admin/manage_bookings.php");
exit();
?>
