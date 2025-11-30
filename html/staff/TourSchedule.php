<?php
session_start();
include('menu.php'); 
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

// Xử lý tìm kiếm
$search_keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$search_date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$search_status = isset($_GET['status']) ? $_GET['status'] : '';
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'upcoming'; // upcoming hoặc history

// Lấy danh sách tour sắp tới (từ hôm nay trở đi)
$sql_upcoming = "
SELECT 
  t.id,
  t.tour_name,
  t.status AS tour_status,
  t.departure_location,
  t.destination,
  t.customer_count,
  t.notes AS tour_notes,
  w.work_date,
  w.start_time,
  w.end_time,
  w.notes AS shift_notes
FROM tour_schedule t
INNER JOIN work_shift w ON t.shift_id = w.id
WHERE w.staff_id = ? AND w.work_date >= CURDATE()
";

$params_upcoming = [$staff_id];
$types_upcoming = "i";

if ($search_keyword) {
    $sql_upcoming .= " AND (t.tour_name LIKE ? OR t.departure_location LIKE ? OR t.destination LIKE ?)";
    $search_param = "%$search_keyword%";
    $params_upcoming[] = $search_param;
    $params_upcoming[] = $search_param;
    $params_upcoming[] = $search_param;
    $types_upcoming .= "sss";
}

if ($search_status) {
    $sql_upcoming .= " AND t.status = ?";
    $params_upcoming[] = $search_status;
    $types_upcoming .= "s";
}

$sql_upcoming .= " ORDER BY w.work_date ASC, w.start_time ASC";

$stmt_upcoming = $conn->prepare($sql_upcoming);
$stmt_upcoming->bind_param($types_upcoming, ...$params_upcoming);
$stmt_upcoming->execute();
$result_upcoming = $stmt_upcoming->get_result();
$total_tours = $result_upcoming->num_rows;

// Lấy TẤT CẢ lịch sử làm việc
$sql_history = "
SELECT 
  t.id,
  t.tour_name,
  t.status AS tour_status,
  t.departure_location,
  t.destination,
  t.customer_count,
  t.notes AS tour_notes,
  w.work_date,
  w.start_time,
  w.end_time,
  w.notes AS shift_notes
FROM tour_schedule t
INNER JOIN work_shift w ON t.shift_id = w.id
WHERE w.staff_id = ? AND w.work_date < CURDATE()
";

$params_history = [$staff_id];
$types_history = "i";

if ($search_keyword) {
    $sql_history .= " AND (t.tour_name LIKE ? OR t.departure_location LIKE ? OR t.destination LIKE ?)";
    $search_param = "%$search_keyword%";
    $params_history[] = $search_param;
    $params_history[] = $search_param;
    $params_history[] = $search_param;
    $types_history .= "sss";
}

if ($search_date_from) {
    $sql_history .= " AND w.work_date >= ?";
    $params_history[] = $search_date_from;
    $types_history .= "s";
}

if ($search_date_to) {
    $sql_history .= " AND w.work_date <= ?";
    $params_history[] = $search_date_to;
    $types_history .= "s";
}

if ($search_status) {
    $sql_history .= " AND t.status = ?";
    $params_history[] = $search_status;
    $types_history .= "s";
}

$sql_history .= " ORDER BY w.work_date DESC, w.start_time DESC";

$stmt_history = $conn->prepare($sql_history);
$stmt_history->bind_param($types_history, ...$params_history);
$stmt_history->execute();
$result_history = $stmt_history->get_result();
$total_history = $result_history->num_rows;

