<?php
session_start();
include '../../db/db.php';

// Kiểm tra quyền admin (nếu cần)
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
//     header("Location: ../../index.php");
//     exit();
// }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Lấy dữ liệu từ form
    $tour_id = isset($_POST['tour_id']) ? intval($_POST['tour_id']) : null;
    $loai_id = isset($_POST['loai_id']) ? intval($_POST['loai_id']) : 0;
    $tieu_de = isset($_POST['tieu_de']) ? trim($_POST['tieu_de']) : '';
    $khampha_id = isset($_POST['khampha_id']) ? intval($_POST['khampha_id']) : 0;
    
    // Mảng chứa các mục
    $tieu_de_muc = isset($_POST['tieu_de_muc']) ? $_POST['tieu_de_muc'] : [];
    $noi_dung_muc = isset($_POST['noi_dung_muc']) ? $_POST['noi_dung_muc'] : [];
    
    // Validate dữ liệu
    if (empty($tieu_de) || $khampha_id == 0 || empty($tieu_de_muc) || empty($noi_dung_muc)) {
        $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin bắt buộc!";
        header("Location: add_article.php");
        exit();
    }
    
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    try {
        // 1. Thêm vào bảng bai_viet
        $sql_baiviet = "INSERT INTO bai_viet (khampha_id, tieu_de) VALUES (?, ?)";
        $stmt_baiviet = $conn->prepare($sql_baiviet);
        $stmt_baiviet->bind_param("is", $khampha_id, $tieu_de);
        
        if (!$stmt_baiviet->execute()) {
            throw new Exception("Lỗi khi thêm bài viết: " . $stmt_baiviet->error);
        }
        
        // Lấy ID bài viết vừa tạo
        $bai_viet_id = $conn->insert_id;
        $stmt_baiviet->close();
        
        // 2. Thêm các mục vào bảng bai_viet_muc
        $sql_muc = "INSERT INTO bai_viet_muc (bai_viet_id, tieu_de_muc, noi_dung, hinh_anh) VALUES (?, ?, ?, ?)";
        $stmt_muc = $conn->prepare($sql_muc);
        
        // Xử lý upload ảnh
        $upload_dir = "../../uploads/baiviet/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Duyệt qua từng mục
        for ($i = 0; $i < count($tieu_de_muc); $i++) {
            $muc_tieu_de = trim($tieu_de_muc[$i]);
            $muc_noi_dung = trim($noi_dung_muc[$i]);
            $hinh_anh_path = null;
            
            // Xử lý upload ảnh cho mục này (nếu có)
            if (isset($_FILES['hinh_anh_muc']['name'][$i]) && !empty($_FILES['hinh_anh_muc']['name'][$i])) {
                $file_name = $_FILES['hinh_anh_muc']['name'][$i];
                $file_tmp = $_FILES['hinh_anh_muc']['tmp_name'][$i];
                $file_size = $_FILES['hinh_anh_muc']['size'][$i];
                $file_error = $_FILES['hinh_anh_muc']['error'][$i];
                
                // Kiểm tra lỗi upload
                if ($file_error === 0) {
                    // Kiểm tra kích thước (max 5MB)
                    if ($file_size <= 5242880) {
                        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'webp');
                        
                        if (in_array($file_ext, $allowed_ext)) {
                            // Tạo tên file unique
                            $new_file_name = uniqid('muc_', true) . '.' . $file_ext;
                            $file_destination = $upload_dir . $new_file_name;
                            
                            if (move_uploaded_file($file_tmp, $file_destination)) {
                                $hinh_anh_path = "uploads/baiviet/" . $new_file_name;
                            }
                        }
                    }
                }
            }
            
            // Insert mục vào database
            $stmt_muc->bind_param("isss", $bai_viet_id, $muc_tieu_de, $muc_noi_dung, $hinh_anh_path);
            
            if (!$stmt_muc->execute()) {
                throw new Exception("Lỗi khi thêm mục $i: " . $stmt_muc->error);
            }
        }
        
        $stmt_muc->close();
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['success'] = "Thêm bài viết thành công!";
        header("Location: ../../html/Admin/ArticleController.php");
        exit();
        
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $conn->rollback();
        
        $_SESSION['error'] = "Có lỗi xảy ra: " . $e->getMessage();
        header("Location: ../../html/Admin/ArticleController.php");
        exit();
    }
    
} else {
    // Nếu không phải POST request
    header("Location: ../../html/Admin/ArticleController.php");
    exit();
}

$conn->close();
?>  