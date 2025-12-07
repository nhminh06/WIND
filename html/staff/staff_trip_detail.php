<?php
session_start();
include '../../db/db.php';

// Kiểm tra quyền staff
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header('Location: ../../index.php');
    exit();
}

$staff_id = $_SESSION['user_id'];

// Lấy thông tin từ URL
$tour_id = isset($_GET['tour']) ? (int)$_GET['tour'] : 0;
$departure_date = isset($_GET['departure']) ? $_GET['departure'] : '';

if (!$tour_id || !$departure_date) {
    header('Location: TourSchedule.php');
    exit();
}

// Kiểm tra xem staff có phải là HDV của chuyến đi này không
$sql_check = "SELECT COUNT(*) as count FROM dat_tour 
              WHERE tour_id = ? AND ngay_khoi_hanh = ? AND huong_dan_vien_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param('isi', $tour_id, $departure_date, $staff_id);
$stmt_check->execute();
$check_result = $stmt_check->get_result()->fetch_assoc();

if ($check_result['count'] == 0) {
    $_SESSION['error'] = 'Bạn không có quyền xem chuyến đi này!';
    header('Location: MyTours.php');
    exit();
}

// Xử lý cập nhật ghi chú (chỉ cho phép thêm ghi chú)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'add_note') {
        $note_content = trim($_POST['note_content'] ?? '');
        
        if ($note_content) {
            $staff_name = $_SESSION['username'] ?? 'Staff';
            $note_text = "[HDV: $staff_name] " . $note_content;
            
            $sql_update_note = "UPDATE dat_tour SET 
                               ghi_chu = CONCAT(COALESCE(ghi_chu, ''), '\n[', NOW(), '] ', ?)
                               WHERE tour_id = ? AND ngay_khoi_hanh = ? AND huong_dan_vien_id = ?";
            
            $stmt_note = $conn->prepare($sql_update_note);
            $stmt_note->bind_param('sisi', $note_text, $tour_id, $departure_date, $staff_id);
            
            if ($stmt_note->execute()) {
                $_SESSION['success'] = 'Đã thêm ghi chú thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi khi thêm ghi chú!';
            }
        }
        
        header("Location: staff_trip_detail.php?tour=$tour_id&departure=$departure_date");
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
                    u.ho_ten as user_name,
                    u.sdt as user_phone
                 FROM dat_tour d
                 LEFT JOIN user u ON d.user_id = u.id
                 WHERE d.tour_id = ? AND d.ngay_khoi_hanh = ? AND d.huong_dan_vien_id = ?
                 ORDER BY d.ngay_dat ASC";

$stmt_bookings = $conn->prepare($sql_bookings);
$stmt_bookings->bind_param('isi', $tour_id, $departure_date, $staff_id);
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
$trip_notes = '';

while ($row = $result_bookings->fetch_assoc()) {
    $bookings[] = $row;
    $total_bookings++;
    $total_customers += ($row['so_nguoi_lon'] + $row['so_tre_em'] + $row['so_tre_nho']);
    if ($row['trang_thai'] == 'confirmed') {
        $confirmed_count++;
    }
    
    if ($trip_status == 'preparing' && !empty($row['trang_thai_chuyen_di'])) {
        $trip_status = $row['trang_thai_chuyen_di'];
    }
    
    if (empty($start_time) && !empty($row['thoi_gian_bat_dau_chuyen_di'])) {
        $start_time = date('H:i d/m/Y', strtotime($row['thoi_gian_bat_dau_chuyen_di']));
    }
    
    if (empty($end_time) && !empty($row['thoi_gian_ket_thuc_chuyen_di'])) {
        $end_time = date('H:i d/m/Y', strtotime($row['thoi_gian_ket_thuc_chuyen_di']));
    }
    
    if (empty($trip_notes) && !empty($row['ghi_chu'])) {
        $trip_notes = $row['ghi_chu'];
    }
}

// Lấy thông tin chi tiết tour
$sql_tour_detail = "SELECT * FROM tour_chi_tiet WHERE tour_id = ?";
$stmt_tour_detail = $conn->prepare($sql_tour_detail);
$stmt_tour_detail->bind_param('i', $tour_id);
$stmt_tour_detail->execute();
$tour_detail = $stmt_tour_detail->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết Chuyến Đi - Staff</title>
    <link rel="stylesheet" href="../../css/Staff.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
      
    </style>
