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
    
    // Validate dữ liệu cơ bản
    if ($bai_viet_id == 0) {
        $_SESSION['error'] = "ID bài viết không hợp lệ!";
        header("Location: ../../html/Admin/ArticleController.php");
        exit();
    }
    
    if (empty($tieu_de) || $khampha_id == 0 || $loai_id == 0) {
        $_SESSION['error'] = "Vui lòng điền đầy đủ tiêu đề, chọn mục khám phá và loại bài viết!";
        header("Location: ../../html/Admin/edit_article.php?id=" . $bai_viet_id);
        exit();
    }
    
    // Validate các mục nội dung
    $has_valid_muc = false;
    foreach ($tieu_de_muc as $index => $tieu_de_muc_item) {
        if (!empty(trim($tieu_de_muc_item)) && !empty(trim($noi_dung_muc[$index]))) {
            $has_valid_muc = true;
            break;
        }
    }
    
    if (!$has_valid_muc) {
        $_SESSION['error'] = "Phải có ít nhất một mục nội dung với tiêu đề và nội dung đầy đủ!";
        header("Location: ../../html/Admin/edit_article.php?id=" . $bai_viet_id);
        exit();
    }
    
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    try {
        // 1. Kiểm tra bài viết có tồn tại không và lấy khampha_id hiện tại
        $sql_check = "SELECT id, khampha_id FROM bai_viet WHERE id = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("i", $bai_viet_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows == 0) {
            throw new Exception("Bài viết không tồn tại!");
        }
        
        $current_article = $result_check->fetch_assoc();
        $current_khampha_id = $current_article['khampha_id'];
        $stmt_check->close();
        
        // 2. Cập nhật thông tin trong bảng khampha
        $sql_update_khampha = "UPDATE khampha SET loai_id = ?, tour_id = ? WHERE khampha_id = ?";
        $stmt_update_khampha = $conn->prepare($sql_update_khampha);
        
        // Nếu tour_id là rỗng, set thành NULL
        $tour_id_value = ($tour_id == 0 || $tour_id === '') ? null : $tour_id;
        $stmt_update_khampha->bind_param("iii", $loai_id, $tour_id_value, $khampha_id);
        
        if (!$stmt_update_khampha->execute()) {
            throw new Exception("Lỗi khi cập nhật thông tin khám phá: " . $stmt_update_khampha->error);
        }
        $stmt_update_khampha->close();
        
        // 3. Cập nhật thông tin bài viết chính
        $sql_update_baiviet = "UPDATE bai_viet SET khampha_id = ?, tieu_de = ? WHERE id = ?";
        $stmt_update_baiviet = $conn->prepare($sql_update_baiviet);
        $stmt_update_baiviet->bind_param("isi", $khampha_id, $tieu_de, $bai_viet_id);
        
        if (!$stmt_update_baiviet->execute()) {
            throw new Exception("Lỗi khi cập nhật bài viết: " . $stmt_update_baiviet->error);
        }
        $stmt_update_baiviet->close();
        
        // 4. Lấy danh sách mục hiện tại trong database
        $sql_existing = "SELECT id, hinh_anh FROM bai_viet_muc WHERE bai_viet_id = ?";
        $stmt_existing = $conn->prepare($sql_existing);
        $stmt_existing->bind_param("i", $bai_viet_id);
        $stmt_existing->execute();
        $result_existing = $stmt_existing->get_result();
        
        $existing_mucs = [];
        $existing_images = [];
        while ($row = $result_existing->fetch_assoc()) {
            $existing_mucs[] = $row['id'];
            if (!empty($row['hinh_anh'])) {
                $existing_images[$row['id']] = $row['hinh_anh'];
            }
        }
        $stmt_existing->close();
        
        // 5. Xác định mục nào cần xóa
        $submitted_mucs = array_filter($muc_id, function($id) { 
            return $id > 0; 
        });
        $mucs_to_delete = array_diff($existing_mucs, $submitted_mucs);
        
        // Xóa các mục không còn trong form và xóa file ảnh của chúng
        if (!empty($mucs_to_delete)) {
            // Xóa file ảnh của các mục bị xóa
            foreach ($mucs_to_delete as $muc_id_to_delete) {
                if (isset($existing_images[$muc_id_to_delete]) && !empty($existing_images[$muc_id_to_delete])) {
                    $image_path = "../../" . $existing_images[$muc_id_to_delete];
                    if (file_exists($image_path)) {
                        @unlink($image_path);
                    }
                }
            }
            
            // Xóa mục khỏi database
            $placeholders = implode(',', array_fill(0, count($mucs_to_delete), '?'));
            $sql_delete = "DELETE FROM bai_viet_muc WHERE id IN ($placeholders)";
            $stmt_delete = $conn->prepare($sql_delete);
            $types = str_repeat('i', count($mucs_to_delete));
            $stmt_delete->bind_param($types, ...$mucs_to_delete);
            
            if (!$stmt_delete->execute()) {
                throw new Exception("Lỗi khi xóa mục: " . $stmt_delete->error);
            }
            $stmt_delete->close();
        }
        
        // 6. Xử lý upload ảnh và cập nhật/thêm mới các mục
        $upload_dir = "../../uploads/baiviet/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        for ($i = 0; $i < count($tieu_de_muc); $i++) {
            $current_muc_id = intval($muc_id[$i]);
            $muc_tieu_de = trim($tieu_de_muc[$i]);
            $muc_noi_dung = trim($noi_dung_muc[$i]);
            $hinh_anh_path = $old_hinh_anh[$i]; // Giữ ảnh cũ mặc định
            
            // Bỏ qua mục trống
            if (empty($muc_tieu_de) || empty($muc_noi_dung)) {
                continue;
            }
            
            // Xử lý upload ảnh mới (nếu có)
            if (isset($_FILES['hinh_anh_muc']['name'][$i]) && !empty($_FILES['hinh_anh_muc']['name'][$i])) {
                $file_name = $_FILES['hinh_anh_muc']['name'][$i];
                $file_tmp = $_FILES['hinh_anh_muc']['tmp_name'][$i];
                $file_size = $_FILES['hinh_anh_muc']['size'][$i];
                $file_error = $_FILES['hinh_anh_muc']['error'][$i];
                
                if ($file_error === 0) {
                    if ($file_size <= 5242880) { // 5MB
                        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'webp');
                        
                        if (in_array($file_ext, $allowed_ext)) {
                            $new_file_name = uniqid('muc_', true) . '.' . $file_ext;
                            $file_destination = $upload_dir . $new_file_name;
                            
                            if (move_uploaded_file($file_tmp, $file_destination)) {
                                // Xóa ảnh cũ nếu có
                                if (!empty($old_hinh_anh[$i]) && file_exists("../../" . $old_hinh_anh[$i])) {
                                    @unlink("../../" . $old_hinh_anh[$i]);
                                }
                                $hinh_anh_path = "uploads/baiviet/" . $new_file_name;
                            }
                        } else {
                            throw new Exception("Định dạng file không hợp lệ. Chỉ chấp nhận JPG, PNG, GIF, WEBP.");
                        }
                    } else {
                        throw new Exception("Kích thước file quá lớn. Tối đa 5MB.");
                    }
                } else {
                    throw new Exception("Lỗi upload file: " . $file_error);
                }
            }
            
            // Cập nhật hoặc thêm mới
            if ($current_muc_id > 0) {
                // UPDATE mục hiện có
                $sql_update_muc = "UPDATE bai_viet_muc 
                                  SET tieu_de_muc = ?, noi_dung = ?, hinh_anh = ? 
                                  WHERE id = ? AND bai_viet_id = ?";
                $stmt_muc = $conn->prepare($sql_update_muc);
                $stmt_muc->bind_param("sssii", $muc_tieu_de, $muc_noi_dung, $hinh_anh_path, $current_muc_id, $bai_viet_id);
            } else {
                // INSERT mục mới
                $sql_insert_muc = "INSERT INTO bai_viet_muc (bai_viet_id, tieu_de_muc, noi_dung, hinh_anh) 
                                  VALUES (?, ?, ?, ?)";
                $stmt_muc = $conn->prepare($sql_insert_muc);
                $stmt_muc->bind_param("isss", $bai_viet_id, $muc_tieu_de, $muc_noi_dung, $hinh_anh_path);
            }
            
            if (!$stmt_muc->execute()) {
                throw new Exception("Lỗi khi xử lý mục '" . $muc_tieu_de . "': " . $stmt_muc->error);
            }
            $stmt_muc->close();
        }
        
        // 7. Kiểm tra xem còn mục nào không
        $sql_check_remaining = "SELECT COUNT(*) as total FROM bai_viet_muc WHERE bai_viet_id = ?";
        $stmt_check_remaining = $conn->prepare($sql_check_remaining);
        $stmt_check_remaining->bind_param("i", $bai_viet_id);
        $stmt_check_remaining->execute();
        $result_remaining = $stmt_check_remaining->get_result();
        $remaining_count = $result_remaining->fetch_assoc()['total'];
        $stmt_check_remaining->close();
        
        if ($remaining_count == 0) {
            throw new Exception("Bài viết phải có ít nhất một mục nội dung!");
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
    // Nếu không phải POST request
    $_SESSION['error'] = "Phương thức không hợp lệ!";
    header("Location: ../../html/Admin/ArticleController.php");
    exit();
}

$conn->close();
?>