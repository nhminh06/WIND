<?php 
session_start();
include '../../db/db.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Vui lòng đăng nhập để đánh giá!';
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: my-bookings.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$tour_id = isset($_POST['tour_id']) ? intval($_POST['tour_id']) : 0;
$booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;

// Validate
if ($tour_id <= 0 || $booking_id <= 0) {
    $_SESSION['error'] = 'Dữ liệu không hợp lệ!';
    header('Location: ../../html/views/user/users.php');
    exit();
}

// Kiểm tra booking có tồn tại và thuộc user không
$check_query = "
SELECT d.*, t.ten_tour, u.ho_ten 
FROM dat_tour d
JOIN tour t ON d.tour_id = t.id
JOIN user u ON d.user_id = u.id
WHERE d.id = ? AND d.user_id = ? AND d.tour_id = ?
";

$stmt = $conn->prepare($check_query);
$stmt->bind_param("iii", $booking_id, $user_id, $tour_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = 'Không tìm thấy thông tin đặt tour!';
    header('Location: ../../html/views/user/users.php');
    exit();
}

$booking = $result->fetch_assoc();
$stmt->close();

// Kiểm tra tour đã hoàn thành chưa
if ($booking['trang_thai'] != 'confirmed' || $booking['trang_thai_chuyen_di'] != 'completed') {
    $_SESSION['error'] = 'Bạn chỉ có thể đánh giá sau khi chuyến đi hoàn thành!';
    header('Location: ../../html/views/user/booking-detail.php?id=' . $booking_id);
    exit();
}

// Kiểm tra đã đánh giá chưa (theo user_id)
$check_review = "SELECT id FROM danh_gia WHERE tour_id = ? AND user_id = ?";
$stmt = $conn->prepare($check_review);
$stmt->bind_param("ii", $tour_id, $user_id);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    $_SESSION['error'] = 'Bạn đã đánh giá tour này rồi!';
    header('Location: ../../html/views/user/booking-detail.php?id=' . $booking_id);
    exit();
}
$stmt->close();

// Lấy dữ liệu từ form
$rating = isset($_POST['rating']) ? floatval($_POST['rating']) : 0;
$rating = $rating * 2;
$review_content = isset($_POST['review_content']) ? trim($_POST['review_content']) : '';

// Validate
if ($rating < 2 || $rating > 10) {
    $_SESSION['error'] = "Vui lòng chọn số sao từ 1 đến 5!";
    header('Location: ../../html/views/user/review-tour.php?tour_id=' . $tour_id . '&booking_id=' . $booking_id);
    exit();
}

if (empty($review_content)) {
    $_SESSION['error'] = "Vui lòng nhập nội dung đánh giá!";
    header('Location: ../../html/views/user/review-tour.php?tour_id=' . $tour_id . '&booking_id=' . $booking_id);
    exit();
}

if (strlen($review_content) < 10) {
    $_SESSION['error'] = "Nội dung đánh giá phải có ít nhất 10 ký tự!";
    header('Location: ../../html/views/user/review-tour.php?tour_id=' . $tour_id . '&booking_id=' . $booking_id);
    exit();
}

if (strlen($review_content) > 1000) {
    $_SESSION['error'] = "Nội dung đánh giá không được vượt quá 1000 ký tự!";
    header('Location: ../../html/views/user/review-tour.php?tour_id=' . $tour_id . '&booking_id=' . $booking_id);
    exit();
}

// Lưu đánh giá (KHÔNG dùng ten_khach_hang nữa)
$insert_query = "
INSERT INTO danh_gia (tour_id, user_id, diem, nhan_xet)
VALUES (?, ?, ?, ?)
";

$stmt = $conn->prepare($insert_query);

$stmt->bind_param("iids", $tour_id, $user_id, $rating, $review_content);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Cảm ơn bạn đã đánh giá! Đánh giá của bạn đã được ghi nhận thành công.';
    $stmt->close();
    $conn->close();
    header('Location: ../../html/views/user/booking-detail.php?id=' . $booking_id);
    exit();
} else {
    $_SESSION['error'] = "Có lỗi xảy ra khi lưu đánh giá. Vui lòng thử lại!";
    $stmt->close();
    $conn->close();
    header('Location: ../../html/views/user/review-tour.php?tour_id=' . $tour_id . '&booking_id=' . $booking_id);
    exit();
}
?>
