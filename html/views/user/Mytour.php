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
    $cancel_query = "UPDATE dat_tour SET status = 'cancelled' WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($cancel_query);
    $stmt->bind_param("ii", $booking_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch user's bookings
$query = "SELECT b.*, t.name as tour_name, t.image_url, t.destination 
          FROM dat_tour b 
          JOIN tours t ON b.tour_id = t.id 
          WHERE b.user_id = ? 
          ORDER BY b.departure_date DESC";
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
    <link rel="stylesheet" href="../css/my-bookings.css">
</head>
<body>
    <section class="my-bookings">
        <div class="container">
            <h2>Đặt Chỗ Của Tôi</h2>
            
            <?php if ($result->num_rows == 0): ?>
                <div class="no-bookings">
                    <p>Bạn chưa có đặt chỗ nào.</p>
                    <a href="../tours.php" class="btn">Khám phá tour</a>
                </div>
            <?php else: ?>
                <?php while ($booking = $result->fetch_assoc()): ?>
                    <div class="booking-card">
                        <img src="<?php echo htmlspecialchars($booking['image_url'] ?: 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800'); ?>" 
                             alt="<?php echo htmlspecialchars($booking['tour_name']); ?>">
                        <div class="booking-info">
                            <h3><?php echo htmlspecialchars($booking['tour_name']); ?></h3>
                            <p><strong>Ngày đi:</strong> <?php echo date('d/m/Y', strtotime($booking['departure_date'])); ?></p>
                            <p><strong>Ngày về:</strong> <?php echo date('d/m/Y', strtotime($booking['return_date'])); ?></p>
                            <p><strong>Số người:</strong> <?php echo intval($booking['num_people']); ?></p>
                            <p><strong>Tổng tiền:</strong> <?php echo number_format($booking['total_price'], 0, ',', '.'); ?> VNĐ</p>
                            
                            <?php
                            $status_class = '';
                            $status_text = '';
                            switch($booking['status']) {
                                case 'confirmed':
                                    $status_class = 'confirmed';
                                    $status_text = 'Đã xác nhận';
                                    break;
                                case 'pending':
                                    $status_class = 'pending';
                                    $status_text = 'Đang chờ';
                                    break;
                                case 'cancelled':
                                    $status_class = 'cancelled';
                                    $status_text = 'Đã hủy';
                                    break;
                                default:
                                    $status_class = 'pending';
                                    $status_text = 'Đang xử lý';
                            }
                            ?>
                            <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                            
                            <div class="actions">
                                <a href="booking-detail.php?id=<?php echo $booking['id']; ?>" class="btn detail">Chi tiết</a>
                                <?php if ($booking['status'] != 'cancelled'): ?>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn hủy đặt chỗ này?');">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <button type="submit" name="cancel_booking" class="btn cancel">Hủy</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </section>

    <script src="../js/my-bookings.js"></script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>