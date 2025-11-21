<?php
session_start();
include '../../db/db.php';

$id = $_GET['id'] ?? null;
$table = $_GET['table'] ?? '';

$allowed_tables = ['khieu_nai', 'gop_y'];

if ($id && in_array($table, $allowed_tables)) {
    $stmt = $conn->prepare("UPDATE $table SET trang_thai = IF(trang_thai = 0, 1, 0) WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Cập nhật trạng thái thành công!";
    } else {
        $_SESSION['error'] = "Cập nhật thất bại";
    }

    $stmt->close();
} else {
    $_SESSION['error'] = "Dữ liệu không hợp lệ";
}

$conn->close();
header("Location: ../../html/Admin/contactcontroller.php");
exit();
?>