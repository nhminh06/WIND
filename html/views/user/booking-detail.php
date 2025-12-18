<?php 
session_start();
include '../../../db/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($booking_id <= 0) {
    header('Location: my-bookings.php');
    exit();
}

// Fetch booking details
$query = "SELECT 
            d.*,
            t.ten_tour,
            t.hinh_anh,
            t.so_ngay,
            t.gia,
            t.vi_tri,
            tc.ma_tour,
            tc.diem_khoi_hanh,
            tc.phuong_tien,
            tc.mo_ta_ngan,
            u.ho_ten as user_name,
            u.email as user_email,
            u.sdt as user_sdt
          FROM dat_tour d
          JOIN tour t ON d.tour_id = t.id
          LEFT JOIN tour_chi_tiet tc ON t.id = tc.tour_id
          LEFT JOIN user u ON d.user_id = u.id
          WHERE d.id = ? AND d.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: my-bookings.php');
    exit();
}

$booking = $result->fetch_assoc();

// Tính toán các thông tin bổ sung
$ngay_ve = date('Y-m-d', strtotime($booking['ngay_khoi_hanh'] . ' + ' . ($booking['so_ngay'] - 1) . ' days'));
$tong_so_nguoi = $booking['so_nguoi_lon'] + $booking['so_tre_em'] + $booking['so_tre_nho'];
$ngay_khoi_hanh = strtotime($booking['ngay_khoi_hanh']);
$today = strtotime(date('Y-m-d'));

// Kiểm tra có thể hủy không (chỉ khi preparing và còn hơn 3 ngày)
$can_cancel = ($booking['trang_thai'] == 'confirmed' && 
              $booking['trang_thai_chuyen_di'] == 'preparing' &&
              $ngay_khoi_hanh > $today && 
              ($ngay_khoi_hanh - $today) >= (3 * 24 * 60 * 60));

// Kiểm tra có thể đánh giá không (đã xác nhận và chuyến đi đã hoàn thành)
$can_review = ($booking['trang_thai'] == 'confirmed' && 
               $booking['trang_thai_chuyen_di'] == 'completed');

// Kiểm tra đã đánh giá chưa
$has_reviewed = false;
if ($can_review) {
    $check_review = "SELECT id FROM danh_gia WHERE tour_id = ? AND ten_khach_hang = ?";
    $stmt_check = $conn->prepare($check_review);
    $stmt_check->bind_param("is", $booking['tour_id'], $booking['ho_ten']);
    $stmt_check->execute();
    $has_reviewed = $stmt_check->get_result()->num_rows > 0;
    $stmt_check->close();
}

// Get timeline events
$timeline = [];

$timeline[] = [
    'date' => $booking['ngay_dat'],
    'title' => 'Đặt tour thành công',
    'description' => 'Đơn đặt tour của bạn đã được ghi nhận',
    'icon' => 'fa-check-circle',
    'status' => 'completed'
];

if ($booking['trang_thai'] == 'confirmed') {
    $timeline[] = [
        'date' => date('Y-m-d H:i:s'),
        'title' => 'Đã xác nhận',
        'description' => 'Tour đã được xác nhận và sẵn sàng',
        'icon' => 'fa-clipboard-check',
        'status' => 'completed'
    ];
}

// Timeline theo trạng thái chuyến đi
if ($booking['trang_thai_chuyen_di'] == 'started' || $booking['trang_thai_chuyen_di'] == 'completed') {
    $start_time = !empty($booking['thoi_gian_bat_dau_chuyen_di']) 
        ? $booking['thoi_gian_bat_dau_chuyen_di'] 
        : $booking['ngay_khoi_hanh'] . ' 00:00:00';
    
    $timeline[] = [
        'date' => $start_time,
        'title' => 'Chuyến đi bắt đầu',
        'description' => 'Đã khởi hành',
        'icon' => 'fa-plane-departure',
        'status' => 'completed'
    ];
}

if ($booking['trang_thai_chuyen_di'] == 'completed') {
    $end_time = !empty($booking['thoi_gian_ket_thuc_chuyen_di']) 
        ? $booking['thoi_gian_ket_thuc_chuyen_di'] 
        : $ngay_ve . ' 23:59:59';
    
    $timeline[] = [
        'date' => $end_time,
        'title' => 'Kết thúc chuyến đi',
        'description' => 'Hoàn thành tour',
        'icon' => 'fa-flag-checkered',
        'status' => 'completed'
    ];
}

if ($booking['trang_thai_chuyen_di'] == 'preparing' && $booking['trang_thai'] != 'cancelled') {
    $timeline[] = [
        'date' => $booking['ngay_khoi_hanh'] . ' 00:00:00',
        'title' => 'Ngày khởi hành dự kiến',
        'description' => 'Chuyến đi sẽ bắt đầu',
        'icon' => 'fa-calendar-day',
        'status' => 'pending'
    ];
}

