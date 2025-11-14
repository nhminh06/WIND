<?php
session_start();
include('../../db/db.php'); // đường dẫn đến file kết nối CSDL

header('Content-Type: application/json');

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Chưa đăng nhập']);
    exit;
}

// Kiểm tra file
if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] != 0) {
    echo json_encode(['status' => 'error', 'message' => 'Không có ảnh hợp lệ']);
    exit;
}

$userId = $_SESSION['user_id'];
$uploadDir = '../../uploads/avatars/';

// Tạo thư mục nếu chưa có
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Lấy phần mở rộng file (jpg/png/gif)
$ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
$allowed = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array(strtolower($ext), $allowed)) {
    echo json_encode(['status' => 'error', 'message' => 'Định dạng ảnh không hợp lệ']);
    exit;
}

// Tạo tên file duy nhất
$filename = 'user_' . $userId . '_' . time() . '.' . $ext;
$path = $uploadDir . $filename;

// Lưu file lên server
if (move_uploaded_file($_FILES['avatar']['tmp_name'], $path)) {
    $avatarPath = 'uploads/avatars/' . $filename;

    // Cập nhật vào database
    $sql = "UPDATE user SET avatar = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $avatarPath, $userId);

    if ($stmt->execute()) {
        $_SESSION['avatar'] = $avatarPath; // Cập nhật session
        echo json_encode(['status' => 'success', 'path' => $avatarPath]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Không thể cập nhật CSDL']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Không thể tải lên ảnh']);
}
?>
