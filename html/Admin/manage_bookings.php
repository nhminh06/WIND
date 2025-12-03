<?php
session_start();
include '../../db/db.php';

// Kiểm tra quyền admin hoặc staff
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    header('Location: ../../index.php');
    exit();
}

// Xử lý tìm kiếm và lọc
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$tour_filter = isset($_GET['tour']) ? $_GET['tour'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Phân trang
$records_per_page = 7;
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($current_page - 1) * $records_per_page;

// Đếm tổng số bản ghi
$sql_count = "SELECT COUNT(*) as total FROM dat_tour WHERE 1=1";

$count_params = [];
$count_types = '';

if (!empty($search)) {
    $sql_count .= " AND (ma_dat_tour LIKE ? OR ho_ten LIKE ? OR email LIKE ? OR sdt LIKE ?)";
    $search_param = "%$search%";
    $count_params[] = $search_param;
    $count_params[] = $search_param;
    $count_params[] = $search_param;
    $count_params[] = $search_param;
    $count_types .= 'ssss';
}

if (!empty($status_filter)) {
    $sql_count .= " AND trang_thai = ?";
    $count_params[] = $status_filter;
    $count_types .= 's';
}

if (!empty($tour_filter)) {
    $sql_count .= " AND tour_id = ?";
    $count_params[] = $tour_filter;
    $count_types .= 'i';
}

$stmt_count = $conn->prepare($sql_count);

if (!empty($count_params)) {
    $stmt_count->bind_param($count_types, ...$count_params);
}

$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_records = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Query chính
$sql = "SELECT 
            d.*,
            t.ten_tour,
            t.hinh_anh,
            u.ho_ten as user_name
        FROM dat_tour d
        LEFT JOIN tour t ON d.tour_id = t.id
        LEFT JOIN user u ON d.user_id = u.id
        WHERE 1=1";

// Thêm điều kiện tìm kiếm
if (!empty($search)) {
    $sql .= " AND (d.ma_dat_tour LIKE ? OR d.ho_ten LIKE ? OR d.email LIKE ? OR d.sdt LIKE ?)";
}

// Thêm điều kiện lọc trạng thái
if (!empty($status_filter)) {
    $sql .= " AND d.trang_thai = ?";
}

// Thêm điều kiện lọc tour
if (!empty($tour_filter)) {
    $sql .= " AND d.tour_id = ?";
}

// Thêm sắp xếp
switch ($sort) {
    case 'oldest':
        $sql .= " ORDER BY d.id ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY d.tong_tien DESC";
        break;
    case 'price_low':
        $sql .= " ORDER BY d.tong_tien ASC";
        break;
    case 'departure':
        $sql .= " ORDER BY d.ngay_khoi_hanh ASC";
        break;
    default:
        $sql .= " ORDER BY d.id DESC";
        break;
}

// Thêm LIMIT và OFFSET
$sql .= " LIMIT ? OFFSET ?";

// Chuẩn bị và thực thi query
$stmt = $conn->prepare($sql);

// Bind parameters
$params = [];
$types = '';

if (!empty($search)) {
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ssss';
}

if (!empty($status_filter)) {
    $params[] = $status_filter;
    $types .= 's';
}

if (!empty($tour_filter)) {
    $params[] = $tour_filter;
    $types .= 'i';
}

$params[] = $records_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt->bind_param($types, ...$params);

$stmt->execute();
$result = $stmt->get_result();

// Đếm thống kê
$sql_stats = "SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN trang_thai = 'confirmed' THEN 1 ELSE 0 END) AS confirmed_count,
    SUM(CASE WHEN trang_thai = 'pending' THEN 1 ELSE 0 END) AS pending_count,
    SUM(CASE WHEN trang_thai = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_count,
    SUM(CASE WHEN trang_thai = 'confirmed' THEN tong_tien ELSE 0 END) AS total_revenue
    FROM dat_tour";
$result_stats = $conn->query($sql_stats);
$stats = $result_stats->fetch_assoc();

// Lấy danh sách tour cho filter
$sql_tours = "SELECT id, ten_tour FROM tour ORDER BY ten_tour ASC";
$result_tours = $conn->query($sql_tours);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đặt Tour - Admin</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
       
    </style>
</head>
<body>
  <aside class="sidebar">
    <h2 class="logo">WIND Admin</h2>
    <?php include '../../includes/Adminnav.php';?>
  </aside>

  <div class="main">
    <header class="header">
       <button class="menu-toggle">
        <span></span>
        <span></span>
        <span></span>
    </button>
      <h1>Quản lý Đặt Tour</h1>
      <div class="admin-info">
        <?php echo "<p>Xin chào " . (isset($_SESSION['ho_ten']) ? $_SESSION['ho_ten'] : 'Admin') . "</p>"; ?>
        <button onclick="window.location.href='../views/index/webindex.php'" class="logout">Trở lại</button>
      </div>
    </header>

    <section class="content">
      <!-- Thông báo -->
      <?php if(isset($_SESSION['success'])): ?>
      <div class="alert alert-success">
          <i class="bi bi-check-circle-fill"></i>
          <?php 
          echo $_SESSION['success']; 
          unset($_SESSION['success']);
          ?>
      </div>
      <?php endif; ?>

      <?php if(isset($_SESSION['error'])): ?>
      <div class="alert alert-error">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <?php 
          echo $_SESSION['error']; 
          unset($_SESSION['error']);
          ?>
      </div>
      <?php endif; ?>

      <!-- Statistics Cards -->
      <div class="stats-cards">
        <div class="stat-card">
          <h3><?php echo $stats['total']; ?></h3>
          <p>Tổng đặt tour</p>
        </div>
        <div class="stat-card">
          <h3><?php echo $stats['confirmed_count']; ?></h3>
          <p>Đã xác nhận</p>
        </div>
        <div class="stat-card">
          <h3><?php echo $stats['pending_count']; ?></h3>
          <p>Chờ xác nhận</p>
        </div>
        <div class="stat-card">
          <h3><?php echo number_format($stats['total_revenue'], 0, ',', '.'); ?> ₫</h3>
          <p>Tổng doanh thu</p>
        </div>
      </div>

      <!-- Header with Export Button -->
      <div class="content-header">
        <h2>Danh sách đặt tour</h2>
        <div style="display: flex; gap: 10px;">
          <button onclick="window.location.href='manage_trip.php'" class="btn-add" style="background: #28a745;">
            <i class="bi bi-airplane-fill"></i>
            Quản lý lịch trình
          </button>
          <button onclick="exportBookings()" class="btn-add" style="background: #28a745;">
            <i class="bi bi-file-earmark-excel"></i>
            Xuất Excel
          </button>
        </div>
      </div>

      <!-- Search and Filter -->
      <form method="GET" action="" class="search-filter">
        <div class="search-box">
          <input type="text" name="search" placeholder="Tìm kiếm mã đặt tour, tên, email, SĐT..." value="<?php echo htmlspecialchars($search); ?>">
          <button type="submit"><i class="bi bi-search"></i></button>
        </div>
        <select name="tour" class="filter-select" onchange="this.form.submit()">
          <option value="">Tất cả tour</option>
          <?php 
          if ($result_tours && $result_tours->num_rows > 0):
              while($tour = $result_tours->fetch_assoc()): 
          ?>
          <option value="<?php echo $tour['id']; ?>" <?php echo ($tour_filter == $tour['id']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($tour['ten_tour']); ?>
          </option>
          <?php 
              endwhile;
          endif;
          ?>
        </select>
        <select name="status" class="filter-select" onchange="this.form.submit()">
          <option value="">Tất cả trạng thái</option>
          <option value="confirmed" <?php echo ($status_filter === 'confirmed') ? 'selected' : ''; ?>>Đã xác nhận</option>
          <option value="pending" <?php echo ($status_filter === 'pending') ? 'selected' : ''; ?>>Chờ xác nhận</option>
          <option value="cancelled" <?php echo ($status_filter === 'cancelled') ? 'selected' : ''; ?>>Đã hủy</option>
        </select>
        <select name="sort" class="filter-select" onchange="this.form.submit()">
          <option value="newest" <?php echo ($sort == 'newest') ? 'selected' : ''; ?>>Mới nhất</option>
          <option value="oldest" <?php echo ($sort == 'oldest') ? 'selected' : ''; ?>>Cũ nhất</option>
          <option value="departure" <?php echo ($sort == 'departure') ? 'selected' : ''; ?>>Ngày khởi hành</option>
          <option value="price_high" <?php echo ($sort == 'price_high') ? 'selected' : ''; ?>>Giá cao - thấp</option>
          <option value="price_low" <?php echo ($sort == 'price_low') ? 'selected' : ''; ?>>Giá thấp - cao</option>
        </select>
        <input type="hidden" name="page" value="<?php echo $current_page; ?>">
      </form>

      <!-- Table -->
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th >Mã đặt tour</th>
              <th style="width:20%">Tour</th>
              <th>Khách hàng</th>
              <th style="width:10%">Ngày khởi hành</th>
              <th style="width:10%">Số khách/Tổng tiền</th>
              <th style="width:5%">Ngày đặt</th>
              <th>Trạng thái</th>
              <th style="width:10%">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while($row = $result->fetch_assoc()): 
                $tong_so_nguoi = $row['so_nguoi_lon'] + $row['so_tre_em'] + $row['so_tre_nho'];
              ?>
            <tr>
              <td>
                <strong style="color: #3dcce2; font-size: 14px;">
                  <?php echo htmlspecialchars($row['ma_dat_tour']); ?>
                </strong>
              </td>
              <td>
                <div class="tour-info">
                  <img src="../../uploads/<?php echo htmlspecialchars($row['hinh_anh']); ?>" 
                       alt="Tour" 
                       class="tour-thumb"
                       onerror="this.src='https://images.unsplash.com/photo-1469854523086-cc02fe5d8800'">
                  <div class="tour-name">
                    <h4><?php echo htmlspecialchars($row['ten_tour']); ?></h4>
                  </div>
                </div>
              </td>
              <td>
                <div class="booking-info">
                  <h4><?php echo htmlspecialchars($row['ho_ten']); ?></h4>
                  <p><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($row['email']); ?></p>
                  <p><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($row['sdt']); ?></p>
                </div>
              </td>
              <td>
                <div class="date-info">
                  <span class="date">
                    <i class="bi bi-calendar-event"></i>
                    <?php echo date('d/m/Y', strtotime($row['ngay_khoi_hanh'])); ?>
                  </span>
                </div>
              </td>
              <td>
                <div class="price-info">
                  <div class="amount"><?php echo number_format($row['tong_tien'], 0, ',', '.'); ?> ₫</div>
                  <div class="people">
                    <i class="bi bi-people"></i> <?php echo $tong_so_nguoi; ?> người
                    <?php if ($row['so_nguoi_lon'] > 0): ?>
                      (<?php echo $row['so_nguoi_lon']; ?> NL
                    <?php endif; ?>
                    <?php if ($row['so_tre_em'] > 0): ?>
                      <?php echo $row['so_tre_em']; ?> TE
                    <?php endif; ?>
                    <?php if ($row['so_tre_nho'] > 0): ?>
                      <?php echo $row['so_tre_nho']; ?> TN
                    <?php endif; ?>)
                  </div>
                </div>
              </td>
              <td>
                <span style="font-size: 13px; color: #666;">
                  <i class="bi bi-clock"></i> 
                  <?php echo date('d/m/Y H:i', strtotime($row['ngay_dat'])); ?>
                </span>
              </td>
              <td>
                <?php
                $badge_class = '';
                $icon = '';
                $text = '';
                switch($row['trang_thai']) {
                    case 'confirmed':
                        $badge_class = 'badge-confirmed';
                        $icon = 'bi-check-circle';
                        $text = 'Đã xác nhận';
                        break;
                    case 'pending':
                        $badge_class = 'badge-pending';
                        $icon = 'bi-clock';
                        $text = 'Chờ xác nhận';
                        break;
                    case 'cancelled':
                        $badge_class = 'badge-cancelled';
                        $icon = 'bi-x-circle';
                        $text = 'Đã hủy';
                        break;
                }
                  $badge_class1 = '';
                $icon1 = '';
                $text1 = '';
                
                switch($row['trang_thai_thanh_toan']) {
                    case 'da_thanh_toan':
                        $badge_class1 = 'badge-confirmed1';
                        $icon1 = 'bi-check-circle';
                        $text1 = 'Đã thanh toán';
                        break;
                    case 'cho_xac_nhan':
                        $badge_class1 = 'badge-pending1';
                        $icon1 = 'bi-clock';
                        $text1 = 'chưa thanh toán';
                        break;
                    case 'tu_choi':
                        $badge_class1 = 'badge-cancelled1';
                        $icon1 = 'bi-x-circle';
                        $text1 = 'Từ chối ';
                        break;
                }

                ?>
                <span class="<?php echo $badge_class; ?>">
                  <i class="<?php echo $icon; ?>"></i> <?php echo $text; ?>
                </span><br> <br>
                <span class="<?php echo $badge_class1; ?>">
                  <i class="<?php echo $icon1; ?>"></i> <?php echo $text1; ?>
                </span>
              </td>
              <td>
                <div class="action-buttons">
                  <a href="tour_booking_details.php?id=<?php echo $row['id']; ?>" 
                     class="btn-icon btn-view-1" 
                     title="Xem chi tiết">
                    <i class="bi bi-eye"></i>
                  </a>

                  <?php if ($row['trang_thai'] == 'pending' || $row['trang_thai'] == 'cancelled'): ?>
                  <a href="../../php/BookingCTL/confirm_booking.php?id=<?php echo $row['id']; ?>" 
                     class="btn-icon btn-edit" 
                     title="Xác nhận đặt tour"
                     onclick="return confirm('Xác nhận đặt tour <?php echo htmlspecialchars($row['ma_dat_tour']); ?>?')">
                    <i class="bi bi-check-circle"></i>
                  </a>
                  <?php endif; ?>

                  <?php if ($row['trang_thai'] != 'cancelled' || $row['trang_thai'] != 'confirmed'): ?>
                  <a href="../../php/BookingCTL/cancel_booking.php?id=<?php echo $row['id']; ?>" 
                     class="btn-icon btn-view" 
                     title="Hủy đặt tour"
                     onclick="return confirm('Bạn có chắc muốn hủy đặt tour <?php echo htmlspecialchars($row['ma_dat_tour']); ?>?')">
                    <i class="bi bi-x-circle"></i>
                  </a>
                  <?php endif; ?>

                  <button class="btn-icon btn-delete" 
                          title="Xóa đặt tour" 
                          onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['ma_dat_tour']); ?>')">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
              <?php endwhile; ?>
            <?php else: ?>
            <tr>
              <td colspan="8">
                <div class="no-data">
                  <i class="bi bi-inbox"></i>
                  <p>Không tìm thấy đặt tour nào</p>
                </div>
              </td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php if ($total_pages > 1): ?>
      <div class="pagination-container">
        <div class="pagination-info">
          Hiển thị <?php echo $offset + 1; ?> - <?php echo min($offset + $records_per_page, $total_records); ?> 
          trong tổng số <?php echo $total_records; ?> đặt tour
        </div>
        
        <ul class="pagination">
          <!-- First Page -->
          <?php if ($current_page > 1): ?>
          <li>
            <a href="?page=1&search=<?php echo urlencode($search); ?>&tour=<?php echo urlencode($tour_filter); ?>&status=<?php echo urlencode($status_filter); ?>&sort=<?php echo $sort; ?>">
              <i class="bi bi-chevron-double-left"></i>
            </a>
          </li>
          <?php endif; ?>
          
          <!-- Previous Page -->
          <?php if ($current_page > 1): ?>
          <li>
            <a href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>&tour=<?php echo urlencode($tour_filter); ?>&status=<?php echo urlencode($status_filter); ?>&sort=<?php echo $sort; ?>">
              <i class="bi bi-chevron-left"></i>
            </a>
          </li>
          <?php else: ?>
          <li><span class="disabled"><i class="bi bi-chevron-left"></i></span></li>
          <?php endif; ?>
          
          <!-- Page Numbers -->
          <?php
          $start_page = max(1, $current_page - 2);
          $end_page = min($total_pages, $current_page + 2);
          
          if ($start_page > 1) {
              echo '<li><a href="?page=1&search=' . urlencode($search) . '&tour=' . urlencode($tour_filter) . '&status=' . urlencode($status_filter) . '&sort=' . $sort . '">1</a></li>';
              if ($start_page > 2) {
                  echo '<li><span>...</span></li>';
              }
          }
          
          for ($i = $start_page; $i <= $end_page; $i++):
          ?>
          <li>
            <?php if ($i == $current_page): ?>
              <span class="active"><?php echo $i; ?></span>
            <?php else: ?>
              <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&tour=<?php echo urlencode($tour_filter); ?>&status=<?php echo urlencode($status_filter); ?>&sort=<?php echo $sort; ?>">
                <?php echo $i; ?>
              </a>
            <?php endif; ?>
          </li>
          <?php 
          endfor;
          
          if ($end_page < $total_pages) {
              if ($end_page < $total_pages - 1) {
                  echo '<li><span>...</span></li>';
              }
              echo '<li><a href="?page=' . $total_pages . '&search=' . urlencode($search) . '&tour=' . urlencode($tour_filter) . '&status=' . urlencode($status_filter) . '&sort=' . $sort . '">' . $total_pages . '</a></li>';
          }
          ?>
          
          <!-- Next Page -->
          <?php if ($current_page < $total_pages): ?>
          <li>
            <a href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>&tour=<?php echo urlencode($tour_filter); ?>&status=<?php echo urlencode($status_filter); ?>&sort=<?php echo $sort; ?>">
              <i class="bi bi-chevron-right"></i>
            </a>
          </li>
          <?php else: ?>
          <li><span class="disabled"><i class="bi bi-chevron-right"></i></span></li>
          <?php endif; ?>
          
          <!-- Last Page -->
          <?php if ($current_page < $total_pages): ?>
          <li>
            <a href="?page=<?php echo $total_pages; ?>&search=<?php echo urlencode($search); ?>&tour=<?php echo urlencode($tour_filter); ?>&status=<?php echo urlencode($status_filter); ?>&sort=<?php echo $sort; ?>">
              <i class="bi bi-chevron-double-right"></i>
            </a>
          </li>
          <?php endif; ?>
        </ul>
      </div>
      <?php endif; ?>
    </section>
  </div>

<div class="sidebar-overlay"></div>
<script src="../../js/Main5.js"></script>
<script>
  function confirmDelete(bookingId, bookingCode) {
    if (confirm('Bạn có chắc chắn muốn XÓA VĨNH VIỄN đặt tour "' + bookingCode + '"?\n\nHành động này sẽ:\n- Xóa tất cả dữ liệu đặt tour\n- KHÔNG THỂ HOÀN TÁC!\n\nNhấn OK để xác nhận xóa.')) {
      window.location.href = '../../php/BookingCTL/delete_booking.php?id=' + bookingId;
    }
  }

  function exportBookings() {
    window.location.href = '../../php/BookingCTL/export_bookings.php?' + 
                           'search=<?php echo urlencode($search); ?>' +
                           '&tour=<?php echo urlencode($tour_filter); ?>' +
                           '&status=<?php echo urlencode($status_filter); ?>' +
                           '&sort=<?php echo $sort; ?>';
  }

  // Auto hide alerts after 5 seconds
  setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 300);
    });
  }, 5000);
</script>
</body>
</html>
<?php
$conn->close();
?>