<?php
session_start();
include '../../db/db.php';

// Kiểm tra quyền admin hoặc staff
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    header('Location: ../../index.php');
    exit();
}

// Lấy thông tin tour và ngày khởi hành từ URL
$tour_id = isset($_GET['tour']) ? (int)$_GET['tour'] : 0;
$departure_date = isset($_GET['departure']) ? $_GET['departure'] : '';

if (!$tour_id || !$departure_date) {
    header('Location: manage_trip.php');
    exit();
}

// Lấy thông tin tour
$sql_tour = "SELECT * FROM tour WHERE id = ?";
$stmt_tour = $conn->prepare($sql_tour);
$stmt_tour->bind_param('i', $tour_id);
$stmt_tour->execute();
$tour_info = $stmt_tour->get_result()->fetch_assoc();

if (!$tour_info) {
    header('Location: manage_trip.php');
    exit();
}

// Lấy danh sách booking
$sql = "SELECT 
            d.*,
            u.ho_ten as user_name,
            u.email as user_email
        FROM dat_tour d
        LEFT JOIN user u ON d.user_id = u.id
        WHERE d.tour_id = ? AND d.ngay_khoi_hanh = ?
        ORDER BY d.ngay_dat DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('is', $tour_id, $departure_date);
$stmt->execute();
$result = $stmt->get_result();

