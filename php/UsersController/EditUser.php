<?php
session_start();
include '../../db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!isset($_GET['id'])) {
        die("Thiếu ID người dùng");
    }

    $id = intval($_GET['id']);

    $truyvan = "SELECT * FROM user WHERE id = $id";
    $ketqua = mysqli_query($conn, $truyvan);
   $row = mysqli_fetch_assoc($ketqua);

        // Phân tách ngày sinh cũ
        $ngay_sinh_cu = $row['ngay_sinh'] ?? '2000-01-01';
        list($nam_cu, $thang_cu, $ngay_cu) = explode('-', $ngay_sinh_cu);

        $ho_ten    = $_POST['ho_ten'] ?: $row['ho_ten'];
        $gioi_tinh = $_POST['gioi_tinh'] ?: $row['gioi_tinh'];
        $dia_chi   = $_POST['dia_chi'] ?: $row['dia_chi'];
        $email     = $_POST['email'] ?: $row['email'];
        $sdt       = $_POST['sdt'] ?: $row['sdt'];

        $ngay  = !empty($_POST['ngay']) ? intval($_POST['ngay']) : intval($ngay_cu);
        $thang = !empty($_POST['thang']) ? intval($_POST['thang']) : intval($thang_cu);
        $nam   = !empty($_POST['nam']) ? intval($_POST['nam']) : intval($nam_cu);

        $ngay_sinh = sprintf("%04d-%02d-%02d", $nam, $thang, $ngay);


    // Xử lý ngày sinh
    if ($ngay != "" && $thang != "" && $nam != "") {
        $ngay_sinh = "$nam-$thang-$ngay";
    } else {
        $ngay_sinh = null; // hoặc giữ nguyên nếu bạn muốn
    }

    // Cập nhật CSDL bằng prepared statement (an toàn)
    $sql = "UPDATE user 
            SET ho_ten = ?, 
                gioi_tinh = ?, 
                ngay_sinh = ?, 
                dia_chi = ?, 
                email = ?, 
                sdt = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssi",
        $ho_ten,
        $gioi_tinh,
        $ngay_sinh,
        $dia_chi,
        $email,
        $sdt,
        $id
    );

    if ($stmt->execute()) {
       if($_SESSION['role'] == 'admin'){
        header("Location: ../../html/Admin/Adminacc.php?status=success");
       } else {
        header("Location: ../../html/views/user/users.php?status=success");

       }
        exit();
    } else {
        if($_SESSION['role'] == 'admin'){
        header("Location: ../../html/Admin/Adminacc.php?status=error");
       } else {
        header("Location: ../views/user/users.php?status=error");

       }
        exit();
    }
}
?>
