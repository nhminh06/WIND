<?php
session_start();
include '../../db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $loai = $_POST['loai'] ?? '';
    $lien_he_id = $_POST['lien_he_id'] ?? 0;
    $noi_dung = trim($_POST['noi_dung'] ?? '');
    
    if ($loai && $lien_he_id && $noi_dung) {
        // Lấy user_id từ bảng khieu_nai hoặc gop_y
        if ($loai == 'khieu_nai') {
            $sql = "SELECT user_id FROM khieu_nai WHERE id = ?";
        } else {
            $sql = "SELECT user_id FROM gop_y WHERE id = ?";
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $lien_he_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        if ($user) {
            // Thêm phản hồi
            $stmt = $conn->prepare("INSERT INTO phan_hoi (loai, lien_he_id, user_id, noi_dung) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siis", $loai, $lien_he_id, $user['user_id'], $noi_dung);
            
            if ($stmt->execute()) {
                // Cập nhật trạng thái đã xử lý
                $table = ($loai == 'khieu_nai') ? 'khieu_nai' : 'gop_y';
                $conn->query("UPDATE $table SET trang_thai = 1 WHERE id = $lien_he_id");
                
                $_SESSION['success'] = "Đã gửi phản hồi thành công!";
            } else {
                $_SESSION['error'] = "Lỗi khi gửi phản hồi";
            }
            $stmt->close();
        }
    }
    
    $conn->close();
    header("Location: ../../html/Admin/ContactController.php");
    exit;
}
?>