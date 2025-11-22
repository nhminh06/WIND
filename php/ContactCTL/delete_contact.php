<?php
session_start();
include '../../db/db.php';

$id = $_GET['id'] ?? null;
$table = $_GET['table'] ?? '';
$from = $_GET['from'] ?? 'contact'; // Mặc định là contact

$allowed_tables = ['khieu_nai', 'gop_y'];

if ($id && in_array($table, $allowed_tables)) {
    $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Xóa vĩnh viễn thành công!";
    } else {
        $_SESSION['error'] = "Xóa thất bại!";
    }

    $stmt->close();
} else {
    $_SESSION['error'] = "Dữ liệu không hợp lệ!";
}

$conn->close();


if ($from == 'storage') {
    header("Location: ../../html/Admin/storage.php");
} else {
    header("Location: ../../html/Admin/ContactController.php");
}
exit();
?>