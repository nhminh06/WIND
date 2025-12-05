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

// Phân trang
$records_per_page = 10;
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($current_page - 1) * $records_per_page;

// Query để gom các đặt tour thành chuyến đi
$sql = "SELECT 
            d.tour_id,
            d.ngay_khoi_hanh,
            t.ten_tour,
            t.hinh_anh,
            COUNT(d.id) as so_booking,
            SUM(d.so_nguoi_lon + d.so_tre_em + d.so_tre_nho) as tong_khach,
            SUM(d.tong_tien) as tong_doanh_thu,
            SUM(CASE WHEN d.trang_thai = 'confirmed' THEN 1 ELSE 0 END) as so_confirmed,
            SUM(CASE WHEN d.trang_thai = 'pending' THEN 1 ELSE 0 END) as so_pending,
            GROUP_CONCAT(d.id) as booking_ids,
            MIN(d.ngay_dat) as ngay_dat_dau_tien,
            MAX(d.ngay_dat) as ngay_dat_cuoi,
            MAX(d.trang_thai_chuyen_di) as trang_thai_chuyen_di,
            MAX(d.thoi_gian_bat_dau_chuyen_di) as thoi_gian_bat_dau_chuyen_di,
            MAX(d.thoi_gian_ket_thuc_chuyen_di) as thoi_gian_ket_thuc_chuyen_di
        FROM dat_tour d
        LEFT JOIN tour t ON d.tour_id = t.id
        WHERE 1=1";

$params = [];
$types = '';

if (!empty($search)) {
    $sql .= " AND (t.ten_tour LIKE ? OR d.ma_dat_tour LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ss';
}

if (!empty($tour_filter)) {
    $sql .= " AND d.tour_id = ?";
    $params[] = $tour_filter;
    $types .= 'i';
}

if (!empty($status_filter)) {
    if ($status_filter === 'active') {
        $sql .= " AND d.ngay_khoi_hanh >= CURDATE()";
    } elseif ($status_filter === 'completed') {
        $sql .= " AND d.ngay_khoi_hanh < CURDATE()";
    }
}

$sql .= " GROUP BY d.tour_id, d.ngay_khoi_hanh";
$sql .= " ORDER BY d.ngay_khoi_hanh ASC";

// Đếm tổng số (tạo query mới cho counting)
$sql_count = "SELECT COUNT(*) as total FROM (
    SELECT 
        d.tour_id,
        d.ngay_khoi_hanh
    FROM dat_tour d
    LEFT JOIN tour t ON d.tour_id = t.id
    WHERE 1=1";

$count_params = [];
$count_types = '';

if (!empty($search)) {
    $sql_count .= " AND (t.ten_tour LIKE ? OR d.ma_dat_tour LIKE ?)";
    $search_param = "%$search%";
    $count_params[] = $search_param;
    $count_params[] = $search_param;
    $count_types .= 'ss';
}

if (!empty($tour_filter)) {
    $sql_count .= " AND d.tour_id = ?";
    $count_params[] = $tour_filter;
    $count_types .= 'i';
}

if (!empty($status_filter)) {
    if ($status_filter === 'active') {
        $sql_count .= " AND d.ngay_khoi_hanh >= CURDATE()";
    } elseif ($status_filter === 'completed') {
        $sql_count .= " AND d.ngay_khoi_hanh < CURDATE()";
    }
}

$sql_count .= " GROUP BY d.tour_id, d.ngay_khoi_hanh
) as subquery";

$stmt_count = $conn->prepare($sql_count);
if (!empty($count_params)) {
    $stmt_count->bind_param($count_types, ...$count_params);
}
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_records = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Thêm LIMIT
$sql .= " LIMIT ? OFFSET ?";
$params[] = $records_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Lấy danh sách tour cho filter
$sql_tours = "SELECT id, ten_tour FROM tour ORDER BY ten_tour ASC";
$result_tours = $conn->query($sql_tours);

