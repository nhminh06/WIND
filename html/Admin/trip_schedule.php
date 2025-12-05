<?php
session_start();
include '../../db/db.php';

// Kiểm tra quyền admin hoặc staff
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    header('Location: ../../index.php');
    exit();
}

// Lấy thông tin từ URL
$tour_id = isset($_GET['tour']) ? (int)$_GET['tour'] : 0;
$departure_date = isset($_GET['departure']) ? $_GET['departure'] : '';

if (!$tour_id || !$departure_date) {
    header('Location: manage_trip.php');
    exit();
}

// Xử lý cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'update_status') {
        $trip_status = $_POST['trip_status'] ?? '';
        $start_time = $_POST['start_time'] ?? null;
        $end_time = $_POST['end_time'] ?? null;
        $notes = $_POST['notes'] ?? '';
        
        $success = false;
        
        // Xử lý theo từng trạng thái
        if ($trip_status == 'started' && $start_time) {
            // Bắt đầu chuyến đi
            $formatted_start_time = date('Y-m-d H:i:s', strtotime($start_time));
            $note_text = "Chuyến đi BẮT ĐẦU lúc " . date('H:i d/m/Y', strtotime($start_time));
            if ($notes) {
                $note_text .= " - Ghi chú: " . $notes;
            }
            
            $sql_update = "UPDATE dat_tour SET 
                           trang_thai_chuyen_di = 'started',
                           thoi_gian_bat_dau_chuyen_di = ?,
                           ghi_chu = CONCAT(COALESCE(ghi_chu, ''), '\n[', NOW(), '] ', ?)
                           WHERE tour_id = ? AND ngay_khoi_hanh = ?";
            
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param('ssis', $formatted_start_time, $note_text, $tour_id, $departure_date);
            $success = $stmt->execute();
            
        } elseif ($trip_status == 'completed' && $end_time) {
            // Hoàn thành chuyến đi
            $formatted_end_time = date('Y-m-d H:i:s', strtotime($end_time));
            $note_text = "Chuyến đi KẾT THÚC lúc " . date('H:i d/m/Y', strtotime($end_time));
            if ($notes) {
                $note_text .= " - Nhận xét: " . $notes;
            }
            
            $sql_update = "UPDATE dat_tour SET 
                           trang_thai_chuyen_di = 'completed',
                           thoi_gian_ket_thuc_chuyen_di = ?,
                           ghi_chu = CONCAT(COALESCE(ghi_chu, ''), '\n[', NOW(), '] ', ?)
                           WHERE tour_id = ? AND ngay_khoi_hanh = ?";
            
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param('ssis', $formatted_end_time, $note_text, $tour_id, $departure_date);
            $success = $stmt->execute();
            
        } elseif ($trip_status == 'cancelled') {
            // Hủy chuyến đi
            $note_text = "Chuyến đi đã BỊ HỦY";
            if ($notes) {
                $note_text .= " - Lý do: " . $notes;
            }
            
            $sql_update = "UPDATE dat_tour SET 
                           trang_thai_chuyen_di = 'cancelled',
                           ghi_chu = CONCAT(COALESCE(ghi_chu, ''), '\n[', NOW(), '] ', ?)
                           WHERE tour_id = ? AND ngay_khoi_hanh = ?";
            
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param('sis', $note_text, $tour_id, $departure_date);
            $success = $stmt->execute();
        }
        
        if ($success) {
            $_SESSION['success'] = 'Cập nhật trạng thái chuyến đi thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật!';
        }
        
        header("Location: trip_schedule.php?tour=$tour_id&departure=$departure_date");
        exit();
    }
}

// Lấy thông tin tour
$sql_tour = "SELECT * FROM tour WHERE id = ?";
$stmt_tour = $conn->prepare($sql_tour);
$stmt_tour->bind_param('i', $tour_id);
$stmt_tour->execute();
$tour_info = $stmt_tour->get_result()->fetch_assoc();

// Lấy thông tin bookings
$sql_bookings = "SELECT 
                    d.*,
                    u.ho_ten as user_name
                 FROM dat_tour d
                 LEFT JOIN user u ON d.user_id = u.id
                 WHERE d.tour_id = ? AND d.ngay_khoi_hanh = ?
                 ORDER BY d.ngay_dat ASC";

$stmt_bookings = $conn->prepare($sql_bookings);
$stmt_bookings->bind_param('is', $tour_id, $departure_date);
$stmt_bookings->execute();
$result_bookings = $stmt_bookings->get_result();

// Thống kê
$total_bookings = 0;
$total_customers = 0;
$confirmed_count = 0;
$bookings = [];

// Biến lưu trạng thái chuyến đi
$trip_status = 'preparing';
$start_time = '';
$end_time = '';

