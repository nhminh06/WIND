<?php
include '../../db/db.php';

// Thư mục upload
$uploadDir = "../../uploads/";

// Tạo thư mục nếu chưa tồn tại
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$anh1 = "";
$anh2 = "";

// Upload ảnh 1
if (!empty($_FILES['anh1']['name'])) {
    $anh1 = $uploadDir . basename($_FILES['anh1']['name']);
    move_uploaded_file($_FILES['anh1']['tmp_name'], $anh1);
}

// Upload ảnh 2
if (!empty($_FILES['anh2']['name'])) {
    $anh2 = $uploadDir . basename($_FILES['anh2']['name']);
    move_uploaded_file($_FILES['anh2']['tmp_name'], $anh2);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id   = mysqli_real_escape_string($conn, $_POST['id']);
    $ten  = mysqli_real_escape_string($conn, $_POST['ten']);
    $mota = mysqli_real_escape_string($conn, $_POST['mota']);

    // Lấy ID bài viết liên kết
    $bai_viet = mysqli_real_escape_string($conn, $_POST['bai_viet']);

    // Tạo link hoàn chỉnh
    $link = "";
    if (!empty($bai_viet)) {
        // Tạo dạng link chi tiết
        $link = "detailed_explore.php?id=" . $bai_viet;
    }

    // Nếu sửa → cần lấy ảnh cũ nếu không upload mới
    if (!empty($id)) {

        $result = mysqli_query($conn, "SELECT hinh_anh, hinh_anh2 FROM banner WHERE id='$id'");
        $row = mysqli_fetch_assoc($result);

        // Giữ ảnh cũ nếu user không chọn ảnh mới
        if (empty($anh1)) $anh1 = $row['hinh_anh'];
        if (empty($anh2)) $anh2 = $row['hinh_anh2'];

        // Câu lệnh UPDATE
        $sql = "UPDATE banner SET 
                    tieu_de='$ten',
                    noi_dung='$mota',
                    link='$link',
                    hinh_anh='$anh1',
                    hinh_anh2='$anh2'
                WHERE id='$id'";

    } else {

        // Câu lệnh INSERT
        $sql = "INSERT INTO banner (tieu_de, noi_dung, link, hinh_anh, hinh_anh2) 
                VALUES ('$ten', '$mota', '$link', '$anh1', '$anh2')";
    }

    // Chạy SQL
    if (mysqli_query($conn, $sql)) {
        header("Location: ../../html/Admin/IndexController.php");
        exit();
    } else {
        echo "❌ Lỗi SQL: " . mysqli_error($conn);
    }
}
?>
