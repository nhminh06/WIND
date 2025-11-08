<?php
session_start();
include '../db/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $layten = "SELECT * FROM user WHERE ho_ten = '$username'";
    $result_layten = $conn->query($layten);
    $user_data = $result_layten->fetch_assoc();
    if (!isset($_SESSION['turn'])) {
        $_SESSION['turn'] = 0;
    }
       if($user['password'] !== $password){
            $_SESSION['turn'] += 1;
            if($_SESSION['turn'] >= 3){
                $updateStatusSql = "UPDATE user SET trang_thai = 0 WHERE ho_ten = ?";
                $stmt = $conn->prepare($updateStatusSql);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                   if ($user_data['trang_thai'] == 0) {
            $_SESSION['error'] = 1;    
             unset($_SESSION['role']);   
             unset($_SESSION['user_id']);
             unset($_SESSION['username']); 
             unset($_SESSION['turn']);
            header("Location: ../html/views/index/contact.php");
            exit();
        }
            }
          

        }

    // Lấy user theo username và password
    $sql = "SELECT * FROM user WHERE ho_ten = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        session_unset();  
        session_regenerate_id(true); 

        $_SESSION['user_id'] = $user['id'];  
        $_SESSION['username'] = $user['ho_ten'];  
        $_SESSION['role'] = $user['role'];


         


        if ($user['trang_thai'] == 0) {
            $_SESSION['error'] = 1;    
             unset($_SESSION['role']);   
             unset($_SESSION['user_id']);
             unset($_SESSION['username']); 
            header("Location: ../html/views/index/contact.php");
            exit();
        }

        header("Location: ../html/views/index/WebIndex.php");
    } else {
        $_SESSION['error'] = "Tên đăng nhập hoặc mật khẩu không đúng.";
        header("Location: ../html/views/index/login.php");
        exit();
    }
}
?>
