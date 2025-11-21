<?php
session_start();
include '../../db/db.php';

$id = $_GET['id'] ?? null;
$table = $_GET['table'] ?? '';
$action = $_GET['action'] ?? ''; // 'toggle' hoặc 'archive'
$allowed_tables = ['khieu_nai', 'gop_y'];

if ($id && in_array($table, $allowed_tables)) {

    // Lấy trạng thái hiện tại
    $stmt = $conn->prepare("SELECT trang_thai FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if($row) {
        if($action === 'archive') {
            // Lưu trữ → luôn cho phép
            $stmt_update = $conn->prepare("UPDATE $table SET trang_thai = 2 WHERE id = ?");
            $stmt_update->bind_param("i", $id);
        } else {
            // Toggle → chỉ thực hiện nếu chưa lưu trữ
            if($row['trang_thai'] == 2) {
                $_SESSION['error'] = "Mục đã lưu trữ, không thể thay đổi trạng thái!";
                header("Location: ../../html/Admin/contactcontroller.php");
                exit();
            }
            $stmt_update = $conn->prepare("UPDATE $table SET trang_thai = IF(trang_thai = 0, 1, 0) WHERE id = ?");
            $stmt_update->bind_param("i", $id);
        }

        if($stmt_update->execute()) {
            $_SESSION['success'] = "Cập nhật trạng thái thành công!";
        } else {
            $_SESSION['error'] = "Cập nhật thất bại!";
        }
        $stmt_update->close();

    } else {
        $_SESSION['error'] = "Không tìm thấy mục!";
    }

    $stmt->close();

} else {
    $_SESSION['error'] = "Dữ liệu không hợp lệ!";
}

$conn->close();
header("Location: ../../html/Admin/contactcontroller.php");
exit();
?>
