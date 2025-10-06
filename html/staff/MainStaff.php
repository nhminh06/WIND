<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../css/Staff.css">
</head>
<body>
    <div class="main-container">
        <div class="menu">
    <ul>
        <li><a href="StaffProfile.php">Quản lý hồ sơ nhân viên</a></li>
        <li><a href="ShiftAssignment.php">Quản lý ca làm việc & phân công</a></li>
        <li><a href="AttendanceSalary.php">Chấm công, lương thưởng</a></li>
        <li><a href="TourSchedule.php">Quản lý lịch tour</a></li>
        <li><a href="InternalChat.php">Chat/Thông báo nội bộ</a></li>
    </ul>
        </div>
        <div class="content">
            <h1>Chào mừng đến với trang quản lý nhân viên</h1>
        </div>
    </div>
</body>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f8;
        margin: 0;
        padding: 0;
    }
    .main-container {
        display: flex;
        min-height: 100vh;
    }
    .menu {
        width: 260px;
        background: #2c3e50;
        color: #fff;
        padding-top: 40px;
        box-shadow: 2px 0 8px rgba(0,0,0,0.05);
    }
    .menu ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .menu li {
        margin-bottom: 18px;
    }
    .menu a {
        color: #fff;
        text-decoration: none;
        display: block;
        padding: 14px 28px;
        border-radius: 4px;
        transition: background 0.2s;
    }
    .menu a:hover {
        background: #34495e;
    }
    .content {
        flex: 1;
        padding: 60px 40px;
        background: #fff;
        box-shadow: 0 0 12px rgba(44,62,80,0.04);
    }
    .content h1 {
        color: #2c3e50;
        font-size: 2.2em;
        margin-bottom: 20px;
        
    }
</style>
</html> 

