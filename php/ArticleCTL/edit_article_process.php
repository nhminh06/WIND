<?php
session_start();
include '../../db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Lấy dữ liệu từ form
    $bai_viet_id = isset($_POST['bai_viet_id']) ? intval($_POST['bai_viet_id']) : 0;
    $tour_id = isset($_POST['tour_id']) ? intval($_POST['tour_id']) : null;
    $loai_id = isset($_POST['loai_id']) ? intval($_POST['loai_id']) : 0;
    $tieu_de = isset($_POST['tieu_de']) ? trim($_POST['tieu_de']) : '';
    $khampha_id = isset($_POST['khampha_id']) ? intval($_POST['khampha_id']) : 0;
    
    // Mảng chứa các mục
    $muc_id = isset($_POST['muc_id']) ? $_POST['muc_id'] : [];
    $tieu_de_muc = isset($_POST['tieu_de_muc']) ? $_POST['tieu_de_muc'] : [];
    $noi_dung_muc = isset($_POST['noi_dung_muc']) ? $_POST['noi_dung_muc'] : [];
    $old_hinh_anh = isset($_POST['old_hinh_anh']) ? $_POST['old_hinh_anh'] : [];
    
    // Validate dữ liệu
    if ($bai_viet_id == 0 || empty($tieu_de) || $khampha_id == 0 || empty($tieu_de_muc) || empty($noi_dung_muc)) {
        $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin bắt buộc!";
        header("Location: ../../html/Admin/edit_article.php?id=" . $bai_viet_id);
        exit();
    }
    
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    try {
        // 1. Cập nhật thông tin bài viết
        $sql_update = "UPDATE bai_viet SET khampha_id = ?, tieu_de = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("isi", $khampha_id, $tieu_de, $bai_viet_id);
        
        if (!$stmt_update->execute()) {
            throw new Exception("Lỗi khi cập nhật bài viết: " . $stmt_update->error);
        }
        $stmt_update->close();
        
        // 2. Lấy danh sách id hiện tại trong database (các mục bài viết)
        $sql_existing = "SELECT id FROM bai_viet_muc WHERE bai_viet_id = ?";
        $stmt_existing = $conn->prepare($sql_existing);
        $stmt_existing->bind_param("i", $bai_viet_id);
        $stmt_existing->execute();
        $result_existing = $stmt_existing->get_result();
        $existing_mucs = [];
        while ($row = $result_existing->fetch_assoc()) {
            $existing_mucs[] = $row['id'];
        }
        $stmt_existing->close();
        
        // 3. Xác định mục nào cần xóa
        $submitted_mucs = array_filter($muc_id, function($id) { return $id > 0; });
        $mucs_to_delete = array_diff($existing_mucs, $submitted_mucs);
        
        // Xóa các mục không còn trong form
        if (!empty($mucs_to_delete)) {
            $placeholders = implode(',', array_fill(0, count($mucs_to_delete), '?'));
            $sql_delete = "DELETE FROM bai_viet_muc WHERE id IN ($placeholders)";
            $stmt_delete = $conn->prepare($sql_delete);
            $types = str_repeat('i', count($mucs_to_delete));
            $stmt_delete->bind_param($types, ...$mucs_to_delete);
            $stmt_delete->execute();
            $stmt_delete->close();
        }
        
        // 4. Xử lý upload ảnh
        $upload_dir = "../../uploads/baiviet/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // 5. Cập nhật hoặc thêm mới các mục
        for ($i = 0; $i < count($tieu_de_muc); $i++) {
            $current_muc_id = intval($muc_id[$i]);
            $muc_tieu_de = trim($tieu_de_muc[$i]);
            $muc_noi_dung = trim($noi_dung_muc[$i]);
            $hinh_anh_path = $old_hinh_anh[$i]; // Giữ ảnh cũ mặc định
            
            // Xử lý upload ảnh mới (nếu có)
            if (isset($_FILES['hinh_anh_muc']['name'][$i]) && !empty($_FILES['hinh_anh_muc']['name'][$i])) {
                $file_name = $_FILES['hinh_anh_muc']['name'][$i];
                $file_tmp = $_FILES['hinh_anh_muc']['tmp_name'][$i];
                $file_size = $_FILES['hinh_anh_muc']['size'][$i];
                $file_error = $_FILES['hinh_anh_muc']['error'][$i];
                
                if ($file_error === 0 && $file_size <= 5242880) {
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    $allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'webp');
                    
                    if (in_array($file_ext, $allowed_ext)) {
                        $new_file_name = uniqid('muc_', true) . '.' . $file_ext;
                        $file_destination = $upload_dir . $new_file_name;
                        
                        if (move_uploaded_file($file_tmp, $file_destination)) {
                            // Xóa ảnh cũ nếu có
                            if (!empty($old_hinh_anh[$i]) && file_exists("../../" . $old_hinh_anh[$i])) {
                                unlink("../../" . $old_hinh_anh[$i]);
                            }
                            $hinh_anh_path = "uploads/baiviet/" . $new_file_name;
                        }
                    }
                }
            }
            
            // Cập nhật hoặc thêm mới
            if ($current_muc_id > 0) {
                // UPDATE mục hiện có
                $sql_update_muc = "UPDATE bai_viet_muc 
                                  SET tieu_de_muc = ?, noi_dung = ?, hinh_anh = ? 
                                  WHERE id = ?";
                $stmt_muc = $conn->prepare($sql_update_muc);
                $stmt_muc->bind_param("sssi", $muc_tieu_de, $muc_noi_dung, $hinh_anh_path, $current_muc_id);
            } else {
                // INSERT mục mới
                $sql_insert_muc = "INSERT INTO bai_viet_muc (bai_viet_id, tieu_de_muc, noi_dung, hinh_anh) 
                                  VALUES (?, ?, ?, ?)";
                $stmt_muc = $conn->prepare($sql_insert_muc);
                $stmt_muc->bind_param("isss", $bai_viet_id, $muc_tieu_de, $muc_noi_dung, $hinh_anh_path);
            }
            
            if (!$stmt_muc->execute()) {
                throw new Exception("Lỗi khi xử lý mục $i: " . $stmt_muc->error);
            }
            $stmt_muc->close();
        }
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['success'] = "Cập nhật bài viết thành công!";
        header("Location: ../../html/Admin/ArticleController.php");
        exit();
        
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $conn->rollback();
        
        $_SESSION['error'] = "Có lỗi xảy ra: " . $e->getMessage();
        header("Location: ../../html/Admin/edit_article.php?id=" . $bai_viet_id);
        exit();
    }
    
} else {
    header("Location: ../../html/Admin/ArticleController.php");
    exit();
}

$conn->close();
?>
