<?php 
session_start();
include('../../db/db.php'); 

// Kiểm tra quyền staff
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'admin')) {
    header('Location: ../../index.php');
    exit();
}

$user_name = $_SESSION['username'] ?? 'Nhân viên';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thông Báo Nội Bộ - WIND</title>
  <link rel="stylesheet" href="../../css/Staff.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../../css/rpstaff.css">

  <style>

  </style>
</head>
<body>
  <?php include('../../includes/Staffnav.php'); ?>

  <div class="main-content">
    <!-- Page Header -->
    <div class="page-header">
      <h1>
        <i class="bi bi-megaphone-fill"></i>
        Thông Báo Nội Bộ
      </h1>
      <p>Cập nhật thông tin và tin tức mới nhất từ công ty</p>
    </div>

    <?php
      // Truy vấn thống kê
      $sql_total = "SELECT COUNT(*) as total FROM announcement";
      $result_total = $conn->query($sql_total);
      $total_announcements = $result_total->fetch_assoc()['total'];

      // Thông báo mới (trong 7 ngày)
      $sql_new = "SELECT COUNT(*) as new_count FROM announcement WHERE post_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
      $result_new = $conn->query($sql_new);
      $new_announcements = $result_new->fetch_assoc()['new_count'];

      // Thông báo tháng này
      $sql_month = "SELECT COUNT(*) as month_count FROM announcement WHERE MONTH(post_date) = MONTH(CURDATE()) AND YEAR(post_date) = YEAR(CURDATE())";
      $result_month = $conn->query($sql_month);
      $month_announcements = $result_month->fetch_assoc()['month_count'];
    ?>

    <!-- Stats Cards -->
    <div class="stats-container">
      <div class="stat-card">
        <div class="stat-icon total">
          <i class="bi bi-file-text"></i>
        </div>
        <div class="stat-info">
          <h3><?php echo $total_announcements; ?></h3>
          <p>Tổng thông báo</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon new">
          <i class="bi bi-bell-fill"></i>
        </div>
        <div class="stat-info">
          <h3><?php echo $new_announcements; ?></h3>
          <p>Thông báo mới (7 ngày)</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon this-month">
          <i class="bi bi-calendar-check"></i>
        </div>
        <div class="stat-info">
          <h3><?php echo $month_announcements; ?></h3>
          <p>Thông báo tháng này</p>
        </div>
      </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
      <label><i class="bi bi-search"></i> Tìm kiếm:</label>
      <input type="text" id="searchInput" placeholder="Tìm kiếm theo tiêu đề hoặc nội dung..." onkeyup="filterAnnouncements()">
    </div>

    <!-- Announcements List -->
    <div class="announcements-container" id="announcementsContainer">
      <?php
        $sql = "SELECT * FROM announcement ORDER BY post_date DESC, created_at DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          $today = date('Y-m-d');
          $seven_days_ago = date('Y-m-d', strtotime('-7 days'));
          
          while($row = $result->fetch_assoc()) {
            $is_new = ($row['post_date'] >= $seven_days_ago);
            
            echo '<div class="announcement-card" data-title="' . htmlspecialchars($row["title"]) . '" data-content="' . htmlspecialchars($row["content"]) . '">';
            
            echo '<div class="announcement-header">';
            echo '<h3 class="announcement-title">';
            echo '<i class="bi bi-bell-fill"></i>';
            echo htmlspecialchars($row["title"]);
            echo '</h3>';
            
            if ($is_new) {
              echo '<span class="badge-new">Mới</span>';
            }
            
            echo '</div>';
            
            echo '<div class="announcement-meta">';
            echo '<span><i class="bi bi-person-circle"></i> <strong>Người đăng:</strong> ' . htmlspecialchars($row["author"]) . '</span>';
            echo '<span><i class="bi bi-calendar-event"></i> <strong>Ngày đăng:</strong> ' . date('d/m/Y', strtotime($row["post_date"])) . '</span>';
            echo '<span><i class="bi bi-clock-history"></i> <strong>Thời gian:</strong> ' . date('H:i d/m/Y', strtotime($row["created_at"])) . '</span>';
            echo '</div>';
            
            echo '<div class="announcement-content">';
            echo nl2br(htmlspecialchars($row["content"]));
            echo '</div>';
            
            echo '</div>';
          }
        } else {
          echo '<div class="empty-state">';
          echo '<i class="bi bi-inbox"></i>';
          echo '<h3>Chưa có thông báo nào</h3>';
          echo '<p>Hiện tại chưa có thông báo nào được gửi đến</p>';
          echo '</div>';
        }

        $conn->close();
      ?>
    </div>
  </div>

  <script>
    // Hàm tìm kiếm thông báo
    function filterAnnouncements() {
      const input = document.getElementById('searchInput');
      const filter = input.value.toLowerCase();
      const container = document.getElementById('announcementsContainer');
      const cards = container.getElementsByClassName('announcement-card');
      
      let visibleCount = 0;
      
      for (let i = 0; i < cards.length; i++) {
        const title = cards[i].getAttribute('data-title').toLowerCase();
        const content = cards[i].getAttribute('data-content').toLowerCase();
        
        if (title.includes(filter) || content.includes(filter)) {
          cards[i].style.display = '';
          visibleCount++;
        } else {
          cards[i].style.display = 'none';
        }
      }
      
      // Hiển thị thông báo không tìm thấy
      const emptyState = container.querySelector('.empty-state');
      if (visibleCount === 0 && cards.length > 0 && filter !== '') {
        if (!emptyState) {
          const noResult = document.createElement('div');
          noResult.className = 'empty-state';
          noResult.id = 'noResultState';
          noResult.innerHTML = `
            <i class="bi bi-search"></i>
            <h3>Không tìm thấy kết quả</h3>
            <p>Không có thông báo nào phù hợp với từ khóa "${input.value}"</p>
          `;
          container.appendChild(noResult);
        }
      } else {
        const noResult = document.getElementById('noResultState');
        if (noResult) {
          noResult.remove();
        }
      }
    }
  </script>
</body>
</html>