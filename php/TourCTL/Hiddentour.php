<?php
include "../../db/db.php";
$tour_id = $_GET['id'];
$sql_check = "SELECT trang_thai FROM tour WHERE id = '$tour_id'";
$result = mysqli_query($conn, $sql_check);

if ($row = mysqli_fetch_assoc($result)) {
    $trang_thai_moi = ($row['trang_thai'] == 1) ? 0 : 1;

   
    $sql_update = "UPDATE tour SET trang_thai = '$trang_thai_moi' WHERE id = '$tour_id'";
    if (mysqli_query($conn, $sql_update)) {
        header("Location: ../../html/Admin/TourController.php");
        exit();
    } else {
        echo "Lỗi khi cập nhật trạng thái tour: " . mysqli_error($conn);
    }
} else {
    echo "Không tìm thấy tour với ID = $tour_id";
}
?>