</head>
<body>
    <?php include('../../includes/Staffnav.php'); ?>

    <div class="main-content">
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
                    <h2 style="color: #ffffff; font-size: 28px; margin-bottom: 15px;">
                        <?php echo htmlspecialchars($tour_info['ten_tour']); ?>
                    </h2>
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
        
        <a href="TourSchedule.php" class="back-button">
            <i class="bi bi-arrow-left"></i>
            Quay lại danh sách tour
        </a>

        <!-- Timeline -->
        <div class="info-card">
            <h2>
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

        <!-- Thông tin chi tiết tour -->
        <?php if ($tour_detail): ?>
        <div class="info-card">
            <h2>
                <i class="bi bi-info-circle"></i> Thông tin chi tiết tour
            </h2>
            <div class="tour-detail-info">
                <?php if ($tour_detail['ma_tour']): ?>
                <div class="info-row">
                    <div class="info-label"><i class="bi bi-qr-code"></i> Mã tour:</div>
                    <div class="info-value"><?php echo htmlspecialchars($tour_detail['ma_tour']); ?></div>
                </div>
                <?php endif; ?>
                
                <?php if ($tour_detail['diem_khoi_hanh']): ?>
                <div class="info-row">
                    <div class="info-label"><i class="bi bi-geo-alt"></i> Điểm khởi hành:</div>
                    <div class="info-value"><?php echo htmlspecialchars($tour_detail['diem_khoi_hanh']); ?></div>
                </div>
                <?php endif; ?>
                
                <?php if ($tour_detail['phuong_tien']): ?>
                <div class="info-row">
                    <div class="info-label"><i class="bi bi-bus-front"></i> Phương tiện:</div>
                    <div class="info-value"><?php echo htmlspecialchars($tour_detail['phuong_tien']); ?></div>
                </div>
                <?php endif; ?>
                
                <?php if ($tour_detail['mo_ta_ngan']): ?>
                <div class="info-row">
                    <div class="info-label"><i class="bi bi-card-text"></i> Mô tả:</div>
                    <div class="info-value"><?php echo htmlspecialchars($tour_detail['mo_ta_ngan']); ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Danh sách khách hàng -->
        <div class="info-card">
            <h2>
                <i class="bi bi-people"></i> Danh sách khách hàng (<?php echo $total_bookings; ?> booking)
            </h2>
            
            <?php if (empty($bookings)): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle"></i>
                    Chưa có booking nào cho chuyến đi này
                </div>
            <?php else: ?>
                <ul class="customer-list">
                    <?php foreach ($bookings as $booking): ?>
                    <li class="customer-item">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong><?php echo htmlspecialchars($booking['ma_dat_tour']); ?></strong>
                                - <?php echo htmlspecialchars($booking['ho_ten']); ?>
                                <br>
                                <small style="color: #666;">
                                    <i class="bi bi-telephone"></i> <?php echo htmlspecialchars($booking['sdt']); ?>
                                    | <i class="bi bi-envelope"></i> <?php echo htmlspecialchars($booking['email']); ?>
                                    | <i class="bi bi-people"></i> 
                                    <?php echo ($booking['so_nguoi_lon'] + $booking['so_tre_em'] + $booking['so_tre_nho']); ?> người
                                    (NL: <?php echo $booking['so_nguoi_lon']; ?>, 
                                    TE: <?php echo $booking['so_tre_em']; ?>, 
                                    TN: <?php echo $booking['so_tre_nho']; ?>)
                                </small>
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
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- Ghi chú -->
        <div class="info-card">
            <h2>
                <i class="bi bi-sticky"></i> Ghi chú chuyến đi
            </h2>
            
            <?php if ($trip_notes): ?>
            <div class="notes-display">
                <?php echo nl2br(htmlspecialchars($trip_notes)); ?>
            </div>
            <?php endif; ?>
            
            <div class="note-section">
                <h6 style="margin-bottom: 15px;">
                    <i class="bi bi-pencil"></i> Thêm ghi chú mới
                </h6>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add_note">
                    <textarea name="note_content" placeholder="Nhập ghi chú của bạn về chuyến đi..." required></textarea>
                    <button type="submit" class="btn-add-note">
                        <i class="bi bi-plus-circle"></i>
                        Thêm ghi chú
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>