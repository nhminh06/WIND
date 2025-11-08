<?php
include '../../db/db.php';
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // Cập nhật trạng thái người dùng thành bị khóa (0)
    $update_sql = "UPDATE user SET trang_thai = 1 WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $user_id);
    header("Location: ../../html/Admin/UserController.php");
    if ($update_stmt->execute()) {
        $_SESSION['success'] = "Đã khóa tài khoản người dùng thành công!";
    } else {
        $_SESSION['error'] = "Lỗi khi khóa tài khoản người dùng.";
    }
    header("Location: ../../html/Admin/UserController.php");
} else {
    $_SESSION['error'] = "ID người dùng không hợp lệ.";
}
?>