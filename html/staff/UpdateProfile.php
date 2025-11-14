<?php
session_start();
include('../../db/db.php');

// Đặt header trả về JSON
header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Vui lòng đăng nhập!'
    ]);
    exit();
}

$staff_id = $_SESSION['user_id'];
$response = ['success' => false, 'message' => ''];

try {
    // Bắt đầu transaction
    $conn->begin_transaction();

    // 1. Xử lý upload ảnh (nếu có)
    $avatar_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        $file_type = $_FILES['photo']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $upload_dir = '../../uploads/avatars/';
            
            // Tạo thư mục nếu chưa có
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Tạo tên file unique
            $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $new_filename = 'avatar_' . $staff_id . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                $avatar_path = $upload_path;
                
                // Xóa ảnh cũ (nếu có)
                $sql_old_avatar = "SELECT avatar FROM user WHERE id = ?";
                $stmt = $conn->prepare($sql_old_avatar);
                $stmt->bind_param("i", $staff_id);
                $stmt->execute();
                $old_avatar = $stmt->get_result()->fetch_assoc();
                
                if ($old_avatar && $old_avatar['avatar'] && 
                    file_exists($old_avatar['avatar']) && 
                    $old_avatar['avatar'] != '../../images/default-avatar.png') {
                    unlink($old_avatar['avatar']);
                }
            }
        }
    }

    // 2. Cập nhật thông tin cơ bản
    $ho_ten = trim($_POST['ho_ten'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $about = trim($_POST['about'] ?? '');

    if (empty($ho_ten) || empty($position) || empty($about)) {
        throw new Exception('Vui lòng điền đầy đủ thông tin!');
    }

    // Cập nhật user
    if ($avatar_path) {
        $sql_update = "UPDATE user SET ho_ten = ?, position = ?, about = ?, avatar = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ssssi", $ho_ten, $position, $about, $avatar_path, $staff_id);
    } else {
        $sql_update = "UPDATE user SET ho_ten = ?, position = ?, about = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("sssi", $ho_ten, $position, $about, $staff_id);
    }
    
    if (!$stmt->execute()) {
        throw new Exception('Không thể cập nhật thông tin cơ bản!');
    }

    // 3. Cập nhật kỹ năng
    // Xóa tất cả kỹ năng cũ
    $sql_delete_skills = "DELETE FROM staff_skill WHERE staff_id = ?";
    $stmt = $conn->prepare($sql_delete_skills);
    $stmt->bind_param("i", $staff_id);
    $stmt->execute();

    // Thêm kỹ năng mới
    if (isset($_POST['skills'])) {
        $skills = json_decode($_POST['skills'], true);
        if (is_array($skills) && !empty($skills)) {
            $sql_insert_skill = "INSERT INTO staff_skill (staff_id, skill_name) VALUES (?, ?)";
            $stmt = $conn->prepare($sql_insert_skill);
            
            foreach ($skills as $skill) {
                $skill = trim($skill);
                if (!empty($skill)) {
                    $stmt->bind_param("is", $staff_id, $skill);
                    $stmt->execute();
                }
            }
        }
    }

    // 4. Cập nhật kinh nghiệm
    // Xóa tất cả kinh nghiệm cũ
    $sql_delete_exp = "DELETE FROM staff_experience WHERE staff_id = ?";
    $stmt = $conn->prepare($sql_delete_exp);
    $stmt->bind_param("i", $staff_id);
    $stmt->execute();

    // Thêm kinh nghiệm mới
    if (isset($_POST['experiences'])) {
        $experiences = json_decode($_POST['experiences'], true);
        if (is_array($experiences) && !empty($experiences)) {
            $sql_insert_exp = "INSERT INTO staff_experience (staff_id, title, description, year_start, year_end) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql_insert_exp);
            
            foreach ($experiences as $exp) {
                $title = trim($exp['title'] ?? '');
                $description = trim($exp['description'] ?? '');
                $year_start = intval($exp['year_start'] ?? 0);
                $year_end = !empty($exp['year_end']) ? intval($exp['year_end']) : null;
                
                if (!empty($title) && $year_start > 0) {
                    if ($year_end === null) {
                        $stmt->bind_param("issii", $staff_id, $title, $description, $year_start, $year_end);
                    } else {
                        $stmt->bind_param("issii", $staff_id, $title, $description, $year_start, $year_end);
                    }
                    $stmt->execute();
                }
            }
        }
    }

    // Commit transaction
    $conn->commit();
    
    $response['success'] = true;
    $response['message'] = 'Cập nhật hồ sơ thành công!';

} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollback();
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>