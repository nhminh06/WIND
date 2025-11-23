<?php
session_start();
include '../../db/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../html/views/index/Webindex.php');
    exit();
}

// Xử lý form khi submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $ho_ten = isset($_POST['ho_ten']) ? trim($_POST['ho_ten']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $sdt = isset($_POST['sdt']) ? trim($_POST['sdt']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $role = isset($_POST['role']) ? $_POST['role'] : '';
    $trang_thai = isset($_POST['trang_thai']) ? 1 : 0;
    $dia_chi = isset($_POST['dia_chi']) ? trim($_POST['dia_chi']) : '';
    $ngay_sinh = !empty($_POST['ngay_sinh']) ? $_POST['ngay_sinh'] : NULL;
    $gioi_tinh = !empty($_POST['gioi_tinh']) ? $_POST['gioi_tinh'] : NULL;
    
    // Mảng lưu lỗi
    $errors = [];
    
    // Validate dữ liệu
    if (empty($ho_ten)) {
        $errors[] = "Họ tên không được để trống";
    }
    
    if (empty($email)) {
        $errors[] = "Email không được để trống";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ";
    }
    
    if (empty($password)) {
        $errors[] = "Mật khẩu không được để trống";
    } elseif (strlen($password) < 6) {
        $errors[] = "Mật khẩu phải có ít nhất 6 ký tự";
    }
    
    if (empty($role) || !in_array($role, ['admin', 'staff', 'user'])) {
        $errors[] = "Vai trò không hợp lệ";
    }
    
    // Validate số điện thoại (nếu có nhập)
    if (!empty($sdt)) {
        // Loại bỏ khoảng trắng và ký tự đặc biệt
        $sdt = preg_replace('/[^0-9]/', '', $sdt);
        
        // Kiểm tra độ dài 10-11 số và bắt đầu bằng 0
        if (!preg_match('/^0[0-9]{9,10}$/', $sdt)) {
            $errors[] = "Số điện thoại không hợp lệ (phải có 10-11 số và bắt đầu bằng 0)";
        }
    }
    
    // Kiểm tra email đã tồn tại chưa
    if (empty($errors)) {
        $check_email = $conn->prepare("SELECT id FROM user WHERE email = ?");
        if ($check_email) {
            $check_email->bind_param("s", $email);
            $check_email->execute();
            $result = $check_email->get_result();
            
            if ($result->num_rows > 0) {
                $errors[] = "Email đã được sử dụng bởi người dùng khác";
            }
            $check_email->close();
        }
    }
    
    // Nếu có lỗi, quay lại form
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        header('Location: ../../html/Admin/add_user.php');
        exit();
    }
    
    // Thêm user vào database (không mã hóa mật khẩu)
    $sql = "INSERT INTO user (ho_ten, email, sdt, password, role, trang_thai, dia_chi, ngay_sinh, gioi_tinh, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        $_SESSION['error'] = "Lỗi chuẩn bị câu lệnh: " . $conn->error;
        header('Location: ../../html/Admin/add_user.php');
        exit();
    }
    
    // Bind parameters - chú ý thứ tự type: s=string, i=integer
    $stmt->bind_param(
        "sssssisss", 
        $ho_ten, 
        $email, 
        $sdt, 
        $password, 
        $role, 
        $trang_thai, 
        $dia_chi, 
        $ngay_sinh, 
        $gioi_tinh
    );
    
    // Thực thi câu lệnh
    if ($stmt->execute()) {
        $new_user_id = $conn->insert_id;
        $_SESSION['success'] = "Thêm người dùng <strong>" . htmlspecialchars($ho_ten) . "</strong> thành công! (ID: #" . str_pad($new_user_id, 3, '0', STR_PAD_LEFT) . ")";
        $stmt->close();
        $conn->close();
        header('Location: ../../html/Admin/UserController.php');
        exit();
    } else {
        $_SESSION['error'] = "Có lỗi xảy ra khi thêm người dùng: " . $stmt->error;
        $stmt->close();
        $conn->close();
        header('Location: ../../html/Admin/add_user.php');
        exit();
    }
    
} else {
    // Nếu không phải POST request, chuyển về trang danh sách user
    header('Location: ../../html/Admin/UserController.php');
    exit();
}
?>