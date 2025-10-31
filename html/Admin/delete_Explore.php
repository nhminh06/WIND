<?php
session_start();
include '../../db/db.php';

// Lấy ID từ URL
$khampha_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Kiểm tra ID hợp lệ
if ($khampha_id == 0) {
    $_SESSION['error'] = 'ID không hợp lệ!';
    header('Location: ExploreController.php');
    exit();
}

// Bước 1: Kiểm tra xem có bài viết liên quan không
$sql_check_bv = "SELECT COUNT(*) as total FROM bai_viet WHERE khampha_id = ?";
$stmt_check = $conn->prepare($sql_check_bv);
$stmt_check->bind_param("i", $khampha_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$check_bv = $result_check->fetch_assoc();

if ($check_bv['total'] > 0) {
    $_SESSION['error'] = '❌ Không thể xóa! Khám phá này đang có ' . $check_bv['total'] . ' bài viết liên quan. Vui lòng xóa bài viết trước.';
    header('Location: ExploreController.php');
    exit();
}

// Bước 2: Lấy thông tin khám phá để hiển thị thông báo
$sql_info = "SELECT tieu_de FROM khampha WHERE khampha_id = ?";
$stmt_info = $conn->prepare($sql_info);
$stmt_info->bind_param("i", $khampha_id);
$stmt_info->execute();
$result_info = $stmt_info->get_result();
$khampha_info = $result_info->fetch_assoc();

if (!$khampha_info) {
    $_SESSION['error'] = 'Không tìm thấy khám phá này!';
    header('Location: ExploreController.php');
    exit();
}

// Bước 3: Lấy danh sách ảnh để xóa file vật lý
$sql_images = "SELECT anh_id, duong_dan_anh FROM khampha_anh WHERE khampha_id = ?";
$stmt_images = $conn->prepare($sql_images);
$stmt_images->bind_param("i", $khampha_id);
$stmt_images->execute();
$result_images = $stmt_images->get_result();

// Xóa các file ảnh trên server
$deleted_files = 0;
$failed_files = 0;
$image_list = [];

while ($img = $result_images->fetch_assoc()) {
    $image_list[] = $img;
    if (file_exists($img['duong_dan_anh'])) {
        if (unlink($img['duong_dan_anh'])) {
            $deleted_files++;
        } else {
            $failed_files++;
        }
    }
}

// Bước 4: Xóa records ảnh trong database
$sql_delete_images = "DELETE FROM khampha_anh WHERE khampha_id = ?";
$stmt_delete_images = $conn->prepare($sql_delete_images);
$stmt_delete_images->bind_param("i", $khampha_id);
$images_deleted = $stmt_delete_images->execute();

// Bước 5: Xóa khám phá
$sql_delete_khampha = "DELETE FROM khampha WHERE khampha_id = ?";
$stmt_delete = $conn->prepare($sql_delete_khampha);
$stmt_delete->bind_param("i", $khampha_id);

if ($stmt_delete->execute()) {
    // Thành công
    $success_msg = '✅ Xóa khám phá "' . htmlspecialchars($khampha_info['tieu_de']) . '" thành công!';
    
    if ($deleted_files > 0) {
        $success_msg .= ' Đã xóa ' . $deleted_files . ' file ảnh.';
    }
    
    if ($failed_files > 0) {
        $success_msg .= ' (Có ' . $failed_files . ' file không xóa được)';
    }
    
    $_SESSION['success'] = $success_msg;
} else {
    // Thất bại
    $_SESSION['error'] = '❌ Có lỗi xảy ra khi xóa: ' . $conn->error;
}

// Đóng kết nối và chuyển hướng
$stmt_check->close();
$stmt_info->close();
$stmt_images->close();
$stmt_delete_images->close();
$stmt_delete->close();
$conn->close();

header('Location: ExploreController.php');
exit();
?>