if ($booking['trang_thai'] == 'cancelled' || $booking['trang_thai_chuyen_di'] == 'cancelled') {
    $timeline[] = [
        'date' => date('Y-m-d H:i:s'),
        'title' => 'Đã hủy',
        'description' => 'Đơn đặt tour đã bị hủy',
        'icon' => 'fa-times-circle',
        'status' => 'cancelled'
    ];
}

// Status display cho booking
$status_config = [
    'confirmed' => ['text' => 'Đã xác nhận', 'icon' => 'fa-check-circle', 'class' => 'success'],
    'pending' => ['text' => 'Chờ xác nhận', 'icon' => 'fa-clock', 'class' => 'warning'],
    'cancelled' => ['text' => 'Đã hủy', 'icon' => 'fa-times-circle', 'class' => 'danger']
];
$status = $status_config[$booking['trang_thai']] ?? $status_config['pending'];

// Status display cho chuyến đi
$trip_status_config = [
    'preparing' => ['text' => 'Chuẩn bị', 'icon' => 'fa-hourglass-half', 'class' => 'info'],
    'started' => ['text' => 'Đang diễn ra', 'icon' => 'fa-plane', 'class' => 'primary'],
    'completed' => ['text' => 'Đã hoàn thành', 'icon' => 'fa-check-circle', 'class' => 'success'],
    'cancelled' => ['text' => 'Đã hủy', 'icon' => 'fa-times-circle', 'class' => 'danger']
];
$trip_status = $trip_status_config[$booking['trang_thai_chuyen_di']] ?? $trip_status_config['preparing'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đặt chỗ #<?php echo htmlspecialchars($booking['ma_dat_tour']); ?></title>
    <link rel="stylesheet" href="../../../css/booking-detail-pro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../css/rpusers.css" />
    <style>
        .status-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .nav-btn-review {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .nav-btn-review:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 87, 108, 0.4);
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="users.php" class="nav-back">
                <i class="fas fa-arrow-left"></i>
                <span>Quay lại danh sách</span>
            </a>
            <div class="nav-actions">
                <button onclick="window.print()" class="nav-btn">
                    <i class="fas fa-print"></i>
                    <span>In phiếu</span>
                </button>
                
                <?php if ($can_review && !$has_reviewed): ?>
                <button onclick="window.location.href='review-tour.php?tour_id=<?php echo $booking['tour_id']; ?>&booking_id=<?php echo $booking['id']; ?>'" class="nav-btn nav-btn-review">
                    <i class="fas fa-star"></i>
                    <span>Đánh giá tour</span>
                </button>
                <?php elseif ($can_review && $has_reviewed): ?>
                <button class="nav-btn" disabled style="opacity: 0.6; cursor: not-allowed;">
                    <i class="fas fa-check"></i>
                    <span>Đã đánh giá</span>
                </button>
                <?php endif; ?>
                
                <?php if ($can_cancel): ?>
                <button onclick="showCancelModal()" class="nav-btn nav-btn-danger">
                    <i class="fas fa-ban"></i>
                    <span>Hủy đặt chỗ</span>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Booking Header -->
        <div class="booking-header-card">
            <div class="booking-header-content">
                <div class="booking-header-left">
                    <div class="booking-code-badge">
                        <i class="fas fa-ticket-alt"></i>
                        <span><?php echo htmlspecialchars($booking['ma_dat_tour']); ?></span>
                    </div>
                    <h1 class="booking-title"><?php echo htmlspecialchars($booking['ten_tour']); ?></h1>
                    <?php if (!empty($booking['vi_tri'])): ?>
                    <p class="booking-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <?php echo htmlspecialchars($booking['vi_tri']); ?>
                    </p>
                    <?php endif; ?>
                </div>
                <div class="booking-header-right">
                    <div class="status-group">
                        <div class="status-badge status-<?php echo $status['class']; ?>">
                            <i class="fas <?php echo $status['icon']; ?>"></i>
                            <span><?php echo $status['text']; ?></span>
                        </div>
                        <div class="status-badge status-<?php echo $trip_status['class']; ?>">
                            <i class="fas <?php echo $trip_status['icon']; ?>"></i>
                            <span><?php echo $trip_status['text']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Left Column -->
            <div class="left-column">
                <!-- Tour Overview -->
                <section class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-image"></i> Thông tin tour</h2>
                    </div>
                    <div class="card-body">
                        <div class="tour-image-container">
                            <img src="<?php echo htmlspecialchars('../../../uploads/' . $booking['hinh_anh']); ?>" 
                                 alt="<?php echo htmlspecialchars($booking['ten_tour']); ?>"
                                 class="tour-image">
                        </div>
                        
                        <?php if (!empty($booking['mo_ta_ngan'])): ?>
                        <div class="tour-description">
                            <p><?php echo htmlspecialchars($booking['mo_ta_ngan']); ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <div class="tour-info-grid">
                            <?php if (!empty($booking['ma_tour'])): ?>
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-barcode"></i>
                                </div>
                                <div class="info-content">
                                    <span class="info-label">Mã tour</span>
                                    <span class="info-value"><?php echo htmlspecialchars($booking['ma_tour']); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div class="info-content">
                                    <span class="info-label">Thời gian</span>
                                    <span class="info-value"><?php echo $booking['so_ngay']; ?> ngày <?php echo $booking['so_ngay'] - 1; ?> đêm</span>
                                </div>
                            </div>
                            
                            <?php if (!empty($booking['phuong_tien'])): ?>
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-bus"></i>
                                </div>
                                <div class="info-content">
                                    <span class="info-label">Phương tiện</span>
                                    <span class="info-value"><?php echo htmlspecialchars($booking['phuong_tien']); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($booking['diem_khoi_hanh'])): ?>
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-location-dot"></i>
                                </div>
                                <div class="info-content">
                                    <span class="info-label">Điểm khởi hành</span>
                                    <span class="info-value"><?php echo htmlspecialchars($booking['diem_khoi_hanh']); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <!-- Timeline -->
                <section class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-history"></i> Tiến trình đặt chỗ</h2>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <?php foreach ($timeline as $index => $event): ?>
                            <div class="timeline-item timeline-<?php echo $event['status']; ?>">
                                <div class="timeline-dot">
                                    <i class="fas <?php echo $event['icon']; ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <h4><?php echo $event['title']; ?></h4>
                                    <p><?php echo $event['description']; ?></p>
                                    <span class="timeline-time">
                                        <i class="fas fa-clock"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($event['date'])); ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
                
                <?php if ($can_review): ?>
                <section class="card" style="background: linear-gradient(135deg, #f093fb15 0%, #f5576c15 100%); border: 2px solid #f5576c;">
                    <div class="card-header">
                        <h2><i class="fas fa-star"></i> Đánh giá chuyến đi</h2>
                    </div>
                    <div class="card-body" style="text-align: center;">
                        <?php if (!$has_reviewed): ?>
                        <p style="margin-bottom: 20px; color: #666;">
                            Chuyến đi của bạn đã kết thúc. Hãy chia sẻ trải nghiệm của bạn để giúp những người khác!
                        </p>
                        <button onclick="window.location.href='review-tour.php?tour_id=<?php echo $booking['tour_id']; ?>&booking_id=<?php echo $booking['id']; ?>'" 
                                style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 12px 30px; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; display: inline-flex; align-items: center; gap: 10px;">
                            <i class="fas fa-star"></i>
                            Viết đánh giá ngay
                        </button>
                        <?php else: ?>
                        <p style="color: #28a745; font-weight: 600;">
                            <i class="fas fa-check-circle"></i>
                            Cảm ơn bạn đã đánh giá tour này!
                        </p>
                        <?php endif; ?>
                    </div>
                </section>
                <?php endif; ?>
            </div>

            <!-- Right Column -->
            <div class="right-column">
                <!-- Booking Details -->
                <section class="card card-sticky">
                    <div class="card-header">
                        <h2><i class="fas fa-calendar-check"></i> Chi tiết đặt chỗ</h2>
                    </div>
                    <div class="card-body">
                        <div class="detail-group">
                            <label><i class="fas fa-calendar-alt"></i> Ngày đặt</label>
                            <value><?php echo date('d/m/Y H:i', strtotime($booking['ngay_dat'])); ?></value>
                        </div>
                        
                        <div class="detail-group highlight">
                            <label><i class="fas fa-plane-departure"></i> Ngày khởi hành</label>
                            <value class="text-primary"><?php echo date('d/m/Y', strtotime($booking['ngay_khoi_hanh'])); ?></value>
                        </div>
                        
                        <div class="detail-group highlight">
                            <label><i class="fas fa-plane-arrival"></i> Ngày về dự kiến</label>
                            <value class="text-primary"><?php echo date('d/m/Y', strtotime($ngay_ve)); ?></value>
                        </div>
                        
                        <div class="divider"></div>
                        
                        <div class="detail-group">
                            <label><i class="fas fa-user"></i> Họ tên</label>
                            <value><?php echo htmlspecialchars($booking['ho_ten']); ?></value>
                        </div>
                        
                        <div class="detail-group">
                            <label><i class="fas fa-envelope"></i> Email</label>
                            <value><?php echo htmlspecialchars($booking['email']); ?></value>
                        </div>
                        
                        <div class="detail-group">
                            <label><i class="fas fa-phone"></i> Số điện thoại</label>
                            <value><?php echo htmlspecialchars($booking['sdt']); ?></value>
                        </div>
                        
                        <?php if (!empty($booking['dia_chi'])): ?>
                        <div class="detail-group">
                            <label><i class="fas fa-home"></i> Địa chỉ</label>
                            <value><?php echo htmlspecialchars($booking['dia_chi']); ?></value>
                        </div>
                        <?php endif; ?>
                        
                        <div class="divider"></div>
                        
                        <div class="passengers-summary">
                            <label><i class="fas fa-users"></i> Số lượng khách</label>
                            <div class="passengers-list">
                                <?php if ($booking['so_nguoi_lon'] > 0): ?>
                                <div class="passenger-item">
                                    <span class="passenger-icon"><i class="fas fa-user"></i></span>
                                    <span class="passenger-label">Người lớn</span>
                                    <span class="passenger-count">×<?php echo $booking['so_nguoi_lon']; ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($booking['so_tre_em'] > 0): ?>
                                <div class="passenger-item">
                                    <span class="passenger-icon"><i class="fas fa-child"></i></span>
                                    <span class="passenger-label">Trẻ em</span>
                                    <span class="passenger-count">×<?php echo $booking['so_tre_em']; ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($booking['so_tre_nho'] > 0): ?>
                                <div class="passenger-item">
                                    <span class="passenger-icon"><i class="fas fa-baby"></i></span>
                                    <span class="passenger-label">Trẻ nhỏ</span>
                                    <span class="passenger-count">×<?php echo $booking['so_tre_nho']; ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="passengers-total">
                                Tổng cộng: <strong><?php echo $tong_so_nguoi; ?> khách</strong>
                            </div>
                        </div>
                        
                        <?php if (!empty($booking['ghi_chu'])): ?>
                        <div class="divider"></div>
                        <div class="note-box">
                            <i class="fas fa-sticky-note"></i>
                            <div>
                                <strong>Ghi chú:</strong>
                                <p><?php echo nl2br(htmlspecialchars($booking['ghi_chu'])); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Payment Summary -->
                <section class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-receipt"></i> Thông tin thanh toán</h2>
                    </div>
                    <div class="card-body">
                        <div class="payment-breakdown">
                            <?php if ($booking['so_nguoi_lon'] > 0): ?>
                            <div class="payment-item">
                                <span>Người lớn (×<?php echo $booking['so_nguoi_lon']; ?>)</span>
                                <span><?php echo number_format($booking['gia'], 0, ',', '.'); ?> ₫</span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($booking['so_tre_em'] > 0): ?>
                            <div class="payment-item">
                                <span>Trẻ em (×<?php echo $booking['so_tre_em']; ?>)</span>
                                <span><?php echo number_format($booking['gia'] * 0.7, 0, ',', '.'); ?> ₫</span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($booking['so_tre_nho'] > 0): ?>
                            <div class="payment-item">
                                <span>Trẻ nhỏ (×<?php echo $booking['so_tre_nho']; ?>)</span>
                                <span><?php echo number_format($booking['gia'] * 0.5, 0, ',', '.'); ?> ₫</span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="payment-total">
                            <span>Tổng thanh toán</span>
                            <span><?php echo number_format($booking['tong_tien'], 0, ',', '.'); ?> ₫</span>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div id="cancelModal" class="modal">
        <div class="modal-overlay" onclick="closeCancelModal()"></div>
        <div class="modal-container">
            <button class="modal-close" onclick="closeCancelModal()">
                <i class="fas fa-times"></i>
            </button>
            <div class="modal-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h3>Xác nhận hủy đặt chỗ</h3>
            <p>Bạn có chắc chắn muốn hủy đặt chỗ này không?</p>
            <div class="modal-warning">
                <i class="fas fa-info-circle"></i>
                <span>Lưu ý: Việc hủy tour phải tuân thủ chính sách hủy. Vui lòng liên hệ để biết thêm chi tiết về chính sách hoàn tiền.</span>
            </div>
            <div class="modal-actions">
                <button onclick="closeCancelModal()" class="modal-btn modal-btn-secondary">
                    <i class="fas fa-times"></i>
                    Đóng
                </button>
                <form method="POST" action="my-bookings.php" style="display: inline;">
                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                    <button type="submit" name="cancel_booking" class="modal-btn modal-btn-danger">
                        <i class="fas fa-check"></i>
                        Xác nhận hủy
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showCancelModal() {
            document.getElementById('cancelModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('cancelModal');
            if (event.target == modal) {
                closeCancelModal();
            }
        }
    </script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>