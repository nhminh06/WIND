<?php 
session_start();
include '../../../db/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle cancellation request
if (isset($_POST['cancel_booking'])) {
    $booking_id = intval($_POST['booking_id']);
    
    // Kiểm tra xem booking có thể hủy không (chỉ hủy nếu chưa khởi hành)
    $check_query = "SELECT ngay_khoi_hanh, trang_thai FROM dat_tour WHERE id = ? AND user_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $booking_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $booking_data = $check_result->fetch_assoc();
        $ngay_khoi_hanh = strtotime($booking_data['ngay_khoi_hanh']);
        $today = strtotime(date('Y-m-d'));
        
        // Chỉ cho phép hủy nếu còn ít nhất 3 ngày trước ngày khởi hành
        if ($ngay_khoi_hanh > $today && ($ngay_khoi_hanh - $today) >= (3 * 24 * 60 * 60)) {
            $cancel_query = "UPDATE dat_tour SET trang_thai = 'cancelled' WHERE id = ? AND user_id = ?";
            $stmt = $conn->prepare($cancel_query);
            $stmt->bind_param("ii", $booking_id, $user_id);
            $stmt->execute();
            $stmt->close();
            $_SESSION['success_message'] = "Đã hủy đặt chỗ thành công!";
        } else {
            $_SESSION['error_message'] = "Không thể hủy đặt chỗ. Phải hủy trước ít nhất 3 ngày!";
        }
    }
    $check_stmt->close();
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch user's bookings with correct column names
$query = "SELECT 
            d.id,
            d.ma_dat_tour,
            d.tour_id,
            d.ngay_khoi_hanh,
            d.so_nguoi_lon,
            d.so_tre_em,
            d.so_tre_nho,
            d.tong_tien,
            d.trang_thai,
            d.ngay_dat,
            d.ghi_chu,
            t.ten_tour,
            t.hinh_anh,
            t.so_ngay,
            t.gia
          FROM dat_tour d
          JOIN tour t ON d.tour_id = t.id
          WHERE d.user_id = ?
          ORDER BY d.ngay_dat DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Chỗ Của Tôi</title>
    <link rel="stylesheet" href="../../../css/Main5_2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../css/rpusers.css" />
