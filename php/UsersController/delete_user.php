<?php
session_start();
include '../../db/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../html/views/index/Webindex.php');
    exit();
}

// Kiểm tra có ID không
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "ID người dùng không hợp lệ!";
    header('Location: ../../html/Admin/UserController.php');
    exit();
}

$id = intval($_GET['id']);

// Kiểm tra user có tồn tại không
$check_sql = "SELECT ho_ten FROM user WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Người dùng không tồn tại!";
    header('Location: ../../html/Admin/UserController.php');
    exit();
}

$user = $result->fetch_assoc();
$ho_ten = $user['ho_ten'];
$check_stmt->close();

// Không cho phép xóa chính mình
if ($id == $_SESSION['id']) {
    $_SESSION['error'] = "Không thể xóa tài khoản của chính bạn!";
    header('Location: ../../html/Admin/UserController.php');
    exit();
}

// Xóa user
$delete_sql = "DELETE FROM user WHERE id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $id);

if ($delete_stmt->execute()) {
    $_SESSION['success'] = "Đã xóa người dùng <strong>" . htmlspecialchars($ho_ten) . "</strong> thành công!";
} else {
    $_SESSION['error'] = "Có lỗi xảy ra khi xóa người dùng: " . $conn->error;
}

$delete_stmt->close();
$conn->close();

header('Location: ../../html/Admin/UserController.php');
exit();
?>