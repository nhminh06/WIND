<?php 
include '../../db/db.php';

// Xóa bản ghi
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM banner WHERE id = $id";
    mysqli_query($conn, $sql);
    header("Location: ../../html/Admin/IndexController.php");
    exit();
}
?>