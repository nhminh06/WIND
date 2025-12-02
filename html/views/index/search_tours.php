<?php
// Tắt hiển thị lỗi PHP ra màn hình
error_reporting(0);
ini_set('display_errors', 0);

// Header JSON và CORS
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include database
include '../../../db/db.php';

try {
    $query = isset($_GET['q']) ? trim($_GET['q']) : '';
    
    if (empty($query)) {
        echo json_encode([
            'success' => false,
            'tours' => [],
            'message' => 'Không có từ khóa tìm kiếm'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Tìm kiếm tour với JOIN để lấy mô tả từ tour_chi_tiet
    $searchTerm = "%{$query}%";
    $sql = "SELECT t.id, t.ten_tour, t.gia, t.so_ngay, t.hinh_anh, 
                   tc.mo_ta_ngan as mo_ta
            FROM tour t
            LEFT JOIN tour_chi_tiet tc ON t.id = tc.tour_id
            WHERE t.trang_thai = 1 
            AND t.ten_tour LIKE ? 
            LIMIT 10";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        throw new Exception('Lỗi prepare statement: ' . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "s", $searchTerm);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $tours = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $tours[] = [
            'id' => (int)$row['id'],
            'ten_tour' => $row['ten_tour'],
            'gia' => (float)$row['gia'],
            'so_ngay' => (int)$row['so_ngay'],
            'hinh_anh' => $row['hinh_anh'] ?? '',
            'mo_ta' => $row['mo_ta'] ?? ''
        ];
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    echo json_encode([
        'success' => true,
        'tours' => $tours,
        'count' => count($tours),
        'query' => $query
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'tours' => [],
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>