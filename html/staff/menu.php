<?php
include ('../../db/db.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index/login.php");
    exit();
}

if ($_SESSION['role'] !== 'staff') {
    echo "Bạn không có quyền truy cập!";
    exit();
}

$staff_id = $_SESSION['user_id']; // Lấy ID từ session

$sql = "SELECT avatar AS staff_avatar, ho_ten, position, about FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$staff = $stmt->get_result()->fetch_assoc();
?>

<div class="sidebar">
    <div class="logo-staff" style="display: flex; align-items: center;">
       <a href=""><img style="width:70px;" src="../../img/logo.png" alt=""></a>
    </div>
    <img src="<?= htmlspecialchars($staff['staff_avatar']) ?>" alt="Staff" class="staff-avatar">
    <h3>Hi! : <?= htmlspecialchars($staff['ho_ten']); ?></h3>
    <a href="StaffProfile.php" class="menu-item">Hồ sơ nhân viên</a>
    <a href="work_shift.php" class="menu-item">Ca làm việc</a>
    <a href="TourSchedule.php" class="menu-item">Lịch tour</a>
    <a href="InternalChat.php" class="menu-item">Thông báo nội bộ</a>
    <a onclick="window.location.href='../../php/logout.php'" class="menu-item">Đăng xuất</a>
</div>
