<?php session_start(); ?>
<?php include '../db/db.php'; ?>
<?php
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = 'admin';

    $sql = "SELECT * FROM user WHERE ho_ten = '$username' AND password = '$password'";
    $result = $conn->query($sql);
   
    if ($result->num_rows > 0) {

      $user = $result->fetch_assoc();
if($user['role'] === 'admin'){
    $_SESSION['role'] = 'admin';
}else{
    $_SESSION['role'] = 'user';
}



        $_SESSION['username'] = $username;
        echo "<script>window.location.href = '../html/views/index/WebIndex.php';</script>";
    } else {
        $_SESSION['error'] = "Tên đăng nhập hoặc mật khẩu không đúng.";
         header("Location: ../html/views/index/login.php");
         exit();
    }
    
}

?>