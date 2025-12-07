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

  <style>
    .main-content {
      margin-left: 250px;
      padding: 30px 40px;
      background: #f5f7fa;
      min-height: 100vh;
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding: 20px;
      }
    }

    /* Header Section */
    .page-header {
      background: #28a745;
      color: white;
      padding: 40px;
      border-radius: 12px;
      margin-bottom: 35px;
      box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);
    }

    .page-header h1 {
      margin: 0 0 10px 0;
      font-size: 32px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .page-header p {
      margin: 0;
      font-size: 16px;
    }

    /* Stats Section */
    .stats-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat-card {
      background: white;
      padding: 25px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      gap: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .stat-icon {
      width: 60px;
      height: 60px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      color: white;
    }

    .stat-icon.total {
      background: #28a745;
    }

    .stat-icon.new {
      background: #20c997;
    }

    .stat-icon.this-month {
      background: #17a2b8;
    }

    .stat-info h3 {
      margin: 0 0 5px 0;
      font-size: 28px;
      font-weight: 700;
      color: #333;
    }

    .stat-info p {
      margin: 0;
      color: #666;
      font-size: 14px;
    }

    /* Filter Section */
    .filter-section {
      background: white;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 25px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      display: flex;
      align-items: center;
      gap: 15px;
      flex-wrap: wrap;
    }

    .filter-section label {
      font-weight: 500;
      color: #333;
    }

    .filter-section input {
      padding: 10px 15px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      font-size: 14px;
      flex: 1;
      min-width: 250px;
    }

    .filter-section input:focus {
      outline: none;
      border-color: #28a745;
    }

    /* Announcement Cards */
    .announcements-container {
      display: grid;
      gap: 20px;
    }

    .announcement-card {
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      border-left: 5px solid #28a745;
    }

    .announcement-header {
      display: flex;
      justify-content: space-between;
      align-items: start;
      margin-bottom: 20px;
      gap: 15px;
    }

    .announcement-title {
      font-size: 22px;
      font-weight: 600;
      color: #1e3d59;
      margin: 0 0 10px 0;
      display: flex;
      align-items: center;
      gap: 10px;
      flex: 1;
    }

    .announcement-title i {
      color: #28a745;
      font-size: 24px;
    }

    .badge-new {
      background: #28a745;
      color: white;
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .announcement-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      color: #666;
      font-size: 14px;
      margin-bottom: 15px;
      padding-bottom: 15px;
      border-bottom: 1px solid #f0f0f0;
    }

    .announcement-meta span {
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .announcement-meta i {
      color: #28a745;
    }

    .announcement-content {
      color: #555;
      line-height: 1.8;
      font-size: 15px;
      white-space: pre-wrap;
      word-wrap: break-word;
    }

    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 80px 20px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .empty-state i {
      font-size: 80px;
      color: #ddd;
      margin-bottom: 20px;
    }

    .empty-state h3 {
      font-size: 24px;
      color: #666;
      margin: 0 0 10px 0;
    }

    .empty-state p {
      color: #999;
      font-size: 16px;
      margin: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .page-header h1 {
        font-size: 24px;
      }

      .announcement-header {
        flex-direction: column;
      }

      .announcement-meta {
        flex-direction: column;
        gap: 10px;
      }

      .stats-container {
        grid-template-columns: 1fr;
      }
    }
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