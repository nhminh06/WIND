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
    // Kiểm tra action - SỬA LẠI: lấy từ POST thay vì GET
    if (!isset($_POST['action'])) {
        throw new Exception('Không xác định được hành động!');
    }

    $action = $_POST['action']; // ✅ SỬA: từ $_GET thành $_POST

    // Bắt đầu transaction
    $conn->begin_transaction();

    switch ($action) {
        // ============================================
        // CẬP NHẬT THÔNG TIN CƠ BẢN
        // ============================================
        case 'update_basic_info':
            $ho_ten = trim($_POST['ho_ten'] ?? '');
            $gioi_tinh = trim($_POST['gioi_tinh'] ?? '');
            $dia_chi = trim($_POST['dia_chi'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $sdt = trim($_POST['sdt'] ?? '');
            $position = trim($_POST['position'] ?? '');
            
            // Validate
            if (empty($ho_ten)) {
                throw new Exception('Họ tên không được để trống!');
            }
            
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email không hợp lệ!');
            }
            
            if (!empty($sdt) && !preg_match('/^[0-9]{10,11}$/', $sdt)) {
                throw new Exception('Số điện thoại không hợp lệ!');
            }
            
            // Kiểm tra email đã tồn tại chưa (ngoại trừ email của chính user)
            if (!empty($email)) {
                $check_email = "SELECT id FROM user WHERE email = ? AND id != ?";
                $stmt_check = $conn->prepare($check_email);
                $stmt_check->bind_param("si", $email, $staff_id);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();
                
                if ($result_check->num_rows > 0) {
                    throw new Exception('Email này đã được sử dụng!');
                }
            }
            
            // Xử lý ngày sinh
            $ngay = intval($_POST['ngay'] ?? 1);
            $thang = intval($_POST['thang'] ?? 1);
            $nam = intval($_POST['nam'] ?? date('Y'));
            
            // Validate ngày sinh
            if (!checkdate($thang, $ngay, $nam)) {
                throw new Exception('Ngày sinh không hợp lệ!');
            }
            
            $ngay_sinh = sprintf("%04d-%02d-%02d", $nam, $thang, $ngay);
            
            // Cập nhật database
            $sql = "UPDATE user SET 
                    ho_ten = ?, 
                    gioi_tinh = ?, 
                    ngay_sinh = ?, 
                    dia_chi = ?, 
                    email = ?, 
                    sdt = ?,
                    position = ?
                    WHERE id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssi", $ho_ten, $gioi_tinh, $ngay_sinh, $dia_chi, $email, $sdt, $position, $staff_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Không thể cập nhật thông tin!');
            }
            
            $response['success'] = true;
            $response['message'] = 'Cập nhật thông tin cơ bản thành công!';
            break;

        // ============================================
        // CẬP NHẬT CHỈ EMAIL
        // ============================================
        case 'update_email':
            $email = trim($_POST['email'] ?? '');
            
            if (empty($email)) {
                throw new Exception('Email không được để trống!');
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email không hợp lệ!');
            }
            
            // Kiểm tra email đã tồn tại chưa
            $check_email = "SELECT id FROM user WHERE email = ? AND id != ?";
            $stmt_check = $conn->prepare($check_email);
            $stmt_check->bind_param("si", $email, $staff_id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows > 0) {
                throw new Exception('Email này đã được sử dụng!');
            }
            
            $sql = "UPDATE user SET email = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $email, $staff_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Không thể cập nhật email!');
            }
            
            $response['success'] = true;
            $response['message'] = 'Cập nhật email thành công!';
            break;

        // ============================================
        // CẬP NHẬT CHỈ SỐ ĐIỆN THOẠI
        // ============================================
        case 'update_phone':
            $sdt = trim($_POST['sdt'] ?? '');
            
            if (empty($sdt)) {
                throw new Exception('Số điện thoại không được để trống!');
            }
            
            if (!preg_match('/^[0-9]{10,11}$/', $sdt)) {
                throw new Exception('Số điện thoại không hợp lệ! (10-11 số)');
            }
            
            $sql = "UPDATE user SET sdt = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $sdt, $staff_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Không thể cập nhật số điện thoại!');
            }
            
            $response['success'] = true;
            $response['message'] = 'Cập nhật số điện thoại thành công!';
            break;

        // ============================================
        // CẬP NHẬT GIỚI THIỆU BẢN THÂN
        // ============================================
        case 'update_about':
            $about = trim($_POST['about'] ?? '');
            
            $sql = "UPDATE user SET about = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $about, $staff_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Không thể cập nhật giới thiệu!');
            }
            
            $response['success'] = true;
            $response['message'] = 'Cập nhật giới thiệu thành công!';
            break;

        // ============================================
        // CẬP NHẬT KỸ NĂNG
        // ============================================
        case 'update_skills':
            if (!isset($_POST['skills'])) {
                throw new Exception('Dữ liệu kỹ năng không hợp lệ!');
            }
            
            $skills = json_decode($_POST['skills'], true);
            
            if (!is_array($skills)) {
                throw new Exception('Định dạng dữ liệu kỹ năng không đúng!');
            }
            
            // Xóa tất cả kỹ năng cũ
            $sql_delete = "DELETE FROM staff_skill WHERE staff_id = ?";
            $stmt = $conn->prepare($sql_delete);
            $stmt->bind_param("i", $staff_id);
            $stmt->execute();
            
            // Thêm kỹ năng mới
            if (!empty($skills)) {
                $sql_insert = "INSERT INTO staff_skill (staff_id, skill_name) VALUES (?, ?)";
                $stmt = $conn->prepare($sql_insert);
                
                foreach ($skills as $skill) {
                    $skill = trim($skill);
                    if (!empty($skill)) {
                        $stmt->bind_param("is", $staff_id, $skill);
                        $stmt->execute();
                    }
                }
            }
            
            $response['success'] = true;
            $response['message'] = 'Cập nhật kỹ năng thành công!';
            break;

        // ============================================
        // CẬP NHẬT KINH NGHIỆM LÀM VIỆC
        // ============================================
        case 'update_experiences':
            if (!isset($_POST['experiences'])) {
                throw new Exception('Dữ liệu kinh nghiệm không hợp lệ!');
            }
            
            $experiences = json_decode($_POST['experiences'], true);
            
            if (!is_array($experiences)) {
                throw new Exception('Định dạng dữ liệu kinh nghiệm không đúng!');
            }
            
            // Xóa tất cả kinh nghiệm cũ
            $sql_delete = "DELETE FROM staff_experience WHERE staff_id = ?";
            $stmt = $conn->prepare($sql_delete);
            $stmt->bind_param("i", $staff_id);
            $stmt->execute();
            
            // Thêm kinh nghiệm mới
            if (!empty($experiences)) {
                $sql_insert = "INSERT INTO staff_experience (staff_id, title, description, year_start, year_end) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql_insert);
                
                foreach ($experiences as $exp) {
                    $title = trim($exp['title'] ?? '');
                    $description = trim($exp['description'] ?? '');
                    $year_start = intval($exp['year_start'] ?? 0);
                    $year_end = !empty($exp['year_end']) ? intval($exp['year_end']) : null;
                    
                    // Validate
                    if (empty($title)) {
                        throw new Exception('Chức danh không được để trống!');
                    }
                    
                    if ($year_start < 1900 || $year_start > date('Y')) {
                        throw new Exception('Năm bắt đầu không hợp lệ!');
                    }
                    
                    if ($year_end !== null && ($year_end < $year_start || $year_end > date('Y'))) {
                        throw new Exception('Năm kết thúc không hợp lệ!');
                    }
                    
                    // Insert vào database
                    $stmt->bind_param("issii", $staff_id, $title, $description, $year_start, $year_end);
                    
                    if (!$stmt->execute()) {
                        throw new Exception('Không thể thêm kinh nghiệm: ' . $title);
                    }
                }
            }
            
            $response['success'] = true;
            $response['message'] = 'Cập nhật kinh nghiệm thành công!';
            break;

        // ============================================
        // CẬP NHẬT TOÀN BỘ HỒ SƠ
        // ============================================
        case 'update_full_profile':
            // 1. Cập nhật thông tin cơ bản
            $ho_ten = trim($_POST['ho_ten'] ?? '');
            $gioi_tinh = trim($_POST['gioi_tinh'] ?? '');
            $dia_chi = trim($_POST['dia_chi'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $sdt = trim($_POST['sdt'] ?? '');
            $position = trim($_POST['position'] ?? '');
            $about = trim($_POST['about'] ?? '');
            
            if (empty($ho_ten)) {
                throw new Exception('Họ tên không được để trống!');
            }
            
            // Xử lý ngày sinh
            $ngay = intval($_POST['ngay'] ?? 1);
            $thang = intval($_POST['thang'] ?? 1);
            $nam = intval($_POST['nam'] ?? date('Y'));
            
            if (!checkdate($thang, $ngay, $nam)) {
                throw new Exception('Ngày sinh không hợp lệ!');
            }
            
            $ngay_sinh = sprintf("%04d-%02d-%02d", $nam, $thang, $ngay);
            
            $sql = "UPDATE user SET 
                    ho_ten = ?, 
                    gioi_tinh = ?, 
                    ngay_sinh = ?, 
                    dia_chi = ?, 
                    email = ?, 
                    sdt = ?,
                    position = ?,
                    about = ?
                    WHERE id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssssi", $ho_ten, $gioi_tinh, $ngay_sinh, $dia_chi, $email, $sdt, $position, $about, $staff_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Không thể cập nhật thông tin cơ bản!');
            }
            
            // 2. Cập nhật kỹ năng
            if (isset($_POST['skills'])) {
                $skills = json_decode($_POST['skills'], true);
                if (is_array($skills)) {
                    $sql_delete = "DELETE FROM staff_skill WHERE staff_id = ?";
                    $stmt = $conn->prepare($sql_delete);
                    $stmt->bind_param("i", $staff_id);
                    $stmt->execute();
                    
                    if (!empty($skills)) {
                        $sql_insert = "INSERT INTO staff_skill (staff_id, skill_name) VALUES (?, ?)";
                        $stmt = $conn->prepare($sql_insert);
                        
                        foreach ($skills as $skill) {
                            $skill = trim($skill);
                            if (!empty($skill)) {
                                $stmt->bind_param("is", $staff_id, $skill);
                                $stmt->execute();
                            }
                        }
                    }
                }
            }
            
            // 3. Cập nhật kinh nghiệm
            if (isset($_POST['experiences'])) {
                $experiences = json_decode($_POST['experiences'], true);
                if (is_array($experiences)) {
                    $sql_delete = "DELETE FROM staff_experience WHERE staff_id = ?";
                    $stmt = $conn->prepare($sql_delete);
                    $stmt->bind_param("i", $staff_id);
                    $stmt->execute();
                    
                    if (!empty($experiences)) {
                        $sql_insert = "INSERT INTO staff_experience (staff_id, title, description, year_start, year_end) VALUES (?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql_insert);
                        
                        foreach ($experiences as $exp) {
                            $title = trim($exp['title'] ?? '');
                            $description = trim($exp['description'] ?? '');
                            $year_start = intval($exp['year_start'] ?? 0);
                            $year_end = !empty($exp['year_end']) ? intval($exp['year_end']) : null;
                            
                            if (!empty($title) && $year_start > 0) {
                                $stmt->bind_param("issii", $staff_id, $title, $description, $year_start, $year_end);
                                $stmt->execute();
                            }
                        }
                    }
                }
            }
            
            $response['success'] = true;
            $response['message'] = 'Cập nhật hồ sơ đầy đủ thành công!';
            break;

        default:
            throw new Exception('Hành động không hợp lệ!');
    }

    // Commit transaction
    $conn->commit();

} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollback();
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// Trả về kết quả
echo json_encode($response, JSON_UNESCAPED_UNICODE);

// Đóng kết nối
$conn->close();
?>