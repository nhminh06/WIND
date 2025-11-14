<?php
session_start();
include('../../db/db.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$staff_id = $_SESSION['user_id'];

// Lấy thông tin nhân viên
$sql_staff = "SELECT * FROM user WHERE id = ?";
$stmt_staff = $conn->prepare($sql_staff);
$stmt_staff->bind_param("i", $staff_id);
$stmt_staff->execute();
$staff_info = $stmt_staff->get_result()->fetch_assoc();
$stmt_staff->close();


// Lấy ca làm việc kèm tour (chỉ từ hôm nay trở đi)
$sql = "
SELECT w.id, w.work_date, w.start_time, w.end_time, w.notes AS shift_notes,
       t.tour_name, t.status AS tour_status, t.notes AS tour_notes,
       t.departure_location, t.destination, t.customer_count
FROM work_shift w
LEFT JOIN tour_schedule t ON w.id = t.shift_id
WHERE w.staff_id = ? AND w.work_date >= CURDATE()
ORDER BY w.work_date ASC, w.start_time ASC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Lỗi chuẩn bị truy vấn: " . $conn->error);
}

$stmt->bind_param("i", $staff_id); // bind user đăng nhập
$stmt->execute();
$result = $stmt->get_result();
$total_shifts = $result->num_rows;

$stmt->bind_param("i", $staff_id);
if (!$stmt->execute()) {
    die("Lỗi thực thi truy vấn: " . $stmt->error);
}

$result = $stmt->get_result();