// Thống kê
$sql_stats = "SELECT 
    COUNT(DISTINCT CONCAT(tour_id, '-', ngay_khoi_hanh)) as total_trips,
    SUM(CASE WHEN ngay_khoi_hanh >= CURDATE() THEN 1 ELSE 0 END) as upcoming_trips,
    SUM(CASE WHEN trang_thai_chuyen_di = 'completed' THEN 1 ELSE 0 END) as completed_trips
    FROM (
        SELECT DISTINCT 
            tour_id, 
            ngay_khoi_hanh,
            MAX(trang_thai_chuyen_di) as trang_thai_chuyen_di
        FROM dat_tour 
        WHERE trang_thai != 'cancelled'
        GROUP BY tour_id, ngay_khoi_hanh
    ) as trips";
$result_stats = $conn->query($sql_stats);
$stats = $result_stats->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Chuyến Đi - Admin</title>
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
            <h1>Quản lý Chuyến Đi</h1>
            <div class="admin-info">
                <?php echo "<p>Xin chào " . (isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin') . "</p>"; ?>
                <button onclick="window.location.href='../views/index/webindex.php'" class="logout">Trở lại</button>
            </div>
        </header>

        <section class="content">
            <!-- Statistics Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <h3><?php echo $stats['total_trips']; ?></h3>
                    <p>Tổng chuyến đi</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $stats['upcoming_trips']; ?></h3>
                    <p>Sắp khởi hành</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $stats['completed_trips']; ?></h3>
                    <p>Đã hoàn thành</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $result->num_rows; ?></h3>
                    <p>Hiển thị</p>
                </div>
            </div>

            <!-- Header -->
            <div class="content-header">
                <h2>Danh sách chuyến đi</h2>
            </div>

            <!-- Search and Filter -->
            <form method="GET" action="" class="search-filter">
                <div class="search-box">
                    <input type="text" name="search" placeholder="Tìm kiếm tour..." value="<?php echo htmlspecialchars($search); ?>">
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
                    <option value="active" <?php echo ($status_filter === 'active') ? 'selected' : ''; ?>>Sắp khởi hành</option>
                    <option value="completed" <?php echo ($status_filter === 'completed') ? 'selected' : ''; ?>>Đã hoàn thành</option>
                </select>
            </form>

            <!-- Trip Cards -->
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while($trip = $result->fetch_assoc()): 
    // Lấy trạng thái từ cột trang_thai_chuyen_di
                        $trip_status = !empty($trip['trang_thai_chuyen_di']) ? $trip['trang_thai_chuyen_di'] : 'preparing';
                        
                        // Xác định hiển thị dựa trên trạng thái
                        switch($trip_status) {
                            case 'preparing':
                                $status_class = 'status-preparing';
                                $status_text = 'Chờ bắt đầu';
                                $status_icon = 'bi-hourglass-split';
                                break;
                                
                            case 'started':
                                $status_class = 'status-started';
                                $status_text = 'Đang diễn ra';
                                $status_icon = 'bi-play-circle-fill';
                                break;
                                
                            case 'completed':
                                $status_class = 'status-completed';
                                $status_text = 'Đã kết thúc';
                                $status_icon = 'bi-check-circle-fill';
                                break;
                                
                            case 'cancelled':
                                $status_class = 'status-cancelled';
                                $status_text = 'Đã hủy';
                                $status_icon = 'bi-x-circle-fill';
                                break;
                                
                            default:
                                $status_class = 'status-preparing';
                                $status_text = 'Chờ bắt đầu';
                                $status_icon = 'bi-hourglass-split';
                        }
                    ?>
                <div class="trip-card">
                    <div class="trip-header">
                        <img src="../../uploads/<?php echo htmlspecialchars($trip['hinh_anh']); ?>" 
                             alt="Tour" 
                             class="trip-image"
                             onerror="this.src='https://images.unsplash.com/photo-1469854523086-cc02fe5d8800'">
                        
                        <div class="trip-info">
                            <div style="display: flex; justify-content: space-between; align-items: start;">
                                <h3 class="trip-title"><?php echo htmlspecialchars($trip['ten_tour']); ?></h3>
                                <span class="trip-status <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </div>
                            
                            <div class="trip-meta">
                                <div class="trip-meta-item">
                                    <i class="bi bi-calendar-event"></i>
                                    <strong>Khởi hành:</strong> <?php echo date('d/m/Y', strtotime($trip['ngay_khoi_hanh'])); ?>
                                </div>
                                <div class="trip-meta-item">
                                    <i class="bi bi-receipt"></i>
                                    <strong>Booking:</strong> <?php echo $trip['so_booking']; ?>
                                </div>
                                <div class="booking-count">
                                    <span class="count-badge count-confirmed">
                                        <i class="bi bi-check-circle"></i> <?php echo $trip['so_confirmed']; ?> Đã xác nhận
                                    </span>
                                    <span class="count-badge count-pending">
                                        <i class="bi bi-clock"></i> <?php echo $trip['so_pending']; ?> Chờ xác nhận
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="trip-stats">
                        <div class="stat-box">
                            <span class="number"><?php echo $trip['tong_khach']; ?></span>
                            <span class="label">Tổng khách</span>
                        </div>
                        <div class="stat-box">
                            <span class="number"><?php echo number_format($trip['tong_doanh_thu'], 0, ',', '.'); ?> ₫</span>
                            <span class="label">Doanh thu</span>
                        </div>
                        <div class="stat-box">
                            <span class="number"><?php echo date('d/m/Y', strtotime($trip['ngay_dat_dau_tien'])); ?></span>
                            <span class="label">Booking đầu</span>
                        </div>
                        <div class="stat-box">
                            <span class="number"><?php echo date('d/m/Y', strtotime($trip['ngay_dat_cuoi'])); ?></span>
                            <span class="label">Booking cuối</span>
                        </div>
                    </div>
                    
                    <div class="trip-actions">
                        <button class="btn-trip btn-view-bookings" 
                                onclick="viewBookings(<?php echo $trip['tour_id']; ?>, '<?php echo $trip['ngay_khoi_hanh']; ?>')">
                            <i class="bi bi-list-ul"></i>
                            Xem danh sách booking
                        </button>
                        <button class="btn-trip btn-manage"
                                onclick="manageTripSchedule(<?php echo $trip['tour_id']; ?>, '<?php echo $trip['ngay_khoi_hanh']; ?>')">
                            <i class="bi bi-calendar-check"></i>
                            Quản lý lịch trình
                        </button>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="trip-card">
                    <div class="no-data" style="text-align: center; padding: 40px;">
                        <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                        <p>Không tìm thấy chuyến đi nào</p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination-container">
                <div class="pagination-info">
                    Hiển thị <?php echo $offset + 1; ?> - <?php echo min($offset + $records_per_page, $total_records); ?> 
                    trong tổng số <?php echo $total_records; ?> chuyến đi
                </div>
                
                <ul class="pagination">
                    <?php if ($current_page > 1): ?>
                    <li>
                        <a href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>&tour=<?php echo urlencode($tour_filter); ?>&status=<?php echo urlencode($status_filter); ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li>
                        <?php if ($i == $current_page): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&tour=<?php echo urlencode($tour_filter); ?>&status=<?php echo urlencode($status_filter); ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endif; ?>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($current_page < $total_pages): ?>
                    <li>
                        <a href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>&tour=<?php echo urlencode($tour_filter); ?>&status=<?php echo urlencode($status_filter); ?>">
                            <i class="bi bi-chevron-right"></i>
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
        function viewBookings(tourId, departureDate) {
            window.location.href = 'tour_bookings.php?tour=' + tourId + '&departure=' + departureDate;
        }
        
        function manageTripSchedule(tourId, departureDate) {
            window.location.href = 'trip_schedule.php?tour=' + tourId + '&departure=' + departureDate;
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>