<?php
session_start();
require_once "../google-api/autoload.php";
require_once "../db/db.php";  // Kết nối DB

if (!isset($_GET['code'])) {
    header("Location: google-login.php");
    exit();
}

$code = $_GET['code'];

// 1️⃣ Lấy access_token
$tokenData = getGoogleToken($code);
if (!isset($tokenData['access_token'])) {
    die("Lỗi: không lấy được access_token. Kiểm tra GOOGLE_REDIRECT_URI và client_id/client_secret.");
}
$access_token = $tokenData['access_token'];

// 2️⃣ Lấy thông tin user
$userInfo = getGoogleUser($access_token);
if (!isset($userInfo['email'])) {
    die("Lỗi: không lấy được email từ Google.");
}

$email = $userInfo["email"];
$hoTen = $userInfo["name"] ?? $userInfo["given_name"] ?? explode("@", $email)[0];
$googleAvatar = $userInfo["picture"] ?? null;

// 3️⃣ Lưu avatar về server
$avatarPath = "img/avatamacdinh.png"; // avatar mặc định
if ($googleAvatar) {
    // Lấy nội dung ảnh từ Google
    $avatarContent = file_get_contents($googleAvatar);
    if ($avatarContent) {
        // Tạo tên file duy nhất
        $ext = pathinfo(parse_url($googleAvatar, PHP_URL_PATH), PATHINFO_EXTENSION);
        if (!$ext) $ext = "jpg";
        $fileName = "avatar_".time()."_".rand(1000,9999).".".$ext;
        $folder = "../uploads/avatar/";
        if (!file_exists($folder)) mkdir($folder, 0777, true); // tạo folder nếu chưa có
        $filePath = $folder.$fileName;
        file_put_contents($filePath, $avatarContent);
        $avatarPath = "uploads/avatar/".$fileName; // đường dẫn lưu vào DB
    }
}

// 4️⃣ Kiểm tra user trong DB
$sql = "SELECT * FROM user WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Tạo user mới với avatar
    $sql2 = "INSERT INTO user (ho_ten, email, password, role, trang_thai, avatar)
             VALUES (?, ?, '', 'user', 1, ?)";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("sss", $hoTen, $email, $avatarPath);
    $stmt2->execute();
    $user_id = $stmt2->insert_id;
} else {
    // Nếu user đã có, cập nhật avatar mới
    $user = $result->fetch_assoc();
    $user_id = $user["id"];
    if ($googleAvatar) {
        $sqlUpd = "UPDATE user SET avatar = ? WHERE id = ?";
        $stmtUpd = $conn->prepare($sqlUpd);
        $stmtUpd->bind_param("si", $avatarPath, $user_id);
        $stmtUpd->execute();
    }
}

// 5️⃣ Lấy lại user đầy đủ
$sql3 = "SELECT * FROM user WHERE id = ?";
$stmt3 = $conn->prepare($sql3);
$stmt3->bind_param("i", $user_id);
$stmt3->execute();
$user = $stmt3->get_result()->fetch_assoc();

// 6️⃣ Set SESSION
$_SESSION["user_id"] = $user["id"];
$_SESSION["email"] = $user["email"];
$_SESSION["username"] = $user["ho_ten"];
$_SESSION["role"] = $user["role"];
$_SESSION["avatar"] = $user["avatar"]; 
$_SESSION["logged_in"] = true;
$_SESSION["login_method"] = "google";

// 7️⃣ Redirect về trang chủ
header("Location: ../html/views/index/Webindex.php");
exit();
