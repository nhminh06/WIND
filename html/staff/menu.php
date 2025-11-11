<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// kiểm tra chưa login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index/login.php");
    exit();
}

// kiểm tra role nhân viên
if ($_SESSION['role'] !== 'staff') {
    echo "Bạn không có quyền truy cập!";
    exit();
}
?>
<div class="sidebar">
        <div class="logo-staff" style="display: flex; align-items: center;">
       <a href=""><img style="width:70px;" src="../../img/logo.png" alt=""></a>
    </div>
        <img src="https://via.placeholder.com/80" alt="Staff" class="staff-avatar">
        <h3>Hi! : <?php echo $_SESSION['username']; ?></h3>
        <a href="StaffProfile.php" class="menu-item">Hồ sơ nhân viên</a>
        <a href="ShiftAssignment.php" class="menu-item">Ca làm việc</a>
        <a href="TourSchedule.php" class="menu-item">Lịch tour</a>
        <a href="InternalChat.php" class="menu-item">Thông báo nội bộ</a>
        <a href="LeaveRequest.php" class="menu-item">Xin nghỉ / Báo ốm</a>
        <a onclick="window.location.href='../../php/logout.php'" class="menu-item">Đăng xuất</a>
    </div>