<?php include('../../db/db.php'); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thông Báo Nội Bộ</title>
  <link rel="stylesheet" href="../../css/Staff.css">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

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
  <?php include('../../includes/Staffnav.php'); ?>

  <div class="main-content">
    <h2 class="main-title">📢 Thông Báo Nội Bộ</h2>

    <?php
      $sql = "SELECT * FROM announcement ORDER BY post_date DESC";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          echo '<div class="announcement">';
          echo '<h4>' . htmlspecialchars($row["title"]) . '</h4>';
          echo '<p><b>Ngày đăng:</b> ' . date('d/m/Y', strtotime($row["post_date"])) . '</p>';
          echo '<p><b>Nội dung:</b> ' . htmlspecialchars($row["content"]) . '</p>';
          echo '<p><b>Người đăng:</b> ' . htmlspecialchars($row["author"]) . '</p>';
          echo '</div>';
        }
      } else {
        echo '<p class="no-announcement">Hiện chưa có thông báo nào.</p>';
      }

      $conn->close();
    ?>
  </div>
</body>
</html>
