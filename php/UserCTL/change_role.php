<?php
session_start();
include '../../db/db.php';

// Kiểm tra xem có dữ liệu POST không
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Nhận dữ liệu từ form
    $user_id = intval($_GET['id']);
    $new_role = trim($_GET['role']);

    // Kiểm tra đầu vào hợp lệ
    if ($user_id <= 0 || empty($new_role)) {
        $_SESSION['error'] = "Dữ liệu không hợp lệ!";
        header("Location: ../../html/Admin/UserController.php");
        exit();
    }

    // // Kiểm tra người dùng có tồn tại không
    // $check_sql = "SELECT id FROM user WHERE id = ?";
    // $check_stmt = $conn->prepare($check_sql);
    // $check_stmt->bind_param("i", $user_id);
    // $check_stmt->execute();
    // $check_result = $check_stmt->get_result();

    // if ($check_result->num_rows === 0) {
    //     $_SESSION['error'] = "Không tìm thấy người dùng.";
    //     header("Location: ../../html/Admin/UserController.php");
    //     exit();
    // }

    // Cập nhật vai trò
    $update_sql = "UPDATE user SET role = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $new_role, $user_id);

    if ($update_stmt->execute()) {
        $_SESSION['success'] = "Đã cập nhật quyền người dùng thành công!";
    } else {
        $_SESSION['error'] = "Lỗi khi cập nhật quyền người dùng.";
    }

    header("Location: ../../html/Admin/UserController.php");
    exit();
} else {
    // Nếu không phải phương thức POST → không cho truy cập trực tiếp
    $_SESSION['error'] = "Truy cập không hợp lệ.";
    header("Location: ../../html/Admin/UserController.php");
    exit();
}
?>
