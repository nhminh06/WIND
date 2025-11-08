<?php
session_start();
include '../../db/db.php';
$id_bl = $_GET['id'];
$user_id = $_SESSION['user_id'];
$so_luot_thich = $_GET['so_luot_thich'];
$khampha_id = $_GET['khampha_id'];

$trang_thai_like = "SELECT * FROM like_binhluan WHERE user_id = $user_id AND binh_luan_id = $id_bl";
$kq_trang_thai = mysqli_query($conn, $trang_thai_like);

if(mysqli_num_rows($kq_trang_thai) > 0) {
    $row = mysqli_fetch_assoc($kq_trang_thai);
    if($row['thich'] == 1) {
        $giam_like = "UPDATE binh_luan SET so_luot_thich = so_luot_thich - 1 WHERE id = $id_bl";
        mysqli_query($conn, $giam_like);
        $mau_like = "UPDATE like_binhluan SET thich = 0 WHERE user_id = $user_id AND binh_luan_id = $id_bl";
        mysqli_query($conn, $mau_like);
    } else {
        $tang_like = "UPDATE binh_luan SET so_luot_thich = so_luot_thich + 1 WHERE id = $id_bl";
        mysqli_query($conn, $tang_like);
        $mau_like = "UPDATE like_binhluan SET thich = 1 WHERE user_id = $user_id AND binh_luan_id = $id_bl";
        mysqli_query($conn, $mau_like);
    }
} else {
    $them_like = "INSERT INTO like_binhluan (user_id, binh_luan_id, thich) VALUES ($user_id, $id_bl, 1)";
    mysqli_query($conn, $them_like);
    $tang_like = "UPDATE binh_luan SET so_luot_thich = so_luot_thich + 1 WHERE id = $id_bl";
    mysqli_query($conn, $tang_like);
}

header("Location: ../../html/views/index/detailed_explore.php?id=$khampha_id");
exit();
?>
