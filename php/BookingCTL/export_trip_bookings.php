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
    $_SESSION['error'] = 'Thông tin chuyến đi không hợp lệ!';
    header('Location: ../admin/manage_trip.php');
    exit();
}

// Lấy thông tin tour
$sql_tour = "SELECT * FROM tour WHERE id = ?";
$stmt_tour = $conn->prepare($sql_tour);
$stmt_tour->bind_param('i', $tour_id);
$stmt_tour->execute();
$tour_info = $stmt_tour->get_result()->fetch_assoc();

if (!$tour_info) {
    $_SESSION['error'] = 'Không tìm thấy thông tin tour!';
    header('Location: ../admin/manage_trip.php');
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

// Tạo tên file
$tour_name_short = mb_substr($tour_info['ten_tour'], 0, 30);
$tour_name_short = preg_replace('/[^a-zA-Z0-9_\-\s]/', '', $tour_name_short);
$tour_name_short = str_replace(' ', '_', $tour_name_short);
$filename = "ChuyenDi_" . $tour_name_short . "_" . date('Ymd', strtotime($departure_date)) . "_" . date('His') . ".xls";

// Set headers cho file Excel
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Thêm BOM để Excel hiển thị đúng tiếng Việt
echo "\xEF\xBB\xBF";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .header h2 {
            color: #3498db;
            font-size: 20px;
            margin-bottom: 5px;
        }
        .info-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 180px;
        }
        .stats-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #e3f2fd;
            border: 1px solid #90caf9;
        }
        .stats-grid {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }
        .stat-item {
            text-align: center;
            margin: 10px;
            padding: 10px;
            background-color: white;
            border-radius: 5px;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        .stat-label {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        .status-confirmed {
            color: green;
            font-weight: bold;
        }
        .status-pending {
            color: orange;
            font-weight: bold;
        }
        .status-cancelled {
            color: red;
            font-weight: bold;
        }
        .payment-paid {
            color: green;
            font-weight: bold;
        }
        .payment-pending {
            color: orange;
            font-weight: bold;
        }
        .payment-rejected {
            color: red;
            font-weight: bold;
        }
        .total-row {
            background-color: #fff3cd;
            font-weight: bold;
        }
        .grand-total-row {
            background-color: #d4edda;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BÁO CÁO CHI TIẾT CHUYẾN ĐI</h1>
        <h2><?php echo htmlspecialchars($tour_info['ten_tour']); ?></h2>
    </div>
    
    <div class="info-section">
        <?php if (isset($tour_info['ma_tour']) && !empty($tour_info['ma_tour'])): ?>
        <div class="info-row">
            <span class="info-label">Mã Tour:</span>
            <span><?php echo htmlspecialchars($tour_info['ma_tour']); ?></span>
        </div>
        <?php endif; ?>
        <div class="info-row">
            <span class="info-label">Ngày khởi hành:</span>
            <span><?php echo date('d/m/Y', strtotime($departure_date)); ?> (<?php echo date('l', strtotime($departure_date)); ?>)</span>
        </div>
        <?php if (isset($tour_info['so_ngay']) && !empty($tour_info['so_ngay'])): ?>
        <div class="info-row">
            <span class="info-label">Thời gian tour:</span>
            <span><?php echo $tour_info['so_ngay']; ?> ngày</span>
        </div>
        <?php endif; ?>
        <?php if (isset($tour_info['diem_khoi_hanh']) && !empty($tour_info['diem_khoi_hanh'])): ?>
        <div class="info-row">
            <span class="info-label">Điểm khởi hành:</span>
            <span><?php echo htmlspecialchars($tour_info['diem_khoi_hanh']); ?></span>
        </div>
        <?php endif; ?>
        <div class="info-row">
            <span class="info-label">Ngày xuất báo cáo:</span>
            <span><?php echo date('d/m/Y H:i:s'); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Người xuất:</span>
            <span><?php echo isset($_SESSION['ho_ten']) ? htmlspecialchars($_SESSION['ho_ten']) : 'Admin'; ?></span>
        </div>
    </div>

    <div class="stats-section">
        <h3 style="margin-top: 0; color: #2c3e50;">THỐNG KÊ TỔNG QUAN</h3>
        <table style="border: none;">
            <tr>
                <td style="border: none; text-align: center; padding: 15px;">
                    <div class="stat-number"><?php echo $total_bookings; ?></div>
                    <div class="stat-label">Tổng Booking</div>
                </td>
                <td style="border: none; text-align: center; padding: 15px;">
                    <div class="stat-number"><?php echo $total_customers; ?></div>
                    <div class="stat-label">Tổng Khách</div>
                </td>
                <td style="border: none; text-align: center; padding: 15px;">
                    <div class="stat-number" style="color: green;"><?php echo $total_confirmed; ?></div>
                    <div class="stat-label">Đã Xác Nhận</div>
                </td>
                <td style="border: none; text-align: center; padding: 15px;">
                    <div class="stat-number" style="color: orange;"><?php echo $total_pending; ?></div>
                    <div class="stat-label">Chờ Xác Nhận</div>
                </td>
                <td style="border: none; text-align: center; padding: 15px;">
                    <div class="stat-number" style="color: red;"><?php echo $total_cancelled; ?></div>
                    <div class="stat-label">Đã Hủy</div>
                </td>
            </tr>
        </table>
        
        <table style="border: none; margin-top: 10px;">
            <tr>
                <td style="border: none; text-align: center; padding: 15px;">
                    <div class="stat-number"><?php echo $total_adults; ?></div>
                    <div class="stat-label">Người Lớn</div>
                </td>
                <td style="border: none; text-align: center; padding: 15px;">
                    <div class="stat-number"><?php echo $total_children; ?></div>
                    <div class="stat-label">Trẻ Em</div>
                </td>
                <td style="border: none; text-align: center; padding: 15px;">
                    <div class="stat-number"><?php echo $total_infants; ?></div>
                    <div class="stat-label">Trẻ Nhỏ</div>
                </td>
                <td style="border: none; text-align: center; padding: 15px;">
                    <div class="stat-number" style="color: #e74c3c;"><?php echo number_format($total_revenue, 0, ',', '.'); ?>₫</div>
                    <div class="stat-label">Tổng Doanh Thu</div>
                </td>
                <td style="border: none; text-align: center; padding: 15px;">
                    <div class="stat-number" style="color: #27ae60;"><?php echo number_format($total_confirmed_revenue, 0, ',', '.'); ?>₫</div>
                    <div class="stat-label">Doanh Thu Xác Nhận</div>
                </td>
            </tr>
        </table>
    </div>

    <h3 style="color: #2c3e50; margin-top: 30px;">DANH SÁCH BOOKING CHI TIẾT</h3>
    
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">STT</th>
                <th style="width: 100px;">Mã Booking</th>
                <th style="width: 150px;">Họ Tên</th>
                <th style="width: 150px;">Email</th>
                <th style="width: 100px;">Số Điện Thoại</th>
                <th style="width: 200px;">Địa Chỉ</th>
                <th style="width: 60px;">NL</th>
                <th style="width: 60px;">TE</th>
                <th style="width: 60px;">TN</th>
                <th style="width: 60px;">Tổng</th>
                <th style="width: 120px;">Tổng Tiền (VNĐ)</th>
                <th style="width: 100px;">Trạng Thái</th>
                <th style="width: 120px;">TT Thanh Toán</th>
                <th style="width: 200px;">Ghi Chú</th>
                <th style="width: 120px;">Ngày Đặt</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (count($bookings) > 0):
                $stt = 1;
                foreach ($bookings as $booking): 
                    $tong_so_nguoi = $booking['so_nguoi_lon'] + $booking['so_tre_em'] + $booking['so_tre_nho'];
                    
                    // Xác định trạng thái
                    $status_class = '';
                    $status_text = '';
                    switch($booking['trang_thai']) {
                        case 'confirmed':
                            $status_class = 'status-confirmed';
                            $status_text = 'Đã xác nhận';
                            break;
                        case 'pending':
                            $status_class = 'status-pending';
                            $status_text = 'Chờ xác nhận';
                            break;
                        case 'cancelled':
                            $status_class = 'status-cancelled';
                            $status_text = 'Đã hủy';
                            break;
                    }
                    
                    // Trạng thái thanh toán
                    $payment_class = '';
                    $payment_text = '';
                    switch($booking['trang_thai_thanh_toan']) {
                        case 'da_thanh_toan':
                            $payment_class = 'payment-paid';
                            $payment_text = 'Đã thanh toán';
                            break;
                        case 'cho_xac_nhan':
                            $payment_class = 'payment-pending';
                            $payment_text = 'Chưa thanh toán';
                            break;
                        case 'tu_choi':
                            $payment_class = 'payment-rejected';
                            $payment_text = 'Từ chối';
                            break;
                    }
            ?>
            <tr>
                <td style="text-align: center;"><?php echo $stt++; ?></td>
                <td><?php echo htmlspecialchars($booking['ma_dat_tour']); ?></td>
                <td><?php echo htmlspecialchars($booking['ho_ten']); ?></td>
                <td><?php echo htmlspecialchars($booking['email']); ?></td>
                <td><?php echo htmlspecialchars($booking['sdt']); ?></td>
                <td><?php echo htmlspecialchars($booking['dia_chi'] ?? ''); ?></td>
                <td style="text-align: center;"><?php echo $booking['so_nguoi_lon']; ?></td>
                <td style="text-align: center;"><?php echo $booking['so_tre_em']; ?></td>
                <td style="text-align: center;"><?php echo $booking['so_tre_nho']; ?></td>
                <td style="text-align: center; font-weight: bold;"><?php echo $tong_so_nguoi; ?></td>
                <td style="text-align: right;"><?php echo number_format($booking['tong_tien'], 0, ',', '.'); ?></td>
                <td class="<?php echo $status_class; ?>"><?php echo $status_text; ?></td>
                <td class="<?php echo $payment_class; ?>"><?php echo $payment_text; ?></td>
                <td><?php echo htmlspecialchars($booking['ghi_chu'] ?? ''); ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($booking['ngay_dat'])); ?></td>
            </tr>
            <?php 
                endforeach;
            ?>
            <!-- Tổng cộng -->
            <tr class="grand-total-row">
                <td colspan="6" style="text-align: right; padding-right: 10px;">TỔNG CỘNG:</td>
                <td style="text-align: center;"><?php echo $total_adults; ?></td>
                <td style="text-align: center;"><?php echo $total_children; ?></td>
                <td style="text-align: center;"><?php echo $total_infants; ?></td>
                <td style="text-align: center;"><?php echo $total_customers; ?></td>
                <td style="text-align: right;"><?php echo number_format($total_revenue, 0, ',', '.'); ?></td>
                <td colspan="4"></td>
            </tr>
            
            <!-- Doanh thu đã xác nhận -->
            <tr class="total-row">
                <td colspan="10" style="text-align: right; padding-right: 10px;">Doanh thu đã xác nhận (<?php echo $total_confirmed; ?> booking):</td>
                <td style="text-align: right; color: green;"><?php echo number_format($total_confirmed_revenue, 0, ',', '.'); ?></td>
                <td colspan="4"></td>
            </tr>
            
            <?php else: ?>
            <tr>
                <td colspan="15" style="text-align: center; padding: 20px;">Không có booking nào</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div style="margin-top: 30px; padding: 15px; background-color: #f8f9fa; border: 1px solid #dee2e6;">
        <h4 style="margin-top: 0;">GHI CHÚ:</h4>
        <p style="margin: 5px 0;">• NL: Người lớn | TE: Trẻ em | TN: Trẻ nhỏ</p>
        <p style="margin: 5px 0;">• Doanh thu được tính từ các booking đã xác nhận</p>
        <p style="margin: 5px 0;">• Báo cáo được tạo tự động từ hệ thống quản lý tour WIND</p>
    </div>
    
    <div style="margin-top: 20px; text-align: center; color: #7f8c8d; font-size: 12px;">
        <p>--- HẾT ---</p>
        <p>Hệ thống Quản lý Tour Du lịch WIND</p>
    </div>
</body>
</html>
<?php
$conn->close();
exit();
?>