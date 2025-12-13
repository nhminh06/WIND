<?php
// File: get_random_tours.php
// Trả về 9 tour ngẫu nhiên để cập nhật phần điểm đến yêu thích

header('Content-Type: application/json');

include '../../db/db.php';

// Lấy 9 tour ngẫu nhiên
$sql = "SELECT id, ten_tour, vi_tri, hinh_anh 
        FROM tour 
        WHERE trang_thai = 1 
        AND vi_tri IS NOT NULL 
        AND vi_tri != ''
        AND hinh_anh IS NOT NULL
        AND hinh_anh != ''
        ORDER BY RAND()
        LIMIT 11";

$result = mysqli_query($conn, $sql);

$tours = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $tours[] = [
            'id' => $row['id'],
            'ten_tour' => $row['ten_tour'],
            'vi_tri' => $row['vi_tri'],
            'hinh_anh' => $row['hinh_anh']
        ];
    }
}

echo json_encode($tours);

mysqli_close($conn);
?>