<?php
session_start();
require_once "../../db/db.php"; // File kết nối CSDL

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Lấy dữ liệu từ form
    $ho_ten   = trim($_POST['ho_ten']);
    $email    = trim($_POST['email']);
    $sdt      = trim($_POST['sdt']);
    $password = trim($_POST['password']);
    $role     = trim($_POST['role']);
    $gioi_tinh = isset($_POST['gioi_tinh']) ? $_POST['gioi_tinh'] : null;
    $ngay_sinh = isset($_POST['ngay_sinh']) ? $_POST['ngay_sinh'] : null;
    $dia_chi = isset($_POST['dia_chi']) ? $_POST['dia_chi'] : null;
    $trang_thai = isset($_POST['trang_thai']) ? 1 : 0;

    // -----------------------------------------------------
    // 1. Kiểm tra dữ liệu bắt buộc
    // -----------------------------------------------------
    if ($ho_ten == "" || $email == "" || $password == "" || $role == "") {
        $_SESSION['error'] = "⚠️ Vui lòng điền đầy đủ các trường bắt buộc!";
        header("Location: ../views/add_user.php");
        exit();
    }

    // -----------------------------------------------------
    // 2. Kiểm tra email trùng
    // -----------------------------------------------------
    $checkEmail = $conn->prepare("SELECT id FROM user WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        $_SESSION['error'] = "❌ Email đã tồn tại, vui lòng dùng email khác!";
        header("Location: ../views/add_user.php");
        exit();
    }

    // -----------------------------------------------------
    // 3. Hash mật khẩu
    // -----------------------------------------------------
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // -----------------------------------------------------
    // 4. Insert vào DB
    // -----------------------------------------------------
    $query = $conn->prepare("
        INSERT INTO user (ho_ten, email, sdt, password, role, gioi_tinh, ngay_sinh, dia_chi, trang_thai)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $query->bind_param(
        "ssssssssi",
        $ho_ten,
        $email,
        $sdt,
        $hashedPassword,
        $role,
        $gioi_tinh,
        $ngay_sinh,
        $dia_chi,
        $trang_thai
    );

    if ($query->execute()) {
        $_SESSION['success'] = "✅ Thêm tài khoản thành công!";
        header("Location: ../../html/Admin/UserController.php");
        exit();
    } else {
        $_SESSION['error'] = "❌ Lỗi khi thêm người dùng: " . $query->error;
        header("Location: ../../html/Admin/add_user.php");
        exit();
    }
}
?>
