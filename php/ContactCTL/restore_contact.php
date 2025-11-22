<?php
session_start();
include '../../db/db.php';

$id = $_GET['id'] ?? null;
$table = $_GET['table'] ?? '';
$allowed_tables = ['khieu_nai', 'gop_y'];

// Xác định giá trị khôi phục
// Nếu có session reply thì khôi phục về trạng thái đã xử lý (1), nếu không thì về chưa xử lý (0)
if(isset($_SESSION['reply'])) {
    $giatri = $_SESSION['reply'];
    unset($_SESSION['reply']); // Xóa session sau khi dùng
} else {
    $giatri = 1; // Mặc định khôi phục về trạng thái "Chưa xử lý"
}

if ($id && in_array($table, $allowed_tables)) {
    // Lấy trạng thái hiện tại
    $stmt = $conn->prepare("SELECT trang_thai FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if($row) {
        if($row['trang_thai'] == 2) {
            // Khôi phục → đổi về trạng thái được chọn (0 hoặc 1)
            $stmt_update = $conn->prepare("UPDATE $table SET trang_thai = ? WHERE id = ?");
            $stmt_update->bind_param("ii", $giatri, $id);

            if($stmt_update->execute()) {
                $_SESSION['success'] = "Khôi phục thành công!";
            } else {
                $_SESSION['error'] = "Khôi phục thất bại!";
            }
            $stmt_update->close();
        } else {
            $_SESSION['error'] = "Mục này chưa được lưu trữ, không cần khôi phục!";
        }
    } else {
        $_SESSION['error'] = "Không tìm thấy mục!";
    }

    $stmt->close();

} else {
    $_SESSION['error'] = "Dữ liệu không hợp lệ!";
}

$conn->close();
header("Location: ../../html/Admin/storage.php");
exit();
?>