while ($row = $result_bookings->fetch_assoc()) {
    $bookings[] = $row;
    $total_bookings++;
    $total_customers += ($row['so_nguoi_lon'] + $row['so_tre_em'] + $row['so_tre_nho']);
    if ($row['trang_thai'] == 'confirmed') {
        $confirmed_count++;
    }
    
    // Lấy trạng thái từ cột trang_thai_chuyen_di (chỉ lấy 1 lần)
    if ($trip_status == 'preparing' && !empty($row['trang_thai_chuyen_di'])) {
        $trip_status = $row['trang_thai_chuyen_di'];
    }
    
    // Lấy thời gian bắt đầu và kết thúc từ database
    if (empty($start_time) && !empty($row['thoi_gian_bat_dau_chuyen_di'])) {
        $start_time = date('H:i d/m/Y', strtotime($row['thoi_gian_bat_dau_chuyen_di']));
    }
    
    if (empty($end_time) && !empty($row['thoi_gian_ket_thuc_chuyen_di'])) {
        $end_time = date('H:i d/m/Y', strtotime($row['thoi_gian_ket_thuc_chuyen_di']));
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Lịch trình Chuyến Đi - Admin</title>
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
            <h1>Quản lý Lịch trình Chuyến Đi</h1>
            <div class="admin-info">
                <?php echo "<p>Xin chào " . (isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin') . "</p>"; ?>
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

            <!-- Trip Header -->
            <div class="trip-header-section">
                <div class="trip-header-content">
                    <img src="../../uploads/<?php echo htmlspecialchars($tour_info['hinh_anh']); ?>" 
                         alt="Tour" 
                         class="trip-image-large"
                         onerror="this.src='https://images.unsplash.com/photo-1469854523086-cc02fe5d8800'">
                    <div style="flex: 1;">
                        <h2 style="color: #ffffffff; font-size: 28px; margin-bottom: 15px;"><?php echo htmlspecialchars($tour_info['ten_tour']); ?></h2>
                        <div style="display: flex; gap: 30px; font-size: 16px; margin-bottom: 15px;">
                            <div>
                                <i class="bi bi-calendar-event"></i>
                                <strong>Ngày khởi hành:</strong> <?php echo date('d/m/Y', strtotime($departure_date)); ?>
                            </div>
                            <div>
                                <i class="bi bi-clock-history"></i>
                                <strong>Thời gian:</strong> <?php echo $tour_info['so_ngay']; ?> ngày
                            </div>
                            <div>
                                <i class="bi bi-people"></i>
                                <strong>Tổng khách:</strong> <?php echo $total_customers; ?> người
                            </div>
                            
                        </div>
                        <?php
                        $status_class = '';
                        $status_icon = '';
                        $status_text = '';
                        switch($trip_status) {
                            case 'preparing':
                                $status_class = 'badge-preparing';
                                $status_icon = 'bi-hourglass-split';
                                $status_text = 'Đang chuẩn bị';
                                break;
                            case 'started':
                                $status_class = 'badge-started';
                                $status_icon = 'bi-play-circle';
                                $status_text = 'Đang diễn ra';
                                break;
                            case 'completed':
                                $status_class = 'badge-completed';
                                $status_icon = 'bi-check-circle';
                                $status_text = 'Đã hoàn thành';
                                break;
                            case 'cancelled':
                                $status_class = 'badge-cancelled';
                                $status_icon = 'bi-x-circle';
                                $status_text = 'Đã hủy';
                                break;
                        }
                        ?>
                        <span class="status-badge-big <?php echo $status_class; ?>">
                            <i class="<?php echo $status_icon; ?>"></i>
                            <?php echo $status_text; ?>
                        </span>
                    </div>
                </div>
                
            </div>
             <a href="manage_trip.php" class="back-button" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: white; color: #667eea; border: 2px solid #667eea; border-radius: 8px; text-decoration: none; font-weight: 500; margin-bottom: 20px;">
                <i class="bi bi-arrow-left"></i>
                Quay lại danh sách chuyến đi
            </a>

            <!-- Timeline -->
            <div class="status-timeline">
                <h2 style="margin-bottom: 10px; color: #333;">
                    <i class="bi bi-clock-history"></i> Tiến trình chuyến đi
                </h2>
                <div class="timeline-steps">
                    <div class="timeline-step <?php echo ($trip_status == 'preparing' || $trip_status == 'started' || $trip_status == 'completed') ? 'completed' : ''; ?>">
                        <div class="step-circle">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                        <div class="step-label">Chuẩn bị</div>
                        <div class="step-time">
                            <?php echo $total_bookings; ?> booking
                        </div>
                    </div>
                    
                    <div class="timeline-step <?php echo ($trip_status == 'started') ? 'active' : ''; ?> <?php echo ($trip_status == 'completed') ? 'completed' : ''; ?>">
                        <div class="step-circle">
                            <i class="bi bi-play-circle"></i>
                        </div>
                        <div class="step-label">Bắt đầu</div>
                        <div class="step-time">
                            <?php echo $start_time ? $start_time : 'Chưa bắt đầu'; ?>
                        </div>
                    </div>
                    
                    <div class="timeline-step <?php echo ($trip_status == 'completed') ? 'completed' : ''; ?>">
                        <div class="step-circle">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="step-label">Hoàn thành</div>
                        <div class="step-time">
                            <?php echo $end_time ? $end_time : 'Chưa kết thúc'; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Control Panel -->
            <div class="control-panel">
                <h2 style="margin-bottom: 20px; color: #333;">
                    <i class="bi bi-gear"></i> Điều khiển chuyến đi
                </h2>
                
                <div class="control-grid">
                    <!-- Bắt đầu chuyến đi -->
                    <div class="control-section">
                        <h3>
                            <i class="bi bi-play-circle"></i>
                            Bắt đầu chuyến đi
                        </h3>
                        
                        <?php if ($trip_status == 'preparing'): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="trip_status" value="started">
                            
                            <div class="form-group">
                                <label><i class="bi bi-clock"></i> Thời gian bắt đầu</label>
                                <input type="datetime-local" name="start_time" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label><i class="bi bi-pencil"></i> Ghi chú</label>
                                <textarea name="notes" placeholder="Thêm ghi chú về chuyến đi..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn-control btn-start" onclick="return confirm('Xác nhận BẮT ĐẦU chuyến đi?');">
                                <i class="bi bi-play-circle"></i>
                                Bắt đầu chuyến đi
                            </button>
                        </form>
                        <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle"></i>
                            Chuyến đi đã được bắt đầu lúc: <strong><?php echo $start_time; ?></strong>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Kết thúc chuyến đi -->
                    <div class="control-section">
                        <h3>
                            <i class="bi bi-check-circle"></i>
                            Kết thúc chuyến đi
                        </h3>
                        
                        <?php if ($trip_status == 'started'): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="trip_status" value="completed">
                            
                            <div class="form-group">
                                <label><i class="bi bi-clock"></i> Thời gian kết thúc</label>
                                <input type="datetime-local" name="end_time" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label><i class="bi bi-pencil"></i> Nhận xét cuối cùng</label>
                                <textarea name="notes" placeholder="Đánh giá tổng quan về chuyến đi..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn-control btn-complete" onclick="return confirm('Xác nhận HOÀN THÀNH chuyến đi?');">
                                <i class="bi bi-check-circle"></i>
                                Hoàn thành chuyến đi
                            </button>
                        </form>
                        <?php elseif ($trip_status == 'completed'): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill"></i>
                            Chuyến đi đã hoàn thành lúc: <strong><?php echo $end_time; ?></strong>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle"></i>
                            Vui lòng bắt đầu chuyến đi trước khi kết thúc
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Hủy chuyến đi -->
                <?php if ($trip_status != 'completed' && $trip_status != 'cancelled'): ?>
                <div class="control-section" style="margin-top: 20px;">
                    <h3>
                        <i class="bi bi-x-circle"></i>
                        Hủy chuyến đi
                    </h3>
                    <form method="POST" action="" onsubmit="return confirm('Bạn có chắc chắn muốn HỦY chuyến đi này?\n\nTất cả booking sẽ được cập nhật trạng thái hủy.');">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="trip_status" value="cancelled">
                        
                        <div class="form-group">
                            <label><i class="bi bi-pencil"></i> Lý do hủy</label>
                            <textarea name="notes" placeholder="Nhập lý do hủy chuyến đi..." required></textarea>
                        </div>
                        
                        <button type="submit" class="btn-control btn-cancel-trip">
                            <i class="bi bi-x-circle"></i>
                            Hủy chuyến đi
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>

            <!-- Booking List -->
            <div class="booking-list">
                <h2 style="margin-bottom: 20px; color: #333;">
                    <i class="bi bi-list-ul"></i> Danh sách Booking (<?php echo $total_bookings; ?>)
                </h2>
                
                <?php if (empty($bookings)): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle"></i>
                        Chưa có booking nào cho chuyến đi này
                    </div>
                <?php else: ?>
                    <?php foreach ($bookings as $booking): ?>
                    <div class="booking-item-simple">
                        <div>
                            <strong style="color: #667eea; font-size: 16px;">
                                <?php echo htmlspecialchars($booking['ma_dat_tour']); ?>
                            </strong>
                            <span style="margin-left: 15px; color: #666;">
                                <?php echo htmlspecialchars($booking['ho_ten']); ?>
                            </span>
                            <span style="margin-left: 15px;">
                                <i class="bi bi-people"></i> 
                                <?php echo ($booking['so_nguoi_lon'] + $booking['so_tre_em'] + $booking['so_tre_nho']); ?> người
                            </span>
                        </div>
                        <div>
                            <?php
                            $badge_class = '';
                            switch($booking['trang_thai']) {
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
                                default:
                                    $badge_class = 'badge-pending';
                                    $icon = 'bi-clock';
                                    $text = 'Chờ xác nhận';
                            }
                            ?>
                            <span class="<?php echo $badge_class; ?>" style="padding: 6px 12px; border-radius: 15px; font-size: 13px;">
                                <i class="<?php echo $icon; ?>"></i>
                                <?php echo $text; ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <div class="sidebar-overlay"></div>
    <script src="../../js/Main5.js"></script>
</body>
</html>
<?php
$conn->close();
?>