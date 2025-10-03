<?php
include '../../db/db.php';

$uploadDir = "../../uploads/";

// Tạo thư mục uploads nếu chưa tồn tại
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

    if (!empty($id)) {
        // Lấy ảnh cũ nếu không upload mới
        $result = mysqli_query($conn, "SELECT hinh_anh, hinh_anh2 FROM banner WHERE id='$id'");
        $row = mysqli_fetch_assoc($result);

        if (empty($anh1)) $anh1 = $row['hinh_anh'];
        if (empty($anh2)) $anh2 = $row['hinh_anh2'];

        $sql = "UPDATE banner 
                SET tieu_de='$ten', noi_dung='$mota', hinh_anh='$anh1', hinh_anh2='$anh2' 
                WHERE id='$id'";
    } else {
        $sql = "INSERT INTO banner (tieu_de, noi_dung, hinh_anh, hinh_anh2) 
                VALUES ('$ten', '$mota', '$anh1', '$anh2')";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: ../../html/Admin/IndexController.php");
        exit();
    } else {
        echo "Lỗi SQL: " . mysqli_error($conn);
    }
}
?>
