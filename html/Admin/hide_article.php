<?php
session_start();
include '../../db/db.php';

if (isset($_GET['id'])) {
    $bai_viet_id = intval($_GET['id']);
    
    // Lấy trạng thái hiện tại
    $sql_get = "SELECT trang_thai FROM bai_viet WHERE id = ?";
    $stmt_get = $conn->prepare($sql_get);
    $stmt_get->bind_param("i", $bai_viet_id);
    $stmt_get->execute();
    $result = $stmt_get->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $trang_thai_moi = ($row['trang_thai'] == 1) ? 0 : 1; // đảo trạng thái
        
        // Cập nhật trạng thái
        $sql_update = "UPDATE bai_viet SET trang_thai = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ii", $trang_thai_moi, $bai_viet_id);
        
        if ($stmt_update->execute()) {
            $_SESSION['success'] = ($trang_thai_moi == 1) 
                ? "Bài viết đã được hiển thị lại!" 
                : "Bài viết đã được ẩn thành công!";
        } else {
            $_SESSION['error'] = "Không thể cập nhật trạng thái bài viết!";
        }

        $stmt_update->close();
    } else {
        $_SESSION['error'] = "Không tìm thấy bài viết!";
    }

    $stmt_get->close();
} else {
    $_SESSION['error'] = "Thiếu ID bài viết!";
}

$conn->close();
header("Location: ../../html/Admin/ArticleController.php");
exit();
?>
