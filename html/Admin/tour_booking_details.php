<?php
session_start();
include '../../db/db.php';

// Kiểm tra quyền admin hoặc staff
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    header('Location: ../../index.php');
    exit();
}

// Lấy ID đặt tour từ URL
$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($booking_id <= 0) {
    header('Location: manage_bookings.php');
    exit();
}

// Lấy thông tin đặt tour
$sql = "SELECT d.*, t.ten_tour, t.hinh_anh, u.ho_ten as user_name 
        FROM dat_tour d 
        LEFT JOIN tour t ON d.tour_id = t.id 
        LEFT JOIN user u ON d.user_id = u.id 
        WHERE d.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = 'Không tìm thấy đặt tour này.';
    header('Location: manage_bookings.php');
    exit();
}

$booking = $result->fetch_assoc();
$stmt->close();

// Xử lý xác nhận thanh toán (nếu form được submit)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_payment'])) {
    $new_status = $_POST['payment_status'];
    $payment_date = !empty($_POST['payment_date']) ? $_POST['payment_date'] : null;
    $notes = trim($_POST['notes']);

    // Cập nhật trạng thái thanh toán
    $update_sql = "UPDATE dat_tour SET trang_thai_thanh_toan = ?, ngay_thanh_toan = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('ssi', $new_status, $payment_date, $booking_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['success'] = 'Cập nhật trạng thái thanh toán thành công.';
    } else {
        $_SESSION['error'] = 'Lỗi khi cập nhật trạng thái thanh toán.';
    }
    $update_stmt->close();
    
    // Reload trang để hiển thị cập nhật
    header("Location: tour_booking_details.php?id=$booking_id");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đặt tour #<?php echo htmlspecialchars($booking['ma_dat_tour']); ?></title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .content { padding: 20px; }
        
    </style>
