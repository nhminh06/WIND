<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Tour Thành Công</title>
    <link rel="stylesheet" href="../../../css/Main5.css">
    <link rel="stylesheet" href="../../../css/Main5_1.css">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background: url('https://i.pinimg.com/1200x/1a/79/17/1a7917c5d95e49ab18bb4fa1595a70ef.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
       
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <div class="checkmark"></div>
        </div>
        
        <h1><i class="bi bi-fire"></i> Đặt Tour Thành Công!</h1>
        <p class="info-text">Cảm ơn bạn đã đặt tour.<br> Chúng tôi đã nhận được yêu cầu của bạn.</p>
        
        <?php if(isset($_GET['code'])) { ?>
        <div class="booking-code">
            <p><i class="bi bi-file-earmark-fill"></i> Mã đặt tour của bạn:</p>
            <strong><?php echo htmlspecialchars($_GET['code']); ?></strong>
        </div>
        <?php } ?>
        
        <p class="info-text">
            <i class="bi bi-envelope-at-fill"></i> Chúng tôi đã gửi email xác nhận đến địa chỉ của bạn.<br>
            <i class="bi bi-telephone-fill"></i> Nhân viên sẽ liên hệ trong vòng 24h để xác nhận thông tin.
        </p>
        
        <div class="btn-group">
            <a href="WebIndex.php" class="btn btn-primary">Về trang chủ</a>
            <a href="detailed_tour.php?id=<?php echo isset($_GET['tour_id']) ? $_GET['tour_id'] : ''; ?>" class="btn btn-secondary">Xem chi tiết tour</a>
        </div>
    </div>
</body>
</html>
<?php
// Clear session messages
unset($_SESSION['booking_success']);
unset($_SESSION['ma_dat_tour']);
?>