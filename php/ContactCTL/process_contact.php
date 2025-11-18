<?php 
session_start();
include '../../db/db.php';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
    $type  = $_POST['type'];
   
    if(isset($_SESSION['user_id'])){
        $id_user = $_SESSION['user_id'];
        $sql = "SELECT * FROM user WHERE id = $id_user";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $sdt = $row['sdt'];
    }else {
        $id_user = null;
    }

    if(empty($name) || empty($email) || empty($message) || empty($type)){
        $_SESSION['error'] = 1;
        header("Location: ../../html/views/index/contact.php");
        exit();
    }

    if($type!=2){
        $stmt = $conn->prepare("INSERT INTO gop_y (user_id, ho_ten, email, noi_dung, sdt, trang_thai) VALUES (?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("isssi", $id_user, $name, $email, $message, $sdt);
    }else{
        $stmt = $conn->prepare("INSERT INTO khieu_nai (user_id, ho_ten, email, noi_dung, sdt, trang_thai) VALUES (?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("isssi", $id_user, $name, $email, $message, $sdt);
    }

    if($stmt->execute()){
        $_SESSION['success'] = 1;
    } else {
        $_SESSION['error'] = 1;
    }

    $stmt->close();
    $conn->close();

    header("Location: ../../html/views/index/contact.php");
    exit();
}
?>