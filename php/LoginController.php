<?php session_start(); ?>
<?php include '../db/db.php'; ?>
<?php
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE name = '$username' AND password = '$password'";
    $result = $conn->query($sql);
   
    if ($result->num_rows > 0) {
        $_SESSION['username'] = $username;
        echo "<script>window.location.href = '../html/views/WebIndex.php';</script>";
    } else {
        $_SESSION['error'] = "Tên đăng nhập hoặc mật khẩu không đúng.";
         header("Location: ../html/views/login.php");
         exit();
    }
    
}

?>