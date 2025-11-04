<?php
session_start();
include '../../db/db.php';

// Lấy ID bài viết từ URL
$bai_viet_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($bai_viet_id <= 0) {
    $_SESSION['error'] = "ID bài viết không hợp lệ!";
    header("Location: ../../html/Admin/ArticleController.php");
    exit();
}

$conn->begin_transaction();

try {
    // 1️⃣ Lấy danh sách hình ảnh trong các mục của bài viết để xóa sau
    $sql_images = "SELECT hinh_anh FROM bai_viet_muc WHERE bai_viet_id = ?";
    $stmt_images = $conn->prepare($sql_images);
    $stmt_images->bind_param("i", $bai_viet_id);
    $stmt_images->execute();
    $result_images = $stmt_images->get_result();

    $images_to_delete = [];
    while ($row = $result_images->fetch_assoc()) {
        if (!empty($row['hinh_anh'])) {
            $images_to_delete[] = $row['hinh_anh'];
        }
    }
    $stmt_images->close();

    // 2️⃣ Xóa các mục thuộc bài viết
    $sql_delete_muc = "DELETE FROM bai_viet_muc WHERE bai_viet_id = ?";
    $stmt_delete_muc = $conn->prepare($sql_delete_muc);
    $stmt_delete_muc->bind_param("i", $bai_viet_id);
    if (!$stmt_delete_muc->execute()) {
        throw new Exception("Lỗi khi xóa mục: " . $stmt_delete_muc->error);
    }
    $stmt_delete_muc->close();

    // 3️⃣ Xóa bài viết chính - SỬA: dùng 'id' (khóa chính) thay vì 'bai_viet_id'
    $sql_delete_baiviet = "DELETE FROM bai_viet WHERE id = ?";
    $stmt_delete_baiviet = $conn->prepare($sql_delete_baiviet);
    $stmt_delete_baiviet->bind_param("i", $bai_viet_id);
    if (!$stmt_delete_baiviet->execute()) {
        throw new Exception("Lỗi khi xóa bài viết: " . $stmt_delete_baiviet->error);
    }
    $stmt_delete_baiviet->close();

    // 4️⃣ Commit transaction
    $conn->commit();

    // 5️⃣ Sau khi xóa DB thành công → xóa file ảnh thật
    foreach ($images_to_delete as $image_path) {
        $full_path = "../../" . $image_path;
        if (file_exists($full_path)) {
            @unlink($full_path);
        }
    }

    $_SESSION['success'] = "Xóa bài viết thành công!";
} 
catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Có lỗi xảy ra khi xóa: " . $e->getMessage();
}

$conn->close();

// Quay lại trang danh sách bài viết
header("Location: ../../html/Admin/ArticleController.php");
exit();
?>