// Đếm tổng số ca
$total_shifts = $result->num_rows;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ca Làm Việc Của Bạn</title>
  <link rel="stylesheet" href="../../css/Staff.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .main-content {
      padding: 20px;
    }
    .main-title {
      color: #2c3e50;
      margin-bottom: 20px;
      font-weight: bold;
    }
    .info-box {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 20px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .info-box h5 {
      margin: 0;
      font-size: 1.1rem;
    }
    .info-box p {
      margin: 5px 0 0 0;
      font-size: 0.9rem;
      opacity: 0.9;
    }
    .badge-status {
      font-size: 0.8rem;
      padding: 5px 10px;
      font-weight: 600;
    }
    .table thead {
      background: #2c3e50;
      color: white;
    }
    .table tbody tr:hover {
      background-color: #f8f9fa;
      cursor: pointer;
      transition: all 0.2s;
    }
    .tour-details {
      font-size: 0.85rem;
      color: #6c757d;
    }
    .no-data {
      padding: 40px;
      text-align: center;
      color: #6c757d;
    }
    .today-mark {
      background-color: #fff3cd !important;
      font-weight: bold;
    }
    @media (max-width: 768px) {
      .table {
        font-size: 0.85rem;
      }
      .info-box {
        padding: 15px;
      }
    }
  </style>
</head>
<body>
<?php include('menu.php'); ?>

<div class="main-content">
  <!-- Thông tin tổng quan -->
  <div class="info-box">
    <h5><i class="fas fa-user-circle"></i> Xin chào, <?= htmlspecialchars($staff_info['ho_ten'] ?? 'Nhân viên') ?>!</h5>
    <p><i class="fas fa-calendar-check"></i> Bạn có <strong><?= $total_shifts ?></strong> ca làm việc sắp tới</p>
  </div>

  <h2 class="main-title"><i class="fas fa-calendar-alt"></i> Ca Làm Việc & Lịch Tour</h2>
  
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead>
        <tr>
          <th style="width: 12%;"><i class="far fa-calendar"></i> Ngày</th>
          <th style="width: 20%;"><i class="fas fa-route"></i> Tour</th>
          <th style="width: 12%;"><i class="fas fa-traffic-light"></i> Trạng thái</th>
          <th style="width: 10%;"><i class="far fa-clock"></i> Giờ bắt đầu</th>
          <th style="width: 10%;"><i class="far fa-clock"></i> Giờ kết thúc</th>
          <th style="width: 20%;"><i class="fas fa-map-marker-alt"></i> Địa điểm</th>
          <th><i class="fas fa-sticky-note"></i> Ghi chú</th>
        </tr>
      </thead>
      <tbody>
        <?php if($total_shifts > 0): ?>
          <?php 
          $today = date('Y-m-d');
          while($row = $result->fetch_assoc()): 
            $is_today = ($row['work_date'] == $today);
          ?>
            <tr class="<?= $is_today ? 'today-mark' : '' ?>">
              <!-- Ngày -->
              <td class="text-center">
                <strong><?= date('d/m/Y', strtotime($row['work_date'])) ?></strong>
                <?php if($is_today): ?>
                  <br><span class="badge bg-warning text-dark">Hôm nay</span>
                <?php endif; ?>
                <br><small class="text-muted"><?= strftime('%A', strtotime($row['work_date'])) ?></small>
              </td>

              <!-- Tour -->
              <td>
                <?php if($row['tour_name']): ?>
                  <strong><?= htmlspecialchars($row['tour_name']) ?></strong>
                  <?php if($row['customer_count']): ?>
                    <br><small class="tour-details">
                      <i class="fas fa-users"></i> <?= $row['customer_count'] ?> khách
                    </small>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="text-muted">Chưa có tour</span>
                <?php endif; ?>
              </td>

              <!-- Trạng thái -->
              <td class="text-center">
                <?php if($row['tour_status']): ?>
                  <?php
                    $statusClass = '';
                    $statusText = '';
                    $statusIcon = '';
                    switch($row['tour_status']) {
                      case 'scheduled':
                        $statusClass = 'bg-info text-dark';
                        $statusText = 'Đã lên lịch';
                        $statusIcon = 'fa-calendar-check';
                        break;
                      case 'ongoing':
                        $statusClass = 'bg-warning text-dark';
                        $statusText = 'Đang diễn ra';
                        $statusIcon = 'fa-spinner';
                        break;
                      case 'completed':
                        $statusClass = 'bg-success';
                        $statusText = 'Hoàn thành';
                        $statusIcon = 'fa-check-circle';
                        break;
                      case 'cancelled':
                        $statusClass = 'bg-danger';
                        $statusText = 'Đã hủy';
                        $statusIcon = 'fa-times-circle';
                        break;
                      default:
                        $statusClass = 'bg-secondary';
                        $statusText = htmlspecialchars($row['tour_status']);
                        $statusIcon = 'fa-info-circle';
                    }
                  ?>
                  <span class="badge <?= $statusClass ?> badge-status">
                    <i class="fas <?= $statusIcon ?>"></i> <?= $statusText ?>
                  </span>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>

              <!-- Giờ bắt đầu -->
              <td class="text-center">
                <strong><?= date('H:i', strtotime($row['start_time'])) ?></strong>
              </td>

              <!-- Giờ kết thúc -->
              <td class="text-center">
                <strong><?= date('H:i', strtotime($row['end_time'])) ?></strong>
              </td>

              <!-- Địa điểm -->
              <td>
                <?php if($row['departure_location'] || $row['destination']): ?>
                  <small class="tour-details">
                    <?php if($row['departure_location']): ?>
                      <i class="fas fa-dot-circle text-success"></i> <?= htmlspecialchars($row['departure_location']) ?>
                    <?php endif; ?>
                    <?php if($row['destination']): ?>
                      <br><i class="fas fa-map-pin text-danger"></i> <?= htmlspecialchars($row['destination']) ?>
                    <?php endif; ?>
                  </small>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>

              <!-- Ghi chú -->
              <td>
                <?php if($row['shift_notes']): ?>
                  <small><strong>Ca:</strong> <?= htmlspecialchars($row['shift_notes']) ?></small>
                <?php endif; ?>
                <?php if($row['tour_notes']): ?>
                  <br><small><strong>Tour:</strong> <?= htmlspecialchars($row['tour_notes']) ?></small>
                <?php endif; ?>
                <?php if(!$row['shift_notes'] && !$row['tour_notes']): ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="no-data">
              <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
              <p class="mb-0"><em>Chưa có ca làm việc hoặc tour nào trong thời gian tới</em></p>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Tự động làm mới trang mỗi 5 phút
setTimeout(function() {
  location.reload();
}, 300000);
</script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>