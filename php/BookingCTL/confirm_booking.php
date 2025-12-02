<!-- File: ../../php/BookingCTL/confirm_booking.php -->
<?php
session_start();
include '../../db/db.php';

// Kiểm tra quyền
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    header('Location: ../../index.php');
    exit();
}

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($booking_id > 0) {
    $sql = "UPDATE dat_tour SET trang_thai = 'confirmed' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Đã xác nhận đặt tour thành công!";
    } else {
        $_SESSION['error'] = "Lỗi khi xác nhận đặt tour!";
    }
    $stmt->close();
}

header('Location: ../../html/admin/manage_bookings.php');
exit();
?>

<!-- File: ../../php/BookingCTL/cancel_booking.php -->
<?php
session_start();
include '../../db/db.php';

// Kiểm tra quyền
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    header('Location: ../../index.php');
    exit();
}

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($booking_id > 0) {
    $sql = "UPDATE dat_tour SET trang_thai = 'cancelled' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Đã hủy đặt tour thành công!";
    } else {
        $_SESSION['error'] = "Lỗi khi hủy đặt tour!";
    }
    $stmt->close();
}

header('Location: ../../admin/bookings/manage_bookings.php');
exit();
?>

<!-- File: ../../php/BookingCTL/delete_booking.php -->
<?php
session_start();
include '../../db/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($booking_id > 0) {
    // Lấy thông tin booking trước khi xóa
    $sql = "SELECT ma_dat_tour FROM dat_tour WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();
    
    // Xóa booking
    $sql = "DELETE FROM dat_tour WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Đã xóa đặt tour " . $booking['ma_dat_tour'] . " thành công!";
    } else {
        $_SESSION['error'] = "Lỗi khi xóa đặt tour!";
    }
    $stmt->close();
}

header('Location: ../../admin/bookings/manage_bookings.php');
exit();
?>

<!-- File: ../../php/BookingCTL/export_bookings.php -->
<?php
session_start();
include '../../db/db.php';

// Kiểm tra quyền
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    header('Location: ../../index.php');
    exit();
}

// Lấy tham số filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$tour_filter = isset($_GET['tour']) ? $_GET['tour'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Query
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

if (!empty($search)) {
    $sql .= " AND (d.ma_dat_tour LIKE ? OR d.ho_ten LIKE ? OR d.email LIKE ? OR d.sdt LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ssss';
}

if (!empty($status_filter)) {
    $sql .= " AND d.trang_thai = ?";
    $params[] = $status_filter;
    $types .= 's';
}

if (!empty($tour_filter)) {
    $sql .= " AND d.tour_id = ?";
    $params[] = $tour_filter;
    $types .= 'i';
}

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

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=dat_tour_' . date('Y-m-d_H-i-s') . '.csv');

// Create output stream
$output = fopen('php://output', 'w');

// Add BOM for UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add header row
fputcsv($output, [
    'Mã đặt tour',
    'Tour',
    'Khách hàng',
    'Email',
    'Số điện thoại',
    'Địa chỉ',
    'Ngày khởi hành',
    'Số người lớn',
    'Số trẻ em',
    'Số trẻ nhỏ',
    'Tổng tiền',
    'Ghi chú',
    'Ngày đặt',
    'Trạng thái'
]);

// Add data rows
while ($row = $result->fetch_assoc()) {
    $status_text = '';
    switch($row['trang_thai']) {
        case 'confirmed': $status_text = 'Đã xác nhận'; break;
        case 'pending': $status_text = 'Chờ xác nhận'; break;
        case 'cancelled': $status_text = 'Đã hủy'; break;
    }
    
    fputcsv($output, [
        $row['ma_dat_tour'],
        $row['ten_tour'],
        $row['ho_ten'],
        $row['email'],
        $row['sdt'],
        $row['dia_chi'],
        date('d/m/Y', strtotime($row['ngay_khoi_hanh'])),
        $row['so_nguoi_lon'],
        $row['so_tre_em'],
        $row['so_tre_nho'],
        number_format($row['tong_tien'], 0, ',', '.') . ' VNĐ',
        $row['ghi_chu'],
        date('d/m/Y H:i', strtotime($row['ngay_dat'])),
        $status_text
    ]);
}

fclose($output);
$stmt->close();
$conn->close();
exit();
?>