// Thống kê
$sql_stats = "SELECT COUNT(*) as total_completed FROM tour_schedule t INNER JOIN work_shift w ON t.shift_id = w.id WHERE w.staff_id = ? AND w.work_date < CURDATE()";
$stmt_stats = $conn->prepare($sql_stats);
$stmt_stats->bind_param("i", $staff_id);
$stmt_stats->execute();
$stats = $stmt_stats->get_result()->fetch_assoc();
$stmt_stats->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lịch Tour Của Tôi</title>
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
    .tour-card {
      background: white;
      border-left: 4px solid #667eea;
      padding: 15px;
      margin-bottom: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .search-box {
      background: white;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .search-active-alert {
      background: #d4edda;
      border: 1px solid #c3e6cb;
      color: #155724;
      padding: 10px 15px;
      border-radius: 5px;
      margin-top: 10px;
      font-size: 0.9rem;
    }
    .nav-tabs .nav-link {
      color: #495057;
      font-weight: 500;
    }
    .nav-tabs .nav-link.active {
      background: #667eea;
      color: white;
      border-color: #667eea;
    }
    .stat-badge {
      background: rgba(255,255,255,0.2);
      padding: 5px 15px;
      border-radius: 20px;
      display: inline-block;
      margin-right: 10px;
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

<div class="main-content">
  <!-- Thông tin tổng quan -->
  <div class="info-box">
    <h5><i class="fas fa-user-circle"></i> Xin chào, <?= htmlspecialchars($staff_info['ho_ten'] ?? 'Nhân viên') ?>!</h5>
    <p>
      <span class="stat-badge"><i class="fas fa-route"></i> <?= $total_tours ?> Tour sắp tới</span>
      <span class="stat-badge"><i class="fas fa-check-circle"></i> <?= $stats['total_completed'] ?> Lịch sử tour</span>
    </p>
  </div>

  <h2 class="main-title"><i class="fas fa-map-marked-alt"></i> Lịch Tour Của Tôi</h2>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs mb-3" id="tourTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link <?= $tab == 'upcoming' ? 'active' : '' ?>" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab">
        <i class="fas fa-calendar-alt"></i> Tour sắp tới (<?= $total_tours ?>)
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link <?= $tab == 'history' ? 'active' : '' ?>" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
        <i class="fas fa-history"></i> Lịch sử (<?= $total_history ?>)
      </button>
    </li>
  </ul>

  <!-- Tab content -->
  <div class="tab-content" id="tourTabContent">
    <!-- Tour sắp tới -->
    <div class="tab-pane fade <?= $tab == 'upcoming' ? 'show active' : '' ?>" id="upcoming" role="tabpanel">
      
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead>
            <tr>
              <th style="width: 12%;"><i class="far fa-calendar"></i> Ngày</th>
              <th style="width: 20%;"><i class="fas fa-route"></i> Tên Tour</th>
              <th style="width: 12%;"><i class="fas fa-traffic-light"></i> Trạng thái</th>
              <th style="width: 10%;"><i class="far fa-clock"></i> Giờ bắt đầu</th>
              <th style="width: 10%;"><i class="far fa-clock"></i> Giờ kết thúc</th>
              <th style="width: 12%;"><i class="fas fa-users"></i> Số khách</th>
              <th><i class="fas fa-map-marker-alt"></i> Địa điểm</th>
              <th><i class="fas fa-sticky-note"></i> Ghi chú</th>
            </tr>
          </thead>
          <tbody>
            <?php if($total_tours > 0): ?>
              <?php 
              $today = date('Y-m-d');
              while($row = $result_upcoming->fetch_assoc()): 
                $is_today = ($row['work_date'] == $today);
              ?>
                <tr class="<?= $is_today ? 'today-mark' : '' ?>">
                  <td class="text-center">
                    <strong><?= date('d/m/Y', strtotime($row['work_date'])) ?></strong>
                    <?php if($is_today): ?>
                      <br><span class="badge bg-warning text-dark">Hôm nay</span>
                    <?php endif; ?>
                  </td>
                  <td><strong><?= htmlspecialchars($row['tour_name']) ?></strong></td>
                  <td class="text-center">
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
                  </td>
                  <td class="text-center"><strong><?= date('H:i', strtotime($row['start_time'])) ?></strong></td>
                  <td class="text-center"><strong><?= date('H:i', strtotime($row['end_time'])) ?></strong></td>
                  <td class="text-center">
                    <?php if($row['customer_count']): ?>
                      <span class="badge bg-primary">
                        <i class="fas fa-users"></i> <?= $row['customer_count'] ?> khách
                      </span>
                    <?php else: ?>
                      <span class="text-muted">Chưa có</span>
                    <?php endif; ?>
                  </td>
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
                <td colspan="8" class="no-data">
                  <i class="fas fa-route fa-3x text-muted mb-3"></i>
                  <p class="mb-0"><em>Không tìm thấy tour nào</em></p>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Lịch sử -->
    <div class="tab-pane fade <?= $tab == 'history' ? 'show active' : '' ?>" id="history" role="tabpanel">
      <!-- Form tìm kiếm cho lịch sử -->
      <div class="search-box">
        <h6><i class="fas fa-search"></i> Tìm kiếm lịch sử tour</h6>
        <form method="GET" class="row g-3">
          <input type="hidden" name="tab" value="history">
          <div class="col-md-3">
            <input type="text" class="form-control" name="search" placeholder="Tên tour, địa điểm..." value="<?= htmlspecialchars($search_keyword) ?>">
          </div>
          <div class="col-md-2">
            <input type="date" class="form-control" name="date_from" placeholder="Từ ngày" value="<?= $search_date_from ?>">
          </div>
          <div class="col-md-2">
            <input type="date" class="form-control" name="date_to" placeholder="Đến ngày" value="<?= $search_date_to ?>">
          </div>
          <div class="col-md-2">
            <select class="form-select" name="status">
              <option value="">Tất cả trạng thái</option>
              <option value="scheduled" <?= $search_status == 'scheduled' ? 'selected' : '' ?>>Đã lên lịch</option>
              <option value="ongoing" <?= $search_status == 'ongoing' ? 'selected' : '' ?>>Đang diễn ra</option>
              <option value="completed" <?= $search_status == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
              <option value="cancelled" <?= $search_status == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
            </select>
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tìm kiếm</button>
            <a href="?tab=history" class="btn btn-secondary"><i class="fas fa-redo"></i> Làm mới</a>
          </div>
        </form>
        <?php if ($tab == 'history' && ($search_keyword || $search_date_from || $search_date_to || $search_status)): ?>
          <div class="search-active-alert">
            <i class="fas fa-filter"></i> Đang lọc: 
            <?php if ($search_keyword): ?>
              <strong>"<?= htmlspecialchars($search_keyword) ?>"</strong>
            <?php endif; ?>
            <?php if ($search_date_from): ?>
              | Từ <strong><?= date('d/m/Y', strtotime($search_date_from)) ?></strong>
            <?php endif; ?>
            <?php if ($search_date_to): ?>
              đến <strong><?= date('d/m/Y', strtotime($search_date_to)) ?></strong>
            <?php endif; ?>
            <?php if ($search_status): ?>
              | Trạng thái: <strong><?= $search_status ?></strong>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead>
            <tr>
              <th style="width: 12%;"><i class="far fa-calendar"></i> Ngày</th>
              <th style="width: 20%;"><i class="fas fa-route"></i> Tên Tour</th>
              <th style="width: 12%;"><i class="fas fa-traffic-light"></i> Trạng thái</th>
              <th style="width: 12%;"><i class="far fa-clock"></i> Giờ làm</th>
              <th style="width: 12%;"><i class="fas fa-users"></i> Số khách</th>
              <th><i class="fas fa-map-marker-alt"></i> Địa điểm</th>
              <th><i class="fas fa-sticky-note"></i> Ghi chú</th>
            </tr>
          </thead>
          <tbody>
            <?php if($total_history > 0): ?>
              <?php while($row = $result_history->fetch_assoc()): ?>
                <tr>
                  <td class="text-center">
                    <strong><?= date('d/m/Y', strtotime($row['work_date'])) ?></strong>
                  </td>
                  <td><strong><?= htmlspecialchars($row['tour_name']) ?></strong></td>
                  <td class="text-center">
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
                  </td>
                  <td class="text-center">
                    <strong><?= date('H:i', strtotime($row['start_time'])) ?> - <?= date('H:i', strtotime($row['end_time'])) ?></strong>
                  </td>
                  <td class="text-center">
                    <?php if($row['customer_count']): ?>
                      <span class="badge bg-primary">
                        <i class="fas fa-users"></i> <?= $row['customer_count'] ?> khách
                      </span>
                    <?php else: ?>
                      <span class="text-muted">Chưa có</span>
                    <?php endif; ?>
                  </td>
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
                  <i class="fas fa-history fa-3x text-muted mb-3"></i>
                  <p class="mb-0"><em>Không tìm thấy lịch sử tour nào</em></p>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Giữ tab active sau khi tìm kiếm
document.addEventListener('DOMContentLoaded', function() {
  const urlParams = new URLSearchParams(window.location.search);
  const tab = urlParams.get('tab');
  if (tab === 'history') {
    document.getElementById('history-tab').click();
  }
});

// Tự động làm mới trang mỗi 5 phút
setTimeout(function() {
  location.reload();
}, 300000);
</script>
</body>
</html>
<?php
$stmt_upcoming->close();
$stmt_history->close();
$conn->close();
?>