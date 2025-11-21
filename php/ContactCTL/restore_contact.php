<?php
session_start();
include '../../db/db.php';

$id = $_GET['id'] ?? null;
$table = $_GET['table'] ?? '';
$allowed_tables = ['khieu_nai', 'gop_y'];

if ($id && in_array($table, $allowed_tables)) {
    // Lấy trạng thái hiện tại
    $stmt = $conn->prepare("SELECT trang_thai FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if($row) {
        if($row['trang_thai'] == 2) {
            // Khôi phục → đổi về 0
            $stmt_update = $conn->prepare("UPDATE $table SET trang_thai = 0 WHERE id = ?");
            $stmt_update->bind_param("i", $id);

            if($stmt_update->execute()) {
                $_SESSION['success'] = "Khôi phục thành công!";
            } else {
                $_SESSION['error'] = "Khôi phục thất bại!";
            }
            $stmt_update->close();
        } else {
            $_SESSION['error'] = "Mục này chưa lưu trữ, không cần khôi phục!";
        }
    } else {
        $_SESSION['error'] = "Không tìm thấy mục!";
    }

    $stmt->close();

} else {
    $_SESSION['error'] = "Dữ liệu không hợp lệ!";
}

$conn->close();
header("Location: ../../html/Admin/storage.php");
exit();
?>