</head>
<body>
    <aside class="sidebar">
        <h2 class="logo">WIND Admin</h2>
        <?php include '../../includes/Adminnav.php'; ?>
    </aside>

    <div class="main">
        <header class="header">
            <button class="menu-toggle">
                <span></span><span></span><span></span>
            </button>
            <h1>Chi tiết đặt tour #<?php echo htmlspecialchars($booking['ma_dat_tour']); ?></h1>
            <div class="admin-info">
                <p>Xin chào <?php echo $_SESSION['ho_ten'] ?? 'Admin'; ?></p>
                <button onclick="window.location.href='manage_bookings.php'" class="logout">Quay lại</button>
            </div>
        </header>

        <section class="content">
            <!-- Thông báo -->
            <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
            <?php endif; ?>

            <!-- Thông tin đặt tour -->
            <div class="detail-card">
                <h3><i class="bi bi-info-circle"></i> Thông tin đặt tour</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Mã đặt tour</div>
                        <div class="info-value"><?php echo htmlspecialchars($booking['ma_dat_tour']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tour</div>
                        <div class="info-value" style="display: flex; align-items: center;">
                            <img src="../../uploads/<?php echo htmlspecialchars($booking['hinh_anh']); ?>" alt="Tour" class="tour-image">
                            <?php echo htmlspecialchars($booking['ten_tour']); ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Khách hàng</div>
                        <div class="info-value"><?php echo htmlspecialchars($booking['ho_ten']); ?> (<?php echo htmlspecialchars($booking['user_name'] ?? 'Khách vãng lai'); ?>)</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?php echo htmlspecialchars($booking['email']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Số điện thoại</div>
                        <div class="info-value"><?php echo htmlspecialchars($booking['sdt']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Địa chỉ</div>
                        <div class="info-value"><?php echo htmlspecialchars($booking['dia_chi'] ?? 'Không có'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Ngày khởi hành</div>
                        <div class="info-value"><?php echo date('d/m/Y', strtotime($booking['ngay_khoi_hanh'])); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Số người</div>
                        <div class="info-value">
                            Người lớn: <?php echo $booking['so_nguoi_lon']; ?>, 
                            Trẻ em: <?php echo $booking['so_tre_em']; ?>, 
                            Trẻ nhỏ: <?php echo $booking['so_tre_nho']; ?> 
                            (Tổng: <?php echo $booking['so_nguoi_lon'] + $booking['so_tre_em'] + $booking['so_tre_nho']; ?>)
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tổng tiền</div>
                        <div class="info-value" style="font-weight: bold; color: #28a745;"><?php echo number_format($booking['tong_tien'], 0, ',', '.'); ?> ₫</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Ghi chú</div>
                        <div class="info-value"><?php echo htmlspecialchars($booking['ghi_chu'] ?? 'Không có'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Ngày đặt</div>
                        <div class="info-value"><?php echo date('d/m/Y H:i', strtotime($booking['ngay_dat'])); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Trạng thái đặt tour</div>
                        <div class="info-value">
                            <?php
                            $status_text = '';
                            switch($booking['trang_thai']) {
                                case 'confirmed': $status_text = 'Đã xác nhận'; break;
                                case 'pending': $status_text = 'Chờ xác nhận'; break;
                                case 'cancelled': $status_text = 'Đã hủy'; break;
                            }
                            echo $status_text;
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin thanh toán -->
            <div class="detail-card">
                <h3><i class="bi bi-credit-card"></i> Thông tin thanh toán</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Phương thức thanh toán</div>
                        <div class="info-value"><?php echo $booking['phuong_thuc_thanh_toan'] == 'chuyen_khoan' ? 'Chuyển khoản' : 'Tiền mặt'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Hình ảnh thanh toán</div>
                        <div class="info-value">
                            <?php if ($booking['hinh_anh_thanh_toan']): ?>
                                <a href="../../uploads/<?php echo htmlspecialchars($booking['hinh_anh_thanh_toan']); ?>" target="_blank" style="color: #3dcce2;">Xem hình ảnh</a>
                            <?php else: ?>
                                Không có
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Ngày thanh toán</div>
                        <div class="info-value"><?php echo $booking['ngay_thanh_toan'] ? date('d/m/Y H:i', strtotime($booking['ngay_thanh_toan'])) : 'Chưa thanh toán'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Trạng thái thanh toán</div>
                        <div class="info-value">
                            <span class="badge badge-<?php echo $booking['trang_thai_thanh_toan']; ?>">
                                <?php
                                $payment_status_text = '';
                                switch($booking['trang_thai_thanh_toan']) {
                                    case 'cho_xac_nhan': $payment_status_text = 'Chờ xác nhận'; break;
                                    case 'da_thanh_toan': $payment_status_text = 'Đã thanh toán'; break;
                                    case 'tu_choi': $payment_status_text = 'Từ chối'; break;
                                }
                                echo $payment_status_text;
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form xác nhận thanh toán -->
           <?php if ($booking['trang_thai_thanh_toan'] == 'cho_xac_nhan'): ?>
<div class="detail-card payment-form">
    <h3><i class="bi bi-check-circle"></i> Xác nhận thanh toán</h3>
    <form method="POST" action="../../php/BookingCTL/confirm_payment.php?id=<?php echo $booking_id; ?>">
        <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
        <div class="form-group">
            <label for="payment_status">Trạng thái thanh toán</label>
            <select name="payment_status" id="payment_status" required>
                <option value="da_thanh_toan">Đã thanh toán</option>
                <option value="tu_choi">Từ chối</option>
            </select>
        </div>
        <div class="form-group">
            <label for="payment_date">Ngày thanh toán</label>
            <input type="datetime-local" name="payment_date" id="payment_date" value="<?php echo date('Y-m-d\TH:i'); ?>">
        </div>
       
        <button type="submit" class="btn-add">Cập nhật thanh toán</button>
    </form>
</div>
<?php endif; ?>
        </section>
    </div>

    <div class="sidebar-overlay"></div>
    <script src="../../js/Main5.js"></script>
</body>
</html>
