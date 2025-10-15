<?php
include '../../db/db.php';

if (isset($_GET['id'])) {
    $tour_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Xóa các bảng liên quan theo thứ tự
    mysqli_query($conn, "DELETE FROM danh_gia WHERE tour_id = '$tour_id'");
    mysqli_query($conn, "DELETE FROM tour WHERE id = '$tour_id'");
    mysqli_query($conn, "DELETE FROM lich_trinh WHERE tour_id = '$tour_id'");
    mysqli_query($conn, "DELETE FROM tour_anh WHERE tour_id = '$tour_id'");
    mysqli_query($conn, "DELETE FROM dich_vu WHERE tour_id = '$tour_id'");
    mysqli_query($conn, "DELETE FROM trai_nghiem WHERE tour_id = '$tour_id'");
    mysqli_query($conn, "DELETE FROM lich_khoi_hanh WHERE tour_id = '$tour_id'");
    mysqli_query($conn, "DELETE FROM tour_chi_tiet WHERE tour_id = '$tour_id'");
    
    // Xóa tour chính
    $sql = "DELETE FROM tour WHERE id = '$tour_id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: ../../html/Admin/TourController.php?msg=success");
    } else {
        header("Location: ../../html/Admin/TourController.php?msg=error");
    }
    exit;
} else {
    header("Location: ../../html/Admin/TourController.php");
    exit;
}
?>