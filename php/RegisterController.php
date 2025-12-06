<?php 
session_start();
include '../db/db.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $USERNAME = $_POST['username'];
    $EMAIL = $_POST['email'];
    $PASSWORD = $_POST['password'];
    $role = 'user';

    $hashedPassword = password_hash($PASSWORD, PASSWORD_DEFAULT);
    $sql = "SELECT * FROM user WHERE ho_ten = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $USERNAME, $EMAIL);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Tên đăng nhập hoặc email đã tồn tại.";
        header("Location: ../html/views/index/register.php");
        exit();
    }
   
    $stmt = $conn->prepare("INSERT INTO user (ho_ten, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $USERNAME, $EMAIL, $hashedPassword, $role);

    if ($stmt->execute()) {
        $_SESSION['username'] = $USERNAME;
        header("Location: ../html/views/index/Webindex.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
