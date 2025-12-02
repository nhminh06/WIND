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
    <link rel="stylesheet" href="../../../css/Main5.css">
    <link rel="stylesheet" href="../../../css/Main5_1.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
       body{
        background: url('https://i.pinimg.com/1200x/fd/3b/2c/fd3b2cf68aa974efb126722462d76506.jpg') no-repeat center center fixed;
  background-size: cover;
       }
       .menusearch{
        background-color: #00000020;
       }
       
    </style>
</head>
<body><?php include '../../../includes/header.php'; ?>
    <div style=" margin-top: 85px;" class="booking-container box fade-up">
        <div class="booking-header">
            
            <h1><i class="bi bi-ticket-perforated"></i> THÔNG TIN LIÊN HỆ</h1>
            <p>Xin chào: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
            
        </div>

        <?php
        if(isset($_GET['id'])) {
            include '../../../db/db.php';
            $matour = intval($_GET['id']);
            
            $sql_tour = "SELECT t.*, tc.ma_tour, tc.diem_khoi_hanh 
                        FROM tour t 
                        LEFT JOIN tour_chi_tiet tc ON t.id = tc.tour_id 
                        WHERE t.id = $matour";
            $result_tour = mysqli_query($conn, $sql_tour);
            
            if(mysqli_num_rows($result_tour) > 0) {
                $tour = mysqli_fetch_assoc($result_tour);
                
                $sql_gia = "SELECT * FROM lich_khoi_hanh WHERE tour_id = $matour LIMIT 1";
                $result_gia = mysqli_query($conn, $sql_gia);
                $gia = mysqli_fetch_assoc($result_gia);
        ?>

        <div class="booking-content">
            
            <!-- Sidebar Tour Info -->
            <div class="tour-sidebar">
                <img class="tour-image" src="../../../uploads/<?php echo htmlspecialchars($tour['hinh_anh']); ?>" alt="Tour">
                
                <div class="tour-info">
                    <h2><?php echo htmlspecialchars($tour['ten_tour']); ?></h2>
                    
                    <div class="tour-detail-item">
                        <strong>Mã tour:</strong>
                        <span><?php echo htmlspecialchars($tour['ma_tour'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="tour-detail-item">
                        <strong>Thời gian:</strong>
                        <span><?php echo htmlspecialchars($tour['so_ngay']); ?> ngày</span>
                    </div>
                    <div class="tour-detail-item">
                        <strong>Giá:</strong>
                        <span><?php echo number_format($tour['gia'], 0, ',', '.'); ?> đ</span>
                    </div>
                    <div class="tour-detail-item">
                        <strong>Ngày khởi hành:</strong>
                        <span><?php echo isset($gia['ngay_khoi_hanh']) ? date('d/m/Y', strtotime($gia['ngay_khoi_hanh'])) : 'Ngày khác'; ?></span>
                    </div>
                    <div class="tour-detail-item">
                        <strong>Nơi khởi hành:</strong>
                        <span><?php echo htmlspecialchars($tour['diem_khoi_hanh'] ?? 'Đang cập nhật'); ?></span>
                    </div>
                  
                </div>

                <?php if($gia): ?>
                <div class="price-table">
                    <h3><i class="bi bi-tag-fill"></i> BẢNG GIÁ TOUR CHI TIẾT</h3>
                    <div class="price-row">
                        <strong>Người lớn (Trên 11 tuổi)</strong>
                        <span><?php echo number_format($gia['gia_nguoi_lon'], 0, ',', '.'); ?> đ</span>
                    </div>
                    <div class="price-row">
                        <strong>Trẻ em (5 - 11 tuổi)</strong>
                        <span><?php echo number_format($gia['gia_tre_em'], 0, ',', '.'); ?> đ</span>
                    </div>
                    <div class="price-row">
                        <strong>Trẻ nhỏ (2 - 5 tuổi)</strong>
                        <span><?php echo number_format($gia['gia_tre_nho'], 0, ',', '.'); ?> đ</span>
                    </div>
                   
                </div>
                <?php endif; ?>
                <a href="tour.php" class="back-link">← Quay lại</a>
            </div>

            <!-- Booking Form -->
            <div class="booking-form">
                <form id="bookingForm" method="POST" action="process_booking.php">
                    <input type="hidden" name="tour_id" value="<?php echo $matour; ?>">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    
                    <?php if(isset($_SESSION['booking_error'])): ?>
                    <div class="message error">
                        <?php 
                        echo $_SESSION['booking_error']; 
                        unset($_SESSION['booking_error']);
                        ?>
                    </div>
                    <?php endif; ?>

                    <div class="form-section">
                        <h3><i class="bi bi-file-earmark-person-fill"></i> HỌ TÊN</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Họ tên <span>*</span></label>
                                <input type="text" name="ho_ten" required placeholder="Nhập họ tên">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="bi bi-telephone-fill"></i> THÔNG TIN LIÊN HỆ</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Email <span>*</span></label>
                                <input type="email" name="email" required placeholder="example@email.com">
                            </div>
                            <div class="form-group">
                                <label>Số điện thoại <span>*</span></label>
                                <input type="tel" name="sdt" required placeholder="0912345678" pattern="[0-9]{10,11}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Địa chỉ</label>
                            <textarea name="dia_chi" placeholder="Nhập địa chỉ của bạn"></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="bi bi-calendar"></i> NGÀY KHỞI HÀNH</h3>
                        <div class="form-group">
                            <input type="date" name="ngay_khoi_hanh" required min="<?php echo date('Y-m-d'); ?>" value="<?php echo isset($gia['ngay_khoi_hanh']) ? $gia['ngay_khoi_hanh'] : ''; ?>">
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="bi bi-people-fill"></i> SỐ LƯỢNG KHÁCH</h3>
                        <div class="form-row quad">
                            <div class="form-group">
                                <label>Người lớn <span>*</span></label>
                                <input type="number" name="so_nguoi_lon" id="adults-input" value="1" min="1" required>
                            </div>
                            <div class="form-group">
                                <label>Trẻ em (5 - 11 tuổi)</label>
                                <input type="number" name="so_tre_em" id="children-input" value="0" min="0">
                            </div>
                            <div class="form-group">
                                <label>Trẻ nhỏ (2 - 5 tuổi)</label>
                                <input type="number" name="so_tre_nho" id="infants-input" value="0" min="0">
                            </div>
                            
                        </div>
                    </div>


                    <div class="form-section">
                        <h3><i class="bi bi-chat-dots"></i> GHI CHÚ</h3>
                        <div class="form-group">
                            <textarea name="ghi_chu" placeholder="Nhập ghi chú của bạn (nếu có)"></textarea>
                        </div>
                    </div>

                    <?php if($gia): ?>
                    <div class="price-summary">
                        <div class="summary-row">
                            <span>Người lớn (<span id="adults-display">1</span> × <?php echo number_format($gia['gia_nguoi_lon'], 0, ',', '.'); ?> đ)</span>
                            <span id="adults-total"><?php echo number_format($gia['gia_nguoi_lon'], 0, ',', '.'); ?> đ</span>
                        </div>
                        <div class="summary-row">
                            <span>Trẻ em (<span id="children-display">0</span> × <?php echo number_format($gia['gia_tre_em'], 0, ',', '.'); ?> đ)</span>
                            <span id="children-total">0 đ</span>
                        </div>
                        <div class="summary-row">
                            <span>Trẻ nhỏ (<span id="infants-display">0</span> × <?php echo number_format($gia['gia_tre_nho'], 0, ',', '.'); ?> đ)</span>
                            <span id="infants-total">0 đ</span>
                        </div>
                        <div class="summary-row">
                            <span>Sơ sinh (<span id="baby-display">0</span> × 500,000 đ)</span>
                            <span id="baby-total">0 đ</span>
                        </div>
                        <div class="summary-row">
                            <span>Phụ thu visa (<span id="visa-display">0</span>)</span>
                            <span id="visa-total">0 đ</span>
                        </div>
                        <div class="summary-row">
                            <span>Phụ thu phòng đơn (<span id="room-display">0</span>)</span>
                            <span id="room-total">0 đ</span>
                        </div>
                        <div class="summary-row total">
                            <span>Tổng giá trị:</span>
                            <span id="grand-total"><?php echo number_format($gia['gia_nguoi_lon'], 0, ',', '.'); ?> đ</span>
                        </div>
                    </div>

                    <input type="hidden" name="tong_tien" id="total-input" value="<?php echo $gia['gia_nguoi_lon']; ?>">

                    <button type="submit" class="submit-btn">Xác Nhận Đặt Tour</button>
                    
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <?php 
            } else {
                echo '<div style="padding: 40px; text-align: center;"><p style="color: #ff4757; font-size: 1.2em;">❌ Không tìm thấy tour. Vui lòng quay lại trang trước.</p></div>';
            }
        } else { 
        ?>
            <div style="padding: 40px; text-align: center;">
                <p style="color: #ff4757; font-size: 1.2em;">❌ Không tìm thấy tour. Vui lòng quay lại trang trước.</p>
            </div>
        <?php } ?>
    </div>
    <?php include '../../../includes/footer.php'; ?>
            <script src="../../../js/Main5.js"></script>
    <script>
        const prices = {
            adults: <?php echo isset($gia) ? $gia['gia_nguoi_lon'] : 0; ?>,
            children: <?php echo isset($gia) ? $gia['gia_tre_em'] : 0; ?>,
            infants: <?php echo isset($gia) ? $gia['gia_tre_nho'] : 0; ?>,
            baby: 500000,
            visa: 590000,
            room: 1600000
        };

        function updateTotal() {
            const adults = parseInt(document.getElementById('adults-input').value) || 0;
            const children = parseInt(document.getElementById('children-input').value) || 0;
            const infants = parseInt(document.getElementById('infants-input').value) || 0;
            const baby = parseInt(document.getElementById('baby-input').value) || 0;
            
            const visaQty = parseInt(document.getElementById('visa-quantity').value) || 0;
            const roomQty = parseInt(document.getElementById('room-quantity').value) || 0;
            
            const adultsTotal = adults * prices.adults;
            const childrenTotal = children * prices.children;
            const infantsTotal = infants * prices.infants;
            const babyTotal = baby * prices.baby;
            const visaTotal = visaQty * prices.visa;
            const roomTotal = roomQty * prices.room;
            
            const grandTotal = adultsTotal + childrenTotal + infantsTotal + babyTotal + visaTotal + roomTotal;
            
            document.getElementById('adults-display').textContent = adults;
            document.getElementById('children-display').textContent = children;
            document.getElementById('infants-display').textContent = infants;
            document.getElementById('baby-display').textContent = baby;
            document.getElementById('visa-display').textContent = visaQty;
            document.getElementById('room-display').textContent = roomQty;
            
            document.getElementById('adults-total').textContent = formatNumber(adultsTotal) + ' đ';
            document.getElementById('children-total').textContent = formatNumber(childrenTotal) + ' đ';
            document.getElementById('infants-total').textContent = formatNumber(infantsTotal) + ' đ';
            document.getElementById('baby-total').textContent = formatNumber(babyTotal) + ' đ';
            document.getElementById('visa-total').textContent = formatNumber(visaTotal) + ' đ';
            document.getElementById('room-total').textContent = formatNumber(roomTotal) + ' đ';
            document.getElementById('grand-total').textContent = formatNumber(grandTotal) + ' đ';
            
            document.getElementById('total-input').value = grandTotal;
        }

        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Event listeners
        document.getElementById('adults-input').addEventListener('input', updateTotal);
        document.getElementById('children-input').addEventListener('input', updateTotal);
        document.getElementById('infants-input').addEventListener('input', updateTotal);
        document.getElementById('baby-input').addEventListener('input', updateTotal);
        document.getElementById('visa-quantity').addEventListener('input', updateTotal);
        document.getElementById('room-quantity').addEventListener('input', updateTotal);
        document.getElementById('visa-select').addEventListener('change', function() {
            if(this.value === '0') {
                document.getElementById('visa-quantity').value = 0;
            }
            updateTotal();
        });
        document.getElementById('room-select').addEventListener('change', function() {
            if(this.value === '0') {
                document.getElementById('room-quantity').value = 0;
            }
            updateTotal();
        });

        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            const adults = parseInt(document.getElementById('adults-input').value) || 0;
            if(adults === 0) {
                e.preventDefault();
                alert('⚠️ Vui lòng chọn ít nhất 1 người lớn!');
            }
        });
    </script>
</body>
</html>