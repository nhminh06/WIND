<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thông Báo Nội Bộ</title>
  <link rel="stylesheet" href="../../css/Staff.css">


  <style>
    .main-content {
      margin-left: 250px;
      padding: 40px;
      transition: 0.3s;
    }

    .main-title {
      font-size: 28px;
      font-weight: 600;
      margin-bottom: 25px;
      color: #91aecaff;
      border-left: 5px solid #007bff;
      padding-left: 10px;
    }

    .announcement {
      background: #ffffff;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
      transition: 0.3s;
    }

    .announcement:hover {
      background-color: #f8f9ff;
      transform: scale(1.01);
    }

    .announcement h4 {
      color: #1e3d59;
      font-size: 20px;
      font-weight: 600;
      margin-bottom: 10px;
    }

    .announcement p {
      margin: 5px 0;
      font-size: 16px;
      color: #333;
    }

    .announcement b {
      color: #007bff;
    }

    .no-announcement {
      text-align: center;
      color: #888;
      font-size: 18px;
      margin-top: 50px;
    }
  </style>
</head>
<body>
  <?php include('menu.php'); ?>

  <div class="main-content">
    <h2 class="main-title">📢 Thông Báo Nội Bộ</h2>

    <!-- Thông báo mẫu -->
    <div class="announcement">
      <h4>🧳 Tour Huế 3 ngày – Đổi giờ khởi hành</h4>
      <p><b>Ngày đăng:</b> 18/10/2025</p>
      <p><b>Nội dung:</b> Tour Huế khởi hành lúc <b>05:30 sáng</b> thay vì 06:00 như cũ.</p>
      <p><b>Người đăng:</b> Quản lý tour</p>
    </div>

    <div class="announcement">
      <h4>🎉 Team Building tháng 11</h4>
      <p><b>Ngày đăng:</b> 15/10/2025</p>
      <p><b>Nội dung:</b> Toàn bộ nhân viên đăng ký tham gia Team Building tại Bà Nà Hills trước ngày 25/10.</p>
      <p><b>Người đăng:</b> Phòng nhân sự</p>
    </div>

    <div class="announcement">
      <h4>📅 Họp nội bộ cuối tháng</h4>
      <p><b>Ngày đăng:</b> 10/10/2025</p>
      <p><b>Nội dung:</b> Họp nhanh về chất lượng dịch vụ khách đoàn tại phòng họp tầng 2.</p>
      <p><b>Người đăng:</b> Ban điều hành</p>
    </div>

    <!-- Nếu không có thông báo -->
    <!-- <p class="no-announcement">Hiện chưa có thông báo nào.</p> -->
  </div>
</body>
</html>