// Tính toán thống kê
$total_bookings = 0;
$total_confirmed = 0;
$total_pending = 0;
$total_cancelled = 0;
$total_customers = 0;
$total_adults = 0;
$total_children = 0;
$total_infants = 0;
$total_revenue = 0;
$total_confirmed_revenue = 0;

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
    $total_bookings++;
    
    $customers = $row['so_nguoi_lon'] + $row['so_tre_em'] + $row['so_tre_nho'];
    $total_customers += $customers;
    $total_adults += $row['so_nguoi_lon'];
    $total_children += $row['so_tre_em'];
    $total_infants += $row['so_tre_nho'];
    $total_revenue += $row['tong_tien'];
    
    if ($row['trang_thai'] == 'confirmed') {
        $total_confirmed++;
        $total_confirmed_revenue += $row['tong_tien'];
    } elseif ($row['trang_thai'] == 'pending') {
        $total_pending++;
    } elseif ($row['trang_thai'] == 'cancelled') {
        $total_cancelled++;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết Booking Chuyến Đi - Admin</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .trip-overview {
            background: linear-gradient(135deg, #3e59d0ff 0%, #0da7c9ff 100%);
            color: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }
        
        .trip-overview-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .trip-overview-image {
            width: 150px;
            height: 150px;
            border-radius: 12px;
            object-fit: cover;
            border: 3px solid rgba(255,255,255,0.3);
        }
        
        .trip-overview-info h2 {
            font-size: 28px;
            margin-bottom: 10px;
            color: white;
        }
        
        .trip-overview-meta {
            display: flex;
            gap: 30px;
            font-size: 16px;
            opacity: 0.95;
        }
        
        .trip-overview-meta i {
            margin-right: 8px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card-detail {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card-detail .icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #4762dbff 0%, #23a3d9ff 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 24px;
        }
        .stat-card-detail > .icon >i{
            margin: auto;
        }
        
        .stat-card-detail .number {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .stat-card-detail .label {
            color: #666;
            font-size: 14px;
        }
        
        .booking-item {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .booking-item:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            transform: translateX(5px);
        }
        
        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .booking-code {
            font-size: 18px;
            font-weight: 700;
            color: #3251dcff;
        }
        
        .booking-date {
            color: #666;
            font-size: 13px;
        }
        
        .booking-content {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }
        
        .booking-section {
            padding: 10px;
            color: #333;
        }
        
        .booking-section h4 {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .info-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .info-row i {
            color: #3a57dbff;
            width: 16px;
        }
        
        .customer-count {
            background: #f0f4ff;
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
        }
        
        .customer-count-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 13px;
        }
        
        .booking-actions {
            grid-column: 1 / -1;
            display: flex;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }
        
        .btn-action {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s;
        }
        
        .btn-detail {
            background: #3dcce2;
            color: white;
        }
        
        .btn-detail:hover {
            background: #2ab5cc;
        }
        
        .btn-confirm {
            background: #28a745;
            color: white;
        }
        
        .btn-confirm:hover {
            background: #218838;
        }
        
        .btn-cancel {
            background: #dc3545;
            color: white;
        }
        
        .btn-cancel:hover {
            background: #c82333;
        }
        
        .btn-delete {
            background: #6c757d;
            color: white;
        }
        
        .btn-delete:hover {
            background: #5a6268;
        }
        
        .status-badge-large {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: white;
            color: #2987d8ff;
            border: 2px solid #388be9ff;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            margin-bottom: 20px;
        }
        
        .back-button:hover {
            background: #1679ebff;
            color: white;
        }
        
        .export-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .btn-export {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-export:hover {
            background: #218838;
        }
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
            <h1>Chi tiết Booking Chuyến Đi</h1>
            <div class="admin-info">
                <?php echo "<p>Xin chào " . (isset($_SESSION['ho_ten']) ? $_SESSION['ho_ten'] : 'Admin') . "</p>"; ?>
                <button onclick="window.location.href='../views/index/webindex.php'" class="logout">Trở lại</button>
            </div>
        </header>

        <section class="content">
           

            <!-- Trip Overview -->
            <div class="trip-overview">
                <div class="trip-overview-header">
                    <img src="../../uploads/<?php echo htmlspecialchars($tour_info['hinh_anh']); ?>" 
                         alt="Tour" 
                         class="trip-overview-image"
                         onerror="this.src='https://images.unsplash.com/photo-1469854523086-cc02fe5d8800'">
                    <div class="trip-overview-info">
                        <h2><?php echo htmlspecialchars($tour_info['ten_tour']); ?></h2>
                        <div class="trip-overview-meta">
                            <div>
                                <i class="bi bi-calendar-event"></i>
                                <strong>Ngày khởi hành:</strong> <?php echo date('d/m/Y', strtotime($departure_date)); ?>
                            </div>
                            <div>
                                <i class="bi bi-clock-history"></i>
                                <strong>Thời gian:</strong> <?php echo $tour_info['so_ngay']; ?> ngày
                            </div>
                        </div>
                    </div>
                </div>
                 <a href="manage_trip.php" class="back-button">
                <i class="bi bi-arrow-left"></i>
                Quay lại danh sách chuyến đi
            </a>
            </div>
                
            <!-- Statistics Grid -->
            <div class="stats-grid">
                <div class="stat-card-detail">
                    <div class="icon">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div class="number"><?php echo $total_bookings; ?></div>
                    <div class="label">Tổng Booking</div>
                </div>
                
                <div class="stat-card-detail">
                    <div class="icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="number"><?php echo $total_customers; ?></div>
                    <div class="label">Tổng Khách</div>
                </div>
                
                <div class="stat-card-detail">
                    <div class="icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="number"><?php echo $total_confirmed; ?></div>
                    <div class="label">Đã Xác Nhận</div>
                </div>
                
                <div class="stat-card-detail">
                    <div class="icon">
                        <i class="bi bi-clock"></i>
                    </div>
                    <div class="number"><?php echo $total_pending; ?></div>
                    <div class="label">Chờ Xác Nhận</div>
                </div>
                
                <div class="stat-card-detail">
                    <div class="icon">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="number"><?php echo number_format($total_confirmed_revenue, 0, ',', '.'); ?>₫</div>
                    <div class="label">Doanh Thu Xác Nhận</div>
                </div>
            </div>

            <!-- Export Section -->
            <div class="export-section">
                <h2 style="margin: 0;">Danh sách Booking (<?php echo $total_bookings; ?>)</h2>
                <button class="btn-export" onclick="exportTripBookings()">
                    <i class="bi bi-file-earmark-excel"></i>
                    Xuất Excel
                </button>
            </div>

            <!-- Bookings List -->
            <?php if (count($bookings) > 0): ?>
                <?php foreach ($bookings as $booking): 
                    $tong_so_nguoi = $booking['so_nguoi_lon'] + $booking['so_tre_em'] + $booking['so_tre_nho'];
                    
                    $badge_class = '';
                    $icon = '';
                    $text = '';
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
                    }
                ?>
                <div class="booking-item">
                    <div class="booking-header">
                        <div>
                            <div class="booking-code">
                                <i class="bi bi-tag"></i>
                                <?php echo htmlspecialchars($booking['ma_dat_tour']); ?>
                            </div>
                            <div class="booking-date">
                                <i class="bi bi-clock"></i>
                                Đặt lúc: <?php echo date('d/m/Y H:i', strtotime($booking['ngay_dat'])); ?>
                            </div>
                        </div>
                        <span class="status-badge-large <?php echo $badge_class; ?>">
                            <i class="<?php echo $icon; ?>"></i>
                            <?php echo $text; ?>
                        </span>
                    </div>
                    
                    <div class="booking-content">
                        <!-- Thông tin khách hàng -->
                        <div class="booking-section">
                            <h4><i class="bi bi-person"></i> Thông tin khách hàng</h4>
                            <div class="info-row">
                                <i class="bi bi-person-fill"></i>
                                <strong><?php echo htmlspecialchars($booking['ho_ten']); ?></strong>
                            </div>
                            <div class="info-row">
                                <i class="bi bi-envelope"></i>
                                <?php echo htmlspecialchars($booking['email']); ?>
                            </div>
                            <div class="info-row">
                                <i class="bi bi-telephone"></i>
                                <?php echo htmlspecialchars($booking['sdt']); ?>
                            </div>
                            <?php if ($booking['dia_chi']): ?>
                            <div class="info-row">
                                <i class="bi bi-geo-alt"></i>
                                <?php echo htmlspecialchars($booking['dia_chi']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Số lượng khách -->
                        <div class="booking-section">
                            <h4><i class="bi bi-people"></i> Số lượng khách</h4>
                            <div class="customer-count">
                                <div class="customer-count-item">
                                    <span>Người lớn:</span>
                                    <strong><?php echo $booking['so_nguoi_lon']; ?> người</strong>
                                </div>
                                <div class="customer-count-item">
                                    <span>Trẻ em:</span>
                                    <strong><?php echo $booking['so_tre_em']; ?> người</strong>
                                </div>
                                <div class="customer-count-item">
                                    <span>Trẻ nhỏ:</span>
                                    <strong><?php echo $booking['so_tre_nho']; ?> người</strong>
                                </div>
                                <div class="customer-count-item" style="border-top: 2px solid #667eea; padding-top: 8px; margin-top: 8px;">
                                    <span><strong>Tổng cộng:</strong></span>
                                    <strong style="color: #667eea; font-size: 16px;"><?php echo $tong_so_nguoi; ?> người</strong>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Thanh toán -->
                        <div class="booking-section">
                            <h4><i class="bi bi-currency-dollar"></i> Thanh toán</h4>
                            <div class="info-row">
                                <i class="bi bi-cash"></i>
                                <strong style="color: #28a745; font-size: 18px;">
                                    <?php echo number_format($booking['tong_tien'], 0, ',', '.'); ?> ₫
                                </strong>
                            </div>
                            <?php if ($booking['ghi_chu']): ?>
                            <div class="info-row" style="margin-top: 10px;">
                                <i class="bi bi-chat-left-text"></i>
                                <em><?php echo htmlspecialchars($booking['ghi_chu']); ?></em>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Actions -->
                        <div class="booking-actions">
                            <button class="btn-action btn-detail" 
                                    onclick="window.location.href='tour_booking_details.php?id=<?php echo $booking['id']; ?>'">
                                <i class="bi bi-eye"></i>
                                Chi tiết
                            </button>
                            
                            <?php if ($booking['trang_thai'] == 'pending'): ?>
                            <button class="btn-action btn-confirm"
                                    onclick="confirmBooking(<?php echo $booking['id']; ?>, '<?php echo htmlspecialchars($booking['ma_dat_tour']); ?>')">
                                <i class="bi bi-check-circle"></i>
                                Xác nhận
                            </button>
                            <?php endif; ?>
                            
                            <?php if ($booking['trang_thai'] != 'cancelled'): ?>
                            <button class="btn-action btn-cancel"
                                    onclick="cancelBooking(<?php echo $booking['id']; ?>, '<?php echo htmlspecialchars($booking['ma_dat_tour']); ?>')">
                                <i class="bi bi-x-circle"></i>
                                Hủy
                            </button>
                            <?php endif; ?>
                            
                            <button class="btn-action btn-delete"
                                    onclick="deleteBooking(<?php echo $booking['id']; ?>, '<?php echo htmlspecialchars($booking['ma_dat_tour']); ?>')">
                                <i class="bi bi-trash"></i>
                                Xóa
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="booking-item">
                    <div style="text-align: center; padding: 40px;">
                        <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                        <p>Không có booking nào</p>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <div class="sidebar-overlay"></div>
    <script src="../../js/Main5.js"></script>
    <script>
        function confirmBooking(bookingId, bookingCode) {
            if (confirm('Xác nhận booking "' + bookingCode + '"?')) {
                window.location.href = '../../php/BookingCTL/confirm_booking.php?id=' + bookingId + 
                                      '&return=trip&tour=<?php echo $tour_id; ?>&departure=<?php echo $departure_date; ?>';
            }
        }
        
        function cancelBooking(bookingId, bookingCode) {
            if (confirm('Hủy booking "' + bookingCode + '"?')) {
                window.location.href = '../../php/BookingCTL/cancel_booking.php?id=' + bookingId + 
                                      '&return=trip&tour=<?php echo $tour_id; ?>&departure=<?php echo $departure_date; ?>';
            }
        }
        
        function deleteBooking(bookingId, bookingCode) {
            if (confirm('Bạn có chắc chắn muốn XÓA VĨNH VIỄN booking "' + bookingCode + '"?\n\nHành động này KHÔNG THỂ HOÀN TÁC!')) {
                window.location.href = '../../php/BookingCTL/delete_booking.php?id=' + bookingId + 
                                      '&return=trip&tour=<?php echo $tour_id; ?>&departure=<?php echo $departure_date; ?>';
            }
        }
        
        function exportTripBookings() {
            window.location.href = '../../php/BookingCTL/export_trip_bookings.php?tour=<?php echo $tour_id; ?>&departure=<?php echo $departure_date; ?>';
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>