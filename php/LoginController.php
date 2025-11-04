<?php
session_start();
include '../db/db.php';

if($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Truyền thống: lấy user theo username
    $sql = "SELECT * FROM user WHERE ho_ten = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();

        // Gán role từ DB
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['ho_ten'];
        
        // Điều hướng theo role
        if ($user['role'] == 'admin') {
            header("Location: ../html/Admin/ContactController.php");
            exit();
        } elseif ($user['role'] == 'staff') {
            header("Location: ../html/staff/StaffProfile.php");
            exit();
        } else {
            header("Location: ../html/views/index/WebIndex.php");
            exit();
        }

    } else {
        $_SESSION['error'] = "Tên đăng nhập hoặc mật khẩu không đúng.";
        header("Location: ../html/views/index/login.php");
        exit();
    }
}
?>
