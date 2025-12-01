<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    $_SESSION['login_message'] = 'Vui lòng đăng nhập để đặt tour!';
    header('Location:login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Tour</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: white;
            padding: 40px 20px;
            min-height: 100vh;
        }

        .booking-container {
            max-width: auto;
            margin: 0 auto;
            background: white;
            border-radius: 30px;
            box-shadow: 0 30px 90px rgba(0,0,0,0.2);
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .booking-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 50px 40px;
            position: relative;
            overflow: hidden;
        }

        .booking-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 15s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .booking-header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 700;
            position: relative;
            z-index: 1;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.2);
        }

        .booking-header > p {
            font-size: 1.1em;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }

        .user-info-box {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            padding: 20px 30px;
            border-radius: 15px;
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(255,255,255,0.2);
            position: relative;
            z-index: 1;
        }

        .user-info-box p {
            margin: 0;
            font-size: 1em;
            font-weight: 500;
        }

        .tour-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 40px;
            position: relative;
        }

        .tour-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .tour-info-content {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 40px;
            align-items: start;
        }

        .tour-image {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
            transition: transform 0.3s ease;
        }

        .tour-image:hover {
            transform: scale(1.03);
        }

        .tour-details h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.8em;
            font-weight: 600;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .info-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
        }

        .info-item svg {
            width: 24px;
            height: 24px;
            color: #667eea;
            flex-shrink: 0;
        }

        .info-item span {
            font-weight: 500;
            color: #555;
        }

        .booking-form {
            padding: 50px;
        }

        .form-section {
            margin-bottom: 45px;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .form-section h3 {
            color: #333;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
            font-size: 1.4em;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            font-weight: 600;
            font-size: 0.95em;
        }

        .form-group label span {
            color: #ff4757;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.15);
            transform: translateY(-2px);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .quantity-selector {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .quantity-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 0;
            border-bottom: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .quantity-item:hover {
            background: rgba(102, 126, 234, 0.05);
            margin: 0 -15px;
            padding: 25px 15px;
            border-radius: 12px;
        }

        .quantity-item:last-child {
            border-bottom: none;
        }

        .quantity-label {
            flex: 1;
        }

        .quantity-label strong {
            display: block;
            color: #333;
            margin-bottom: 5px;
            font-size: 1.1em;
        }

        .quantity-label small {
            color: #666;
            font-size: 0.9em;
        }

        .quantity-price {
            color: #667eea;
            font-weight: 700;
            margin: 0 30px;
            font-size: 1.1em;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .quantity-controls button {
            width: 45px;
            height: 45px;
            border: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            cursor: pointer;
            font-size: 20px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .quantity-controls button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .quantity-controls button:active {
            transform: translateY(-1px);
        }

        .quantity-controls span {
            min-width: 40px;
            text-align: center;
            font-weight: 700;
            color: #333;
            font-size: 1.2em;
        }

        .price-summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 35px;
            border-radius: 20px;
            margin: 30px 0;
            color: white;
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.3);
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            font-size: 1.05em;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .price-row:last-child {
            border-bottom: none;
        }

        .price-row.total {
            margin-top: 15px;
            padding-top: 25px;
            font-size: 1.5em;
            font-weight: 700;
            border-top: 3px solid rgba(255,255,255,0.5);
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 22px;
            border-radius: 15px;
            font-size: 1.3em;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5);
        }

        .submit-btn:active {
            transform: translateY(-1px);
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: #f8f9fa;
            color: #667eea;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid #e0e0e0;
        }

        .back-link:hover {
            background: white;
            border-color: #667eea;
            transform: translateX(-5px);
        }

        .message {
            padding: 20px 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            display: none;
            font-weight: 500;
            animation: slideDown 0.4s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border-left: 5px solid #28a745;
        }

        .message.error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border-left: 5px solid #dc3545;
        }

        .message.show {
            display: block;
        }

        @media (max-width: 968px) {
            .tour-info-content {
                grid-template-columns: 1fr;
            }

            .booking-form {
                padding: 30px 25px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .booking-header h1 {
                font-size: 2em;
            }

            .quantity-item {
                flex-wrap: wrap;
                gap: 15px;
            }

            .quantity-price {
                margin: 0;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 20px 10px;
            }

            .booking-header {
                padding: 30px 20px;
            }

            .booking-header h1 {
                font-size: 1.6em;
            }

            .tour-info {
                padding: 25px 20px;
            }

            .booking-form {
                padding: 25px 20px;
            }

            .price-summary {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="booking-container">
        <div class="booking-header">
            <h1>🎫 Đặt Tour Du Lịch</h1>
            <p>Điền thông tin để hoàn tất đặt tour của bạn</p>
            <div class="user-info-box">
                <p>👤 Xin chào: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
            </div>
        </div>

        <?php
        if(isset($_GET['id'])) {
            include '../../../db/db.php';
            $matour = intval($_GET['id']);
            
            $sql_tour = "SELECT * FROM tour WHERE id = $matour";
            $result_tour = mysqli_query($conn, $sql_tour);
            
            if(mysqli_num_rows($result_tour) > 0) {
                $tour = mysqli_fetch_assoc($result_tour);
                
                $sql_gia = "SELECT * FROM lich_khoi_hanh WHERE tour_id = $matour LIMIT 1";
                $result_gia = mysqli_query($conn, $sql_gia);
                $gia = mysqli_fetch_assoc($result_gia);
        ?>

        <div class="tour-info">
            <div class="tour-info-content">
                <img class="tour-image" src="../../../uploads/<?php echo htmlspecialchars($tour['hinh_anh']); ?>" alt="<?php echo htmlspecialchars($tour['ten_tour']); ?>">
                <div class="tour-details">
                    <h2><?php echo htmlspecialchars($tour['ten_tour']); ?></h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span><?php echo htmlspecialchars($tour['so_ngay']); ?> ngày</span>
                        </div>
                        <div class="info-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Việt Nam</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form class="booking-form" id="bookingForm" method="POST" action="process_booking.php">
            <input type="hidden" name="tour_id" value="<?php echo $matour; ?>">
            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
            
            <?php if(isset($_SESSION['booking_error'])): ?>
            <div class="message error show">
                <?php 
                echo $_SESSION['booking_error']; 
                unset($_SESSION['booking_error']);
                ?>
            </div>
            <?php endif; ?>

            <div class="form-section">
                <h3>📋 Thông Tin Khách Hàng</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Họ và tên <span>*</span></label>
                        <input type="text" name="ho_ten" required placeholder="Nguyễn Văn A">
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại <span>*</span></label>
                        <input type="tel" name="sdt" required placeholder="0912345678" pattern="[0-9]{10,11}">
                    </div>
                    <div class="form-group">
                        <label>Email <span>*</span></label>
                        <input type="email" name="email" required placeholder="example@email.com">
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ</label>
                        <input type="text" name="dia_chi" placeholder="Số nhà, đường, quận/huyện, tỉnh/thành">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>📅 Thời Gian và Số Lượng</h3>
                <div class="form-group">
                    <label>Ngày khởi hành <span>*</span></label>
                    <input type="date" name="ngay_khoi_hanh" required min="<?php echo date('Y-m-d'); ?>">
                </div>

                <?php if($gia): ?>
                <div class="quantity-selector">
                    <div class="quantity-item">
                        <div class="quantity-label">
                            <strong>Người lớn</strong>
                            <small>(Trên 9 tuổi)</small>
                        </div>
                        <div class="quantity-price"><?php echo number_format($gia['gia_nguoi_lon'], 0, ',', '.'); ?> đ</div>
                        <div class="quantity-controls">
                            <button type="button" onclick="changeQuantity('adults', -1)">−</button>
                            <span id="adults-count">1</span>
                            <button type="button" onclick="changeQuantity('adults', 1)">+</button>
                        </div>
                        <input type="hidden" name="so_nguoi_lon" id="adults-input" value="1">
                    </div>

                    <div class="quantity-item">
                        <div class="quantity-label">
                            <strong>Trẻ em</strong>
                            <small>(2 - 9 tuổi)</small>
                        </div>
                        <div class="quantity-price"><?php echo number_format($gia['gia_tre_em'], 0, ',', '.'); ?> đ</div>
                        <div class="quantity-controls">
                            <button type="button" onclick="changeQuantity('children', -1)">−</button>
                            <span id="children-count">0</span>
                            <button type="button" onclick="changeQuantity('children', 1)">+</button>
                        </div>
                        <input type="hidden" name="so_tre_em" id="children-input" value="0">
                    </div>

                    <div class="quantity-item">
                        <div class="quantity-label">
                            <strong>Trẻ nhỏ</strong>
                            <small>(Dưới 2 tuổi)</small>
                        </div>
                        <div class="quantity-price"><?php echo number_format($gia['gia_tre_nho'], 0, ',', '.'); ?> đ</div>
                        <div class="quantity-controls">
                            <button type="button" onclick="changeQuantity('infants', -1)">−</button>
                            <span id="infants-count">0</span>
                            <button type="button" onclick="changeQuantity('infants', 1)">+</button>
                        </div>
                        <input type="hidden" name="so_tre_nho" id="infants-input" value="0">
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="form-section">
                <h3>💬 Ghi Chú</h3>
                <div class="form-group">
                    <textarea name="ghi_chu" placeholder="Yêu cầu đặc biệt hoặc câu hỏi của bạn..."></textarea>
                </div>
            </div>

            <?php if($gia): ?>
            <div class="price-summary">
                <div class="price-row">
                    <span>Người lớn (<span id="adults-display">1</span> × <?php echo number_format($gia['gia_nguoi_lon'], 0, ',', '.'); ?> đ)</span>
                    <span id="adults-total"><?php echo number_format($gia['gia_nguoi_lon'], 0, ',', '.'); ?> đ</span>
                </div>
                <div class="price-row">
                    <span>Trẻ em (<span id="children-display">0</span> × <?php echo number_format($gia['gia_tre_em'], 0, ',', '.'); ?> đ)</span>
                    <span id="children-total">0 đ</span>
                </div>
                <div class="price-row">
                    <span>Trẻ nhỏ (<span id="infants-display">0</span> × <?php echo number_format($gia['gia_tre_nho'], 0, ',', '.'); ?> đ)</span>
                    <span id="infants-total">0 đ</span>
                </div>
                <div class="price-row total">
                    <span>Tổng thanh toán</span>
                    <span id="grand-total"><?php echo number_format($gia['gia_nguoi_lon'], 0, ',', '.'); ?> đ</span>
                </div>
            </div>

            <input type="hidden" name="tong_tien" id="total-input" value="<?php echo $gia['gia_nguoi_lon']; ?>">

            <button type="submit" class="submit-btn">Xác Nhận Đặt Tour</button>
            <a href="tour.php" class="back-link">← Quay lại</a>
            <?php endif; ?>
        </form>
        <?php 
            } else {
                echo '<div class="booking-form"><p style="text-align: center; color: #ff4757; font-size: 1.2em;">❌ Không tìm thấy tour. Vui lòng quay lại trang trước.</p></div>';
            }
        } else { 
        ?>
            <div class="booking-form">
                <p style="text-align: center; color: #ff4757; font-size: 1.2em;">❌ Không tìm thấy tour. Vui lòng quay lại trang trước.</p>
            </div>
        <?php } ?>
    </div>

    <script>
        const prices = {
            adults: <?php echo isset($gia) ? $gia['gia_nguoi_lon'] : 0; ?>,
            children: <?php echo isset($gia) ? $gia['gia_tre_em'] : 0; ?>,
            infants: <?php echo isset($gia) ? $gia['gia_tre_nho'] : 0; ?>
        };

        let quantities = {
            adults: 1,
            children: 0,
            infants: 0
        };

        function changeQuantity(type, change) {
            quantities[type] = Math.max(0, quantities[type] + change);
            
            if(type === 'adults' && quantities[type] === 0) {
                quantities[type] = 1;
            }
            
            updateDisplay();
        }

        function updateDisplay() {
            document.getElementById('adults-count').textContent = quantities.adults;
            document.getElementById('children-count').textContent = quantities.children;
            document.getElementById('infants-count').textContent = quantities.infants;
            
            document.getElementById('adults-input').value = quantities.adults;
            document.getElementById('children-input').value = quantities.children;
            document.getElementById('infants-input').value = quantities.infants;
            
            const adultsTotal = quantities.adults * prices.adults;
            const childrenTotal = quantities.children * prices.children;
            const infantsTotal = quantities.infants * prices.infants;
            const grandTotal = adultsTotal + childrenTotal + infantsTotal;
            
            document.getElementById('adults-display').textContent = quantities.adults;
            document.getElementById('children-display').textContent = quantities.children;
            document.getElementById('infants-display').textContent = quantities.infants;
            
            document.getElementById('adults-total').textContent = formatNumber(adultsTotal) + ' đ';
            document.getElementById('children-total').textContent = formatNumber(childrenTotal) + ' đ';
            document.getElementById('infants-total').textContent = formatNumber(infantsTotal) + ' đ';
            document.getElementById('grand-total').textContent = formatNumber(grandTotal) + ' đ';
            
            document.getElementById('total-input').value = grandTotal;
        }

        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            if(quantities.adults === 0) {
                e.preventDefault();
                alert('⚠️ Vui lòng chọn ít nhất 1 người lớn!');
            }
        });
    </script>
</body>
</html>