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

// Xử lý tìm kiếm
$search_keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$search_date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$search_status = isset($_GET['status']) ? $_GET['status'] : '';
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'upcoming';

// =====================================================
// Lấy danh sách tour SẮP TỚI từ dat_tour
// =====================================================
$sql_upcoming = "
SELECT 
  dt.tour_id,
  dt.ngay_khoi_hanh,
  dt.trang_thai_chuyen_di,
  dt.thoi_gian_bat_dau_chuyen_di,
  dt.thoi_gian_ket_thuc_chuyen_di,
  t.ten_tour,
  t.so_ngay,
  t.hinh_anh,
  COUNT(DISTINCT dt.id) as total_bookings,
  SUM(dt.so_nguoi_lon + dt.so_tre_em + dt.so_tre_nho) as total_customers,
  SUM(CASE WHEN dt.trang_thai = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings
FROM dat_tour dt
INNER JOIN tour t ON dt.tour_id = t.id
WHERE dt.huong_dan_vien_id = ? 
  AND dt.ngay_khoi_hanh >= CURDATE()
";

$params_upcoming = [$staff_id];
$types_upcoming = "i";

if ($search_keyword) {
    $sql_upcoming .= " AND t.ten_tour LIKE ?";
    $search_param = "%$search_keyword%";
    $params_upcoming[] = $search_param;
    $types_upcoming .= "s";
}

if ($search_status) {
    $sql_upcoming .= " AND dt.trang_thai_chuyen_di = ?";
    $params_upcoming[] = $search_status;
    $types_upcoming .= "s";
}

$sql_upcoming .= " GROUP BY dt.tour_id, dt.ngay_khoi_hanh ORDER BY dt.ngay_khoi_hanh ASC";

$stmt_upcoming = $conn->prepare($sql_upcoming);
$stmt_upcoming->bind_param($types_upcoming, ...$params_upcoming);
$stmt_upcoming->execute();
$result_upcoming = $stmt_upcoming->get_result();
$total_tours = $result_upcoming->num_rows;

// =====================================================
// Lấy LỊCH SỬ tour từ dat_tour
// =====================================================
$sql_history = "
SELECT 
  dt.tour_id,
  dt.ngay_khoi_hanh,
  dt.trang_thai_chuyen_di,
  dt.thoi_gian_bat_dau_chuyen_di,
  dt.thoi_gian_ket_thuc_chuyen_di,
  t.ten_tour,
  t.so_ngay,
  t.hinh_anh,
  COUNT(DISTINCT dt.id) as total_bookings,
  SUM(dt.so_nguoi_lon + dt.so_tre_em + dt.so_tre_nho) as total_customers,
  SUM(CASE WHEN dt.trang_thai = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings
FROM dat_tour dt
INNER JOIN tour t ON dt.tour_id = t.id
WHERE dt.huong_dan_vien_id = ? 
  AND dt.ngay_khoi_hanh < CURDATE()
";

$params_history = [$staff_id];
$types_history = "i";

if ($search_keyword) {
    $sql_history .= " AND t.ten_tour LIKE ?";
    $search_param = "%$search_keyword%";
    $params_history[] = $search_param;
    $types_history .= "s";
}

if ($search_date_from) {
    $sql_history .= " AND dt.ngay_khoi_hanh >= ?";
    $params_history[] = $search_date_from;
    $types_history .= "s";
}

if ($search_date_to) {
    $sql_history .= " AND dt.ngay_khoi_hanh <= ?";
    $params_history[] = $search_date_to;
    $types_history .= "s";
}

if ($search_status) {
    $sql_history .= " AND dt.trang_thai_chuyen_di = ?";
    $params_history[] = $search_status;
    $types_history .= "s";
}

$sql_history .= " GROUP BY dt.tour_id, dt.ngay_khoi_hanh ORDER BY dt.ngay_khoi_hanh DESC";

$stmt_history = $conn->prepare($sql_history);
$stmt_history->bind_param($types_history, ...$params_history);
$stmt_history->execute();
$result_history = $stmt_history->get_result();
$total_history = $result_history->num_rows;

// Thống kê
$sql_stats = "
SELECT 
  COUNT(DISTINCT CONCAT(tour_id, '-', ngay_khoi_hanh)) as total_completed,
  SUM(so_nguoi_lon + so_tre_em + so_tre_nho) as total_customers_served
FROM dat_tour 
WHERE huong_dan_vien_id = ? 
  AND ngay_khoi_hanh < CURDATE()
  AND trang_thai_chuyen_di = 'completed'
";
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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    .main-content {
      padding: 20px;
    }
    
  </style>
</head>
<body>
<?php include('../../includes/Staffnav.php');  ?>
<div class="main-content">
  <!-- Thông tin tổng quan -->
  <div class="info-box">
    <h5><i class="fas fa-user-circle"></i> Xin chào, <?= htmlspecialchars($staff_info['ho_ten'] ?? 'Nhân viên') ?>!</h5>
    <p>
      <span class="stat-badge"><i class="fas fa-route"></i> <?= $total_tours ?> Tour sắp tới</span>
      <span class="stat-badge"><i class="fas fa-check-circle"></i> <?= $stats['total_completed'] ?> Tour đã hoàn thành</span>
      <span class="stat-badge"><i class="fas fa-users"></i> <?= $stats['total_customers_served'] ?? 0 ?> Khách đã phục vụ</span>
    </p>
  </div>

  <h2 class="main-title"><i class="fas fa-map-marked-alt"></i> Lịch Tour Được Gán</h2>

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
              <th style="width: 80px;"><i class="fas fa-image"></i></th>
              <th style="width: 12%;"><i class="far fa-calendar"></i> Ngày khởi hành</th>
              <th><i class="fas fa-route"></i> Tên Tour</th>
              <th style="width: 10%;"><i class="fas fa-clock"></i> Số ngày</th>
              <th style="width: 12%;"><i class="fas fa-traffic-light"></i> Trạng thái</th>
              <th style="width: 10%;"><i class="fas fa-users"></i> Khách hàng</th>
              <th style="width: 10%;"><i class="fas fa-ticket-alt"></i> Booking</th>
              <th style="width: 10%;"><i class="fas fa-eye"></i> Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <?php if($total_tours > 0): ?>
              <?php 
              $today = date('Y-m-d');
              while($row = $result_upcoming->fetch_assoc()): 
                $is_today = ($row['ngay_khoi_hanh'] == $today);
              ?>
                <tr class="<?= $is_today ? 'today-mark' : '' ?>">
                  <td class="text-center">
                    <img src="../../uploads/<?= htmlspecialchars($row['hinh_anh']) ?>" 
                         alt="Tour" 
                         class="tour-image"
                         onerror="this.src='https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=200'">
                  </td>
                  <td class="text-center">
                    <strong><?= date('d/m/Y', strtotime($row['ngay_khoi_hanh'])) ?></strong>
                    <?php if($is_today): ?>
                      <br><span class="badge bg-warning text-dark">Hôm nay</span>
                    <?php endif; ?>
                  </td>
                  <td><strong><?= htmlspecialchars($row['ten_tour']) ?></strong></td>
                  <td class="text-center">
                    <span class="badge bg-info text-dark">
                      <i class="fas fa-calendar-days"></i> <?= $row['so_ngay'] ?> ngày
                    </span>
                  </td>
                  <td class="text-center">
                    <?php
                      $statusClass = '';
                      $statusText = '';
                      $statusIcon = '';
                      switch($row['trang_thai_chuyen_di']) {
                        case 'preparing':
                          $statusClass = 'bg-secondary';
                          $statusText = 'Chuẩn bị';
                          $statusIcon = 'fa-hourglass-half';
                          break;
                        case 'started':
                          $statusClass = 'bg-warning text-dark';
                          $statusText = 'Đang diễn ra';
                          $statusIcon = 'fa-play-circle';
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
                          $statusText = 'Chuẩn bị';
                          $statusIcon = 'fa-hourglass-half';
                      }
                    ?>
                    <span class="badge <?= $statusClass ?> badge-status">
                      <i class="fas <?= $statusIcon ?>"></i> <?= $statusText ?>
                    </span>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-primary">
                      <i class="fas fa-users"></i> <?= $row['total_customers'] ?> người
                    </span>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-success">
                      <i class="fas fa-check"></i> <?= $row['confirmed_bookings'] ?>
                    </span>
                    <span class="badge bg-secondary">
                      <i class="fas fa-list"></i> <?= $row['total_bookings'] ?>
                    </span>
                  </td>
                  <td class="text-center">
                    <a href="staff_trip_detail.php?tour=<?= $row['tour_id'] ?>&departure=<?= $row['ngay_khoi_hanh'] ?>" 
                       class="btn btn-sm btn-primary view-detail-btn">
                      <i class="fas fa-eye"></i> Xem chi tiết
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" class="no-data">
                  <i class="fas fa-route fa-3x text-muted mb-3"></i>
                  <p class="mb-0"><em>Chưa có tour nào được gán cho bạn</em></p>
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
            <input type="text" class="form-control" name="search" placeholder="Tên tour..." value="<?= htmlspecialchars($search_keyword) ?>">
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
              <option value="preparing" <?= $search_status == 'preparing' ? 'selected' : '' ?>>Chuẩn bị</option>
              <option value="started" <?= $search_status == 'started' ? 'selected' : '' ?>>Đang diễn ra</option>
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
              <th style="width: 80px;"><i class="fas fa-image"></i></th>
              <th style="width: 12%;"><i class="far fa-calendar"></i> Ngày khởi hành</th>
              <th><i class="fas fa-route"></i> Tên Tour</th>
              <th style="width: 10%;"><i class="fas fa-clock"></i> Số ngày</th>
              <th style="width: 12%;"><i class="fas fa-traffic-light"></i> Trạng thái</th>
              <th style="width: 10%;"><i class="fas fa-users"></i> Khách hàng</th>
              <th style="width: 10%;"><i class="fas fa-ticket-alt"></i> Booking</th>
              <th style="width: 10%;"><i class="fas fa-eye"></i> Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <?php if($total_history > 0): ?>
              <?php while($row = $result_history->fetch_assoc()): ?>
                <tr>
                  <td class="text-center">
                    <img src="../../uploads/<?= htmlspecialchars($row['hinh_anh']) ?>" 
                         alt="Tour" 
                         class="tour-image"
                         onerror="this.src='https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=200'">
                  </td>
                  <td class="text-center">
                    <strong><?= date('d/m/Y', strtotime($row['ngay_khoi_hanh'])) ?></strong>
                  </td>
                  <td><strong><?= htmlspecialchars($row['ten_tour']) ?></strong></td>
                  <td class="text-center">
                    <span class="badge bg-info text-dark">
                      <i class="fas fa-calendar-days"></i> <?= $row['so_ngay'] ?> ngày
                    </span>
                  </td>
                  <td class="text-center">
                    <?php
                      $statusClass = '';
                      $statusText = '';
                      $statusIcon = '';
                      switch($row['trang_thai_chuyen_di']) {
                        case 'preparing':
                          $statusClass = 'bg-secondary';
                          $statusText = 'Chuẩn bị';
                          $statusIcon = 'fa-hourglass-half';
                          break;
                        case 'started':
                          $statusClass = 'bg-warning text-dark';
                          $statusText = 'Đang diễn ra';
                          $statusIcon = 'fa-play-circle';
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
                          $statusText = 'Chuẩn bị';
                          $statusIcon = 'fa-hourglass-half';
                      }
                    ?>
                    <span class="badge <?= $statusClass ?> badge-status">
                      <i class="fas <?= $statusIcon ?>"></i> <?= $statusText ?>
                    </span>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-primary">
                      <i class="fas fa-users"></i> <?= $row['total_customers'] ?> người
                    </span>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-success">
                      <i class="fas fa-check"></i> <?= $row['confirmed_bookings'] ?>
                    </span>
                    <span class="badge bg-secondary">
                      <i class="fas fa-list"></i> <?= $row['total_bookings'] ?>
                    </span>
                  </td>
                  <td class="text-center">
                    <a href="staff_trip_detail.php?tour=<?= $row['tour_id'] ?>&departure=<?= $row['ngay_khoi_hanh'] ?>" 
                       class="btn btn-sm btn-info view-detail-btn">
                      <i class="fas fa-eye"></i> Xem chi tiết
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" class="no-data">
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