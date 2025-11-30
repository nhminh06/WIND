<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Tour Thành Công</title>
    <link rel="stylesheet" href="../../../css/Main5.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .success-container {
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            max-width: 600px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideUp 0.5s ease;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(200deg, #4caf50, #45a049);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.6s ease 0.2s both;
        }
        @keyframes scaleIn {
            from { transform: scale(0) rotate(-180deg); }
            to { transform: scale(1) rotate(0); }
        }
        .checkmark {
    width: 50px;
    height: 30px;
    border: 4px solid white;
    border-top: none;
    border-right: none;
    transform: rotate(-45deg);
    margin-top: -15px;
}
        h1 {
            color: #333;
            font-size: 2.2em;
            margin-bottom: 15px;
        }
        .booking-code {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 25px;
            border-radius: 15px;
            margin: 30px 0;
            border: 2px dashed #667eea;
        }
        .booking-code p {
            color: #666;
            margin-bottom: 10px;
            font-size: 0.9em;
        }
        .booking-code strong {
            color: #667eea;
            font-size: 1.8em;
            letter-spacing: 2px;
        }
        .info-text {
            color: #666;
            line-height: 1.8;
            margin: 20px 0;
        }
        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 35px;
        }
        .btn {
            padding: 15px 35px;
            border: none;
            border-radius: 10px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }
        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        .btn-secondary:hover {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <div class="checkmark"></div>
        </div>
        
        <h1>🎉 Đặt Tour Thành Công!</h1>
        <p class="info-text">Cảm ơn bạn đã đặt tour. Chúng tôi đã nhận được yêu cầu của bạn.</p>
        
        <?php if(isset($_GET['code'])) { ?>
        <div class="booking-code">
            <p>📋 Mã đặt tour của bạn:</p>
            <strong><?php echo htmlspecialchars($_GET['code']); ?></strong>
        </div>
        <?php } ?>
        
        <p class="info-text">
            ✉️ Chúng tôi đã gửi email xác nhận đến địa chỉ của bạn.<br>
            📞 Nhân viên sẽ liên hệ trong vòng 24h để xác nhận thông tin.
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