</head>
<body>
    <section class="my-bookings">
        <div class="container">
            <div class="page-header">
                <h2><i class="fas fa-ticket-alt"></i> Đặt Chỗ Của Tôi</h2>
                <a href="../index/tour.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Quay lại</a>
            </div>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php 
                    echo $_SESSION['error_message']; 
                    unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- Filter tabs -->
            <div class="filter-tabs">
                <button class="tab-btn active" data-status="all">
                    <i class="fas fa-list"></i> Tất cả (<?php echo $result->num_rows; ?>)
                </button>
                <button class="tab-btn" data-status="pending">
                    <i class="fas fa-clock"></i> Chờ xác nhận
                </button>
                <button class="tab-btn" data-status="confirmed">
                    <i class="fas fa-check"></i> Đã xác nhận
                </button>
                <button class="tab-btn" data-status="cancelled">
                    <i class="fas fa-times"></i> Đã hủy
                </button>
            </div>
            
            <?php if ($result->num_rows == 0): ?>
                <div class="no-bookings">
                    <i class="fas fa-calendar-times"></i>
                    <p>Bạn chưa có đặt chỗ nào.</p>
                    <a href="../index/tour.php" class="btn btn-primary">
                        <i class="fas fa-search"></i> Khám phá tour
                    </a>
                </div>
            <?php else: ?>
                <div class="bookings-grid">
                    <?php while ($booking = $result->fetch_assoc()): 
                        // Tính ngày về dựa trên số ngày tour
                        $ngay_ve = date('Y-m-d', strtotime($booking['ngay_khoi_hanh'] . ' + ' . ($booking['so_ngay'] - 1) . ' days'));
                        $tong_so_nguoi = $booking['so_nguoi_lon'] + $booking['so_tre_em'] + $booking['so_tre_nho'];
                        
                        // Kiểm tra có thể hủy không
                        $ngay_khoi_hanh = strtotime($booking['ngay_khoi_hanh']);
                        $today = strtotime(date('Y-m-d'));
                        $can_cancel = ($booking['trang_thai'] != 'cancelled' && 
                                      $ngay_khoi_hanh > $today && 
                                      ($ngay_khoi_hanh - $today) >= (3 * 24 * 60 * 60));
                    ?>
                        <div class="booking-card" data-status="<?php echo $booking['trang_thai']; ?>">
                            <div class="booking-image">
                                <img src="<?php echo htmlspecialchars("../../../uploads/" . $booking['hinh_anh'] ?: 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800'); ?>" 
                                     alt="<?php echo htmlspecialchars($booking['ten_tour']); ?>">
                                <?php
                                $status_class = '';
                                $status_text = '';
                                $status_icon = '';
                                switch($booking['trang_thai']) {
                                    case 'confirmed':
                                        $status_class = 'confirmed';
                                        $status_text = 'Đã xác nhận';
                                        $status_icon = 'fa-check-circle';
                                        break;
                                    case 'pending':
                                        $status_class = 'pending';
                                        $status_text = 'Chờ xác nhận';
                                        $status_icon = 'fa-clock';
                                        break;
                                    case 'cancelled':
                                        $status_class = 'cancelled';
                                        $status_text = 'Đã hủy';
                                        $status_icon = 'fa-times-circle';
                                        break;
                                    default:
                                        $status_class = 'pending';
                                        $status_text = 'Đang xử lý';
                                        $status_icon = 'fa-spinner';
                                }
                                ?>
                                <span class="badge badge-<?php echo $status_class; ?>">
                                    <i class="fas <?php echo $status_icon; ?>"></i>
                                    <?php echo $status_text; ?>
                                </span>
                            </div>
                            
                            <div class="booking-content">
                                <div class="booking-header">
                                    <h3><?php echo htmlspecialchars($booking['ten_tour']); ?></h3>
                                    <span class="booking-code">
                                        <i class="fas fa-barcode"></i>
                                        <?php echo htmlspecialchars($booking['ma_dat_tour']); ?>
                                    </span>
                                </div>
                                
                                <div class="booking-details">
                                    <div class="detail-row">
                                        <div class="detail-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            <div>
                                                <span class="label">Ngày đi</span>
                                                <span class="value"><?php echo date('d/m/Y', strtotime($booking['ngay_khoi_hanh'])); ?></span>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-calendar-check"></i>
                                            <div>
                                                <span class="label">Ngày về</span>
                                                <span class="value"><?php echo date('d/m/Y', strtotime($ngay_ve)); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="detail-row">
                                        <div class="detail-item">
                                            <i class="fas fa-users"></i>
                                            <div>
                                                <span class="label">Số người</span>
                                                <span class="value">
                                                    <?php echo $tong_so_nguoi; ?> người
                                                    <?php if ($booking['so_nguoi_lon'] > 0): ?>
                                                        (<?php echo $booking['so_nguoi_lon']; ?> NL
                                                    <?php endif; ?>
                                                    <?php if ($booking['so_tre_em'] > 0): ?>
                                                        <?php echo $booking['so_tre_em']; ?> TE
                                                    <?php endif; ?>
                                                    <?php if ($booking['so_tre_nho'] > 0): ?>
                                                        <?php echo $booking['so_tre_nho']; ?> TN
                                                    <?php endif; ?>)
                                                </span>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-moon"></i>
                                            <div>
                                                <span class="label">Thời gian</span>
                                                <span class="value"><?php echo $booking['so_ngay']; ?> ngày <?php echo $booking['so_ngay'] - 1; ?> đêm</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="detail-row">
                                        <div class="detail-item full-width">
                                            <i class="fas fa-money-bill-wave"></i>
                                            <div>
                                                <span class="label">Tổng tiền</span>
                                                <span class="value price"><?php echo number_format($booking['tong_tien'], 0, ',', '.'); ?> VNĐ</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($booking['ghi_chu'])): ?>
                                    <div class="detail-row">
                                        <div class="detail-item full-width">
                                            <i class="fas fa-sticky-note"></i>
                                            <div>
                                                <span class="label">Ghi chú</span>
                                                <span class="value note"><?php echo htmlspecialchars($booking['ghi_chu']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="booking-actions">
                                    <a href="booking-detail.php?id=<?php echo $booking['id']; ?>" class="btn btn-detail">
                                        <i class="fas fa-info-circle"></i> Chi tiết
                                    </a>
                                    
                                    <?php if ($can_cancel): ?>
                                        <form method="POST" class="cancel-form" onsubmit="return confirmCancel(event);">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <button type="submit" name="cancel_booking" class="btn btn-cancel">
                                                <i class="fas fa-ban"></i> Hủy đặt chỗ
                                            </button>
                                        </form>
                                    <?php elseif ($booking['trang_thai'] == 'cancelled'): ?>
                                        <button class="btn btn-disabled" disabled>
                                            <i class="fas fa-times"></i> Đã hủy
                                        </button>
                                    <?php elseif ($ngay_khoi_hanh <= $today): ?>
                                        <button class="btn btn-disabled" disabled>
                                            <i class="fas fa-clock"></i> Không thể hủy
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script>
        // Filter functionality
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const status = this.dataset.status;
                
                // Update active tab
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Filter cards
                document.querySelectorAll('.booking-card').forEach(card => {
                    if (status === 'all' || card.dataset.status === status) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Cancel confirmation
        function confirmCancel(event) {
            event.preventDefault();
            
            if (confirm('Bạn có chắc muốn hủy đặt chỗ này?\n\nLưu ý: Việc hủy phải tuân thủ chính sách hủy tour của chúng tôi.')) {
                event.target.submit();
            }
            
            return false;
        }

        // Auto hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>