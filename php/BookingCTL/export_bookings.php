<?php
session_start();
include '../../db/db.php';

// Kiểm tra quyền admin hoặc staff
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    header('Location: ../../index.php');
    exit();
}

// Lấy các tham số lọc từ URL
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$tour_filter = isset($_GET['tour']) ? $_GET['tour'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Query dữ liệu
$sql = "SELECT 
            d.*,
            t.ten_tour,
            u.ho_ten as user_name
        FROM dat_tour d
        LEFT JOIN tour t ON d.tour_id = t.id
        LEFT JOIN user u ON d.user_id = u.id
        WHERE 1=1";

$params = [];
$types = '';

// Thêm điều kiện tìm kiếm
if (!empty($search)) {
    $sql .= " AND (d.ma_dat_tour LIKE ? OR d.ho_ten LIKE ? OR d.email LIKE ? OR d.sdt LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ssss';
}

// Thêm điều kiện lọc trạng thái
if (!empty($status_filter)) {
    $sql .= " AND d.trang_thai = ?";
    $params[] = $status_filter;
    $types .= 's';
}

// Thêm điều kiện lọc tour
if (!empty($tour_filter)) {
    $sql .= " AND d.tour_id = ?";
    $params[] = $tour_filter;
    $types .= 'i';
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

// Chuẩn bị và thực thi query
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Tạo tên file với timestamp
$filename = "DanhSachDatTour_" . date('Y-m-d_His') . ".xls";

// Set headers cho file Excel
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Thêm BOM để Excel hiển thị đúng tiếng Việt
echo "\xEF\xBB\xBF";

// Tạo bảng HTML cho Excel
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        .header {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .info {
            margin-bottom: 10px;
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
    </style>
</head>
<body>
    <div class="header">
        <h2>DANH SÁCH ĐẶT TOUR</h2>
    </div>
    
    <div class="info">
        <p><strong>Ngày xuất:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
        <p><strong>Người xuất:</strong> <?php echo isset($_SESSION['ho_ten']) ? $_SESSION['ho_ten'] : 'Admin'; ?></p>
        <?php if (!empty($status_filter)): ?>
        <p><strong>Lọc trạng thái:</strong> 
            <?php 
            switch($status_filter) {
                case 'confirmed': echo 'Đã xác nhận'; break;
                case 'pending': echo 'Chờ xác nhận'; break;
                case 'cancelled': echo 'Đã hủy'; break;
            }
            ?>
        </p>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã đặt tour</th>
                <th>Tour</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Ngày khởi hành</th>
                <th>Số người lớn</th>
                <th>Số trẻ em</th>
                <th>Số trẻ nhỏ</th>
                <th>Tổng số người</th>
                <th>Tổng tiền (VNĐ)</th>
                <th>Trạng thái</th>
                <th>Trạng thái thanh toán</th>
                <th>Ghi chú</th>
                <th>Ngày đặt</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($result && $result->num_rows > 0):
                $stt = 1;
                $total_revenue = 0;
                $total_people = 0;
                while($row = $result->fetch_assoc()): 
                    $tong_so_nguoi = $row['so_nguoi_lon'] + $row['so_tre_em'] + $row['so_tre_nho'];
                    $total_people += $tong_so_nguoi;
                    
                    if ($row['trang_thai'] == 'confirmed') {
                        $total_revenue += $row['tong_tien'];
                    }
                    
                    // Xác định class cho trạng thái
                    $status_class = '';
                    $status_text = '';
                    switch($row['trang_thai']) {
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
                    
                    $payment_status_text = '';
                    switch($row['trang_thai_thanh_toan']) {
                        case 'da_thanh_toan':
                            $payment_status_text = 'Đã thanh toán';
                            break;
                        case 'cho_xac_nhan':
                            $payment_status_text = 'Chưa thanh toán';
                            break;
                        case 'tu_choi':
                            $payment_status_text = 'Từ chối';
                            break;
                    }
            ?>
            <tr>
                <td><?php echo $stt++; ?></td>
                <td><?php echo htmlspecialchars($row['ma_dat_tour']); ?></td>
                <td><?php echo htmlspecialchars($row['ten_tour']); ?></td>
                <td><?php echo htmlspecialchars($row['ho_ten']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['sdt']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['ngay_khoi_hanh'])); ?></td>
                <td><?php echo $row['so_nguoi_lon']; ?></td>
                <td><?php echo $row['so_tre_em']; ?></td>
                <td><?php echo $row['so_tre_nho']; ?></td>
                <td><?php echo $tong_so_nguoi; ?></td>
                <td><?php echo number_format($row['tong_tien'], 0, ',', '.'); ?></td>
                <td class="<?php echo $status_class; ?>"><?php echo $status_text; ?></td>
                <td><?php echo $payment_status_text; ?></td>
                <td><?php echo htmlspecialchars($row['ghi_chu'] ?? ''); ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($row['ngay_dat'])); ?></td>
            </tr>
            <?php 
                endwhile;
            ?>
            <!-- Tổng kết -->
            <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td colspan="10" style="text-align: right;">TỔNG CỘNG:</td>
                <td><?php echo $total_people; ?></td>
                <td><?php echo number_format($total_revenue, 0, ',', '.'); ?></td>
                <td colspan="4"></td>
            </tr>
            <?php else: ?>
            <tr>
                <td colspan="16" style="text-align: center;">Không có dữ liệu</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div style="margin-top: 20px;">
        <p><em>* Dữ liệu được xuất từ hệ thống quản lý tour WIND</em></p>
    </div>
</body>
</html>
<?php
$conn->close();
exit();
?>