<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    // Lưu trang hiện tại để redirect sau khi đăng nhập
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }

        .booking-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .booking-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .booking-header h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }

        .user-info-box {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-info-box p {
            margin: 0;
            font-size: 0.95em;
        }

        .tour-info {
            background: #f8f9fa;
            padding: 20px 30px;
            border-bottom: 3px solid #667eea;
        }

        .tour-info h2 {
            color: #333;
            margin-bottom: 15px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #555;
        }

        .info-item svg {
            width: 20px;
            height: 20px;
            color: #667eea;
        }

        .booking-form {
            padding: 30px;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section h3 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }

        .form-group label span {
            color: #ff4757;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .quantity-selector {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
        }

        .quantity-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
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
        }

        .quantity-label small {
            color: #666;
        }

        .quantity-price {
            color: #667eea;
            font-weight: bold;
            margin: 0 20px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-controls button {
            width: 35px;
            height: 35px;
            border: 2px solid #667eea;
            background: white;
            color: #667eea;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .quantity-controls button:hover {
            background: #667eea;
            color: white;
        }

        .quantity-controls span {
            min-width: 30px;
            text-align: center;
            font-weight: bold;
            color: #333;
        }

        .price-summary {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            color: #555;
        }

        .price-row.total {
            border-top: 2px solid #667eea;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 1.3em;
            color: #333;
            font-weight: bold;
        }

        .price-row.total span {
            color: #667eea;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 18px;
            border-radius: 10px;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .message.show {
            display: block;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .booking-header h1 {
                font-size: 1.5em;
            }
        }
    </style>
</head>
<body>
    <div class="booking-container">
        <div class="booking-header">
            <h1>🎫 Đặt Tour Du Lịch</h1>
            <p>Điền thông tin để đặt tour</p>
            <div class="user-info-box">
                <p>👤 Đăng nhập với: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
            </div>
        </div>

        <?php
        // Lấy thông tin tour từ database
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
            <h2><?php echo htmlspecialchars($tour['ten_tour']); ?></h2>
            <div class="info-grid">
                <div class="info-item">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?php echo htmlspecialchars($tour['so_ngay']); ?></span>
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

        <form class="booking-form" id="bookingForm" method="POST" action="process_booking.php">
            <input type="hidden" name="tour_id" value="<?php echo $matour; ?>">
            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
            
            <!-- Hiển thị lỗi nếu có -->
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
                </div>
                <div class="form-row">
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
                            <small>(> 9 tuổi)</small>
                        </div>
                        <div class="quantity-price"><?php echo number_format($gia['gia_nguoi_lon'], 0, ',', '.'); ?> VNĐ</div>
                        <div class="quantity-controls">
                            <button type="button" onclick="changeQuantity('adults', -1)">-</button>
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
                        <div class="quantity-price"><?php echo number_format($gia['gia_tre_em'], 0, ',', '.'); ?> VNĐ</div>
                        <div class="quantity-controls">
                            <button type="button" onclick="changeQuantity('children', -1)">-</button>
                            <span id="children-count">0</span>
                            <button type="button" onclick="changeQuantity('children', 1)">+</button>
                        </div>
                        <input type="hidden" name="so_tre_em" id="children-input" value="0">
                    </div>

                    <div class="quantity-item">
                        <div class="quantity-label">
                            <strong>Trẻ nhỏ</strong>
                            <small>(< 2 tuổi)</small>
                        </div>
                        <div class="quantity-price"><?php echo number_format($gia['gia_tre_nho'], 0, ',', '.'); ?> VNĐ</div>
                        <div class="quantity-controls">
                            <button type="button" onclick="changeQuantity('infants', -1)">-</button>
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
                    <textarea name="ghi_chu" placeholder="Yêu cầu đặc biệt hoặc câu hỏi..."></textarea>
                </div>
            </div>

            <?php if($gia): ?>
            <div class="price-summary">
                <div class="price-row">
                    <span>Người lớn (<span id="adults-display">1</span> x <?php echo number_format($gia['gia_nguoi_lon'], 0, ',', '.'); ?> VNĐ)</span>
                    <span id="adults-total"><?php echo number_format($gia['gia_nguoi_lon'], 0, ',', '.'); ?> VNĐ</span>
                </div>
                <div class="price-row">
                    <span>Trẻ em (<span id="children-display">0</span> x <?php echo number_format($gia['gia_tre_em'], 0, ',', '.'); ?> VNĐ)</span>
                    <span id="children-total">0 VNĐ</span>
                </div>
                <div class="price-row">
                    <span>Trẻ nhỏ (<span id="infants-display">0</span> x <?php echo number_format($gia['gia_tre_nho'], 0, ',', '.'); ?> VNĐ)</span>
                    <span id="infants-total">0 VNĐ</span>
                </div>
                <div class="price-row total">
                    <span>Tổng cộng:</span>
                    <span id="grand-total"><?php echo number_format($gia['gia_nguoi_lon'], 0, ',', '.'); ?> VNĐ</span>
                </div>
            </div>

            <input type="hidden" name="tong_tien" id="total-input" value="<?php echo $gia['gia_nguoi_lon']; ?>">

            <button type="submit" class="submit-btn">Xác Nhận Đặt Tour</button>
            <?php endif; ?>
        </form>

        <?php 
            } else {
                echo '<div class="booking-form"><p style="text-align: center; color: #ff4757;">Không tìm thấy tour. Vui lòng quay lại trang trước.</p></div>';
            }
        } else { 
        ?>
            <div class="booking-form">
                <p style="text-align: center; color: #ff4757;">Không tìm thấy tour. Vui lòng quay lại trang trước.</p>
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
            
            document.getElementById('adults-total').textContent = formatNumber(adultsTotal) + ' VNĐ';
            document.getElementById('children-total').textContent = formatNumber(childrenTotal) + ' VNĐ';
            document.getElementById('infants-total').textContent = formatNumber(infantsTotal) + ' VNĐ';
            document.getElementById('grand-total').textContent = formatNumber(grandTotal) + ' VNĐ';
            
            document.getElementById('total-input').value = grandTotal;
        }

        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            if(quantities.adults === 0) {
                e.preventDefault();
                alert('Vui lòng chọn ít nhất 1 người lớn!');
            }
        });
    </script>
</body>
</html>