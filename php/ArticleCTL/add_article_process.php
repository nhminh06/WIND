<?php
session_start();
include '../../db/db.php';

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
        header("Location: ../../html/Admin/AddArticleController.php");
        exit();
    }
    
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    try {
        // Kiểm tra xem đã có bài viết cho khampha_id này chưa
        $sql_check_existing = "SELECT id FROM bai_viet WHERE khampha_id = ?";
        $stmt_check = $conn->prepare($sql_check_existing);
        $stmt_check->bind_param("i", $khampha_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            // Nếu đã có bài viết, xóa bài viết cũ và các mục của nó
            $existing_article = $result_check->fetch_assoc();
            $existing_id = $existing_article['id'];
            
            // Xóa các mục cũ
            $sql_delete_muc = "DELETE FROM bai_viet_muc WHERE bai_viet_id = ?";
            $stmt_delete_muc = $conn->prepare($sql_delete_muc);
            $stmt_delete_muc->bind_param("i", $existing_id);
            if (!$stmt_delete_muc->execute()) {
                throw new Exception("Lỗi khi xóa mục cũ: " . $stmt_delete_muc->error);
            }
            $stmt_delete_muc->close();
            
            // Xóa bài viết cũ
            $sql_delete_article = "DELETE FROM bai_viet WHERE id = ?";
            $stmt_delete_article = $conn->prepare($sql_delete_article);
            $stmt_delete_article->bind_param("i", $existing_id);
            if (!$stmt_delete_article->execute()) {
                throw new Exception("Lỗi khi xóa bài viết cũ: " . $stmt_delete_article->error);
            }
            $stmt_delete_article->close();
        }
        $stmt_check->close();
        
        // 1. Thêm vào bảng bai_viet - CÓ THÊM tour_id
        $sql_baiviet = "INSERT INTO bai_viet (khampha_id, tieu_de, tour_id) VALUES (?, ?, ?)";
        $stmt_baiviet = $conn->prepare($sql_baiviet);
        
        // Nếu tour_id là null hoặc 0 thì bind null, ngược lại bind giá trị
        if ($tour_id === null || $tour_id === 0) {
            $stmt_baiviet->bind_param("isi", $khampha_id, $tieu_de, $tour_id_null);
            $tour_id_null = null;
        } else {
            $stmt_baiviet->bind_param("isi", $khampha_id, $tieu_de, $tour_id);
        }
        
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
        header("Location: ../../html/Admin/AddArticleController.php");
        exit();
    }
    
} else {
    // Nếu không phải POST request
    header("Location: ../../html/Admin/ArticleController.php");
    exit();
}

$conn->close();
?>