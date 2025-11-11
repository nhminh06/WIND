<?php
session_start();
include('../../db/db.php');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_id = $_SESSION['id'];
    
    // Lấy dữ liệu từ POST
    $full_name = trim($_POST['full_name']);
    $position = trim($_POST['position']);
    $about = trim($_POST['about']);
    $skills = isset($_POST['skills']) ? json_decode($_POST['skills'], true) : [];
    $experiences = isset($_POST['experiences']) ? json_decode($_POST['experiences'], true) : [];
    
    // Xử lý upload ảnh
    $photo_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['photo']['name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($file_ext, $allowed)) {
            $new_filename = 'staff_' . $staff_id . '_' . time() . '.' . $file_ext;
            $upload_dir = '../../uploads/profiles/';
            
            // Tạo thư mục nếu chưa có
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                $photo_path = '/uploads/profiles/' . $new_filename;
            }
        }
    }
    
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    try {
        // Cập nhật thông tin cơ bản
        if ($photo_path) {
            $sql = "UPDATE user SET full_name = ?, position = ?, about = ?, photo = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $full_name, $position, $about, $photo_path, $staff_id);
        } else {
            $sql = "UPDATE user SET full_name = ?, position = ?, about = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $full_name, $position, $about, $staff_id);
        }
        $stmt->execute();
        
        // Xóa kỹ năng cũ
        $sql_del_skill = "DELETE FROM staff_skill WHERE staff_id = ?";
        $stmt_del_skill = $conn->prepare($sql_del_skill);
        $stmt_del_skill->bind_param("i", $staff_id);
        $stmt_del_skill->execute();
        
        // Thêm kỹ năng mới
        if (!empty($skills)) {
            $sql_skill = "INSERT INTO staff_skill (staff_id, skill_name) VALUES (?, ?)";
            $stmt_skill = $conn->prepare($sql_skill);
            
            foreach ($skills as $skill) {
                $stmt_skill->bind_param("is", $staff_id, $skill);
                $stmt_skill->execute();
            }
        }
        
        // Xóa kinh nghiệm cũ
        $sql_del_exp = "DELETE FROM staff_experience WHERE staff_id = ?";
        $stmt_del_exp = $conn->prepare($sql_del_exp);
        $stmt_del_exp->bind_param("i", $staff_id);
        $stmt_del_exp->execute();
        
        // Thêm kinh nghiệm mới
        if (!empty($experiences)) {
            $sql_exp = "INSERT INTO staff_experience (staff_id, title, year_start, year_end, description) VALUES (?, ?, ?, ?, ?)";
            $stmt_exp = $conn->prepare($sql_exp);
            
            foreach ($experiences as $exp) {
                $year_end = empty($exp['year_end']) ? null : $exp['year_end'];
                $stmt_exp->bind_param("isiis", 
                    $staff_id, 
                    $exp['title'], 
                    $exp['year_start'], 
                    $year_end, 
                    $exp['description']
                );
                $stmt_exp->execute();
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Cập nhật hồ sơ thành công!',
            'photo' => $photo_path
        ]);
        
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
    }
    
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
}
?>