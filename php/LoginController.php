<?php
session_start();
include '../db/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $stmt = $conn->prepare("SELECT * FROM user WHERE ho_ten = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();


    if (!$user) {
        $_SESSION['error'] = "Tên đăng nhập hoặc mật khẩu không đúng.";
        header("Location: ../html/views/index/login.php");
        exit();
    }

  
    if ($user['trang_thai'] == 0) {
        $_SESSION['error'] = 1;
        header("Location: ../html/views/index/contact.php");
        exit();
    }

    if (!isset($_SESSION['turn'])) {
        $_SESSION['turn'] = 0;
    }


    if (!password_verify($password, $user['password'])) {

 
        $_SESSION['turn']++;

     
        if ($_SESSION['turn'] >= 3) {
            $update = $conn->prepare("UPDATE user SET trang_thai = 0 WHERE ho_ten = ?");
            $update->bind_param("s", $username);
            $update->execute();

            $_SESSION['error'] = 1;
            session_unset();
            header("Location: ../html/views/index/contact.php");
            exit();
        }

 
        $_SESSION['error'] = "Tên đăng nhập hoặc mật khẩu không đúng.";
        header("Location: ../html/views/index/login.php");
        exit();
    }

    
    $_SESSION['turn'] = 0;

    session_regenerate_id(true);

    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['ho_ten'];
    $_SESSION['role']     = $user['role'];
    $_SESSION['avatar']   = $user['avatar'];

    if ($user['role'] == 'admin') {
        $_SESSION['rank'] = $user['rank'];
    }

    header("Location: ../html/views/index/WebIndex.php");
    exit();
}
?>
