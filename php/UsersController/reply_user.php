<?php
session_start();
include '../../db/db.php';
$user_id = $_SESSION['reply_user'] ?? null;
$loai = 'user';
$lien_he_id = $_POST['lien_he_id'];
$noi_dung = $_POST['noi_dung'];
unset($_SESSION['reply_user']);

$sql = "INSERT INTO phan_hoi (loai, lien_he_id, user_id, noi_dung) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("siis", $loai, $lien_he_id, $user_id, $noi_dung);

if ($stmt->execute()) {
    header("Location: ../../html/Admin/UserController.php");
} else {
    echo "Lỗi hệ thống!";
}

$stmt->close();
$conn->close();
?>
