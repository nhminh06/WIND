<?php 
session_start();
include '../../../db/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$tour_id = isset($_GET['tour_id']) ? intval($_GET['tour_id']) : 0;
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

if ($tour_id <= 0 || $booking_id <= 0) {
    header('Location: my-bookings.php');
    exit();
}

// Kiểm tra booking có tồn tại và thuộc về user không
$check_query = "SELECT d.*, t.ten_tour, t.hinh_anh 
                FROM dat_tour d
                JOIN tour t ON d.tour_id = t.id
                WHERE d.id = ? AND d.user_id = ? AND d.tour_id = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("iii", $booking_id, $user_id, $tour_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: my-bookings.php');
    exit();
}

$booking = $result->fetch_assoc();
$stmt->close();

// Kiểm tra tour đã hoàn thành chưa
if ($booking['trang_thai'] != 'confirmed' || $booking['trang_thai_chuyen_di'] != 'completed') {
    $_SESSION['error'] = 'Bạn chỉ có thể đánh giá sau khi chuyến đi hoàn thành!';
    header('Location: booking-detail.php?id=' . $booking_id);
    exit();
}

// Kiểm tra đã đánh giá chưa
$check_review = "SELECT id FROM danh_gia WHERE tour_id = ? AND ten_khach_hang = ?";
$stmt = $conn->prepare($check_review);
$stmt->bind_param("is", $tour_id, $booking['ho_ten']);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $_SESSION['error'] = 'Bạn đã đánh giá tour này rồi!';
    header('Location: booking-detail.php?id=' . $booking_id);
    exit();
}
$stmt->close();

// Xử lý submit đánh giá
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $diem = floatval($_POST['rating']);
    $nhan_xet = trim($_POST['review_content']);
    $hinh_anh = null;
    
    // Validate
    if ($diem < 1 || $diem > 5) {
        $error = "Vui lòng chọn số sao từ 1 đến 5!";
    } elseif (empty($nhan_xet)) {
        $error = "Vui lòng nhập nội dung đánh giá!";
    } else {
        // Xử lý upload ảnh (nếu có)
        if (isset($_FILES['review_image']) && $_FILES['review_image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['review_image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = 'review_' . time() . '_' . uniqid() . '.' . $ext;
                $upload_path = '../../../uploads/' . $new_filename;
                
                if (move_uploaded_file($_FILES['review_image']['tmp_name'], $upload_path)) {
                    $hinh_anh = $new_filename;
                }
            }
        }
        
        // Lưu đánh giá vào database
        $insert_query = "INSERT INTO danh_gia (tour_id, ten_khach_hang, diem, nhan_xet, hinh_anh) 
                         VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("isdss", $tour_id, $booking['ho_ten'], $diem, $nhan_xet, $hinh_anh);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Cảm ơn bạn đã đánh giá! Đánh giá của bạn đã được ghi nhận.';
            header('Location: booking-detail.php?id=' . $booking_id);
            exit();
        } else {
            $error = "Có lỗi xảy ra. Vui lòng thử lại!";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đánh giá tour - <?php echo htmlspecialchars($booking['ten_tour']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('https://i.pinimg.com/1200x/4a/4b/6b/4a4b6b057be3eee3beb0d0f72b6a075f.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .review-card {
            background: white;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease;
            width: 550px;
            margin: auto;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        
        .card-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .tour-info {
            background: rgba(255,255,255,0.2);
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .tour-info img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            border: 3px solid white;
        }
        
        .tour-info-text h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .tour-info-text p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .card-body {
            padding: 40px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-error {
            background: #fee;
            color: #c33;
            border-left: 4px solid #c33;
        }
        
        .form-group {
            margin-bottom: 30px;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
            font-size: 16px;
        }
        
        .rating-container {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
        }
        
        .stars {
            font-size: 50px;
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .star {
            cursor: pointer;
            color: #ddd;
            transition: all 0.3s ease;
        }
        
        .star:hover,
        .star.active {
            color: #ffc107;
            transform: scale(1.2);
        }
        
        .rating-text {
            font-size: 18px;
            color: #666;
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s ease;
            resize: vertical;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #f5576c;
            box-shadow: 0 0 0 3px rgba(245, 87, 108, 0.1);
        }
        
        .char-count {
            text-align: right;
            font-size: 13px;
            color: #999;
            margin-top: 5px;
        }
        
        .image-upload {
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .image-upload:hover {
            border-color: #f5576c;
            background: #fff5f7;
        }
        
        .image-upload input[type="file"] {
            display: none;
        }
        
        .upload-icon {
            font-size: 50px;
            color: #ddd;
            margin-bottom: 10px;
        }
        
        .image-preview {
            margin-top: 15px;
            display: none;
        }
        
        .image-preview img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .remove-image {
            margin-top: 10px;
            padding: 8px 20px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            flex: 1;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(245, 87, 108, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .card-body {
                padding: 20px;
            }
            
            .stars {
                font-size: 40px;
            }
            
            .btn-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="review-card">
            <div class="card-header">
                <h1>
                    <i class="fas fa-star"></i>
                    Đánh giá chuyến đi
                </h1>
                <div class="tour-info">
                    <img src="<?php echo htmlspecialchars('../../../uploads/' . $booking['hinh_anh']); ?>" 
                         alt="<?php echo htmlspecialchars($booking['ten_tour']); ?>">
                    <div class="tour-info-text">
                        <h3><?php echo htmlspecialchars($booking['ten_tour']); ?></h3>
                        <p><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($booking['ngay_khoi_hanh'])); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="../../../php/UsersController/process-review.php" id="reviewForm">
    <!-- Hidden fields để truyền dữ liệu -->
    <input type="hidden" name="tour_id" value="<?php echo $tour_id; ?>">
    <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
    
    <!-- Rating -->
    <div class="form-group">
        <label class="form-label">
            <i class="fas fa-star"></i> Đánh giá của bạn
        </label>
        <div class="rating-container">
            <div class="stars" id="starRating">
                <i class="fas fa-star star" data-value="1"></i>
                <i class="fas fa-star star" data-value="2"></i>
                <i class="fas fa-star star" data-value="3"></i>
                <i class="fas fa-star star" data-value="4"></i>
                <i class="fas fa-star star" data-value="5"></i>
            </div>
            <div class="rating-text" id="ratingText">Chọn số sao để đánh giá</div>
            <input type="hidden" name="rating" id="ratingValue" value="0" required>
        </div>
    </div>
    
    <!-- Review Content -->
    <div class="form-group">
        <label class="form-label">
            <i class="fas fa-comment-dots"></i> Nhận xét của bạn
        </label>
        <textarea name="review_content" 
                  class="form-control" 
                  rows="8" 
                  placeholder="Chia sẻ trải nghiệm của bạn về chuyến đi này...&#10;&#10;Ví dụ: Tour rất tuyệt vời, hướng dẫn viên nhiệt tình, địa điểm đẹp..."
                  maxlength="1000"
                  id="reviewContent"
                  required></textarea>
                  <input type="hidden" name="idnguoidung" value="<?php echo $_SESSION['user_id']; ?>">
        <div class="char-count">
            <span id="charCount">0</span>/1000 ký tự
        </div>
    </div>
    
    <!-- Buttons -->
    <div class="btn-group">
        <button type="button" class="btn btn-secondary" onclick="window.history.back()">
            <i class="fas fa-arrow-left"></i>
            Quay lại
        </button>
        <button type="submit" name="submit_review" class="btn btn-primary">
            <i class="fas fa-paper-plane"></i>
            Gửi đánh giá
        </button>
    </div>
</form>

            </div>
        </div>
    </div>
    
    <script>
       const stars = document.querySelectorAll('.star');
const ratingValue = document.getElementById('ratingValue');
const ratingText = document.getElementById('ratingText');

const ratingLabels = {
    1: 'Rất tệ',
    2: 'Tệ',
    3: 'Trung bình',
    4: 'Tốt',
    5: 'Xuất sắc'
};

stars.forEach(star => {
    star.addEventListener('click', function() {
        const value = parseInt(this.getAttribute('data-value'));
        ratingValue.value = value;
        ratingText.textContent = ratingLabels[value];
        
        stars.forEach(s => {
            const sValue = parseInt(s.getAttribute('data-value'));
            if (sValue <= value) {
                s.classList.add('active');
            } else {
                s.classList.remove('active');
            }
        });
    });
    
    star.addEventListener('mouseenter', function() {
        const value = parseInt(this.getAttribute('data-value'));
        stars.forEach(s => {
            const sValue = parseInt(s.getAttribute('data-value'));
            if (sValue <= value) {
                s.style.color = '#ffc107';
            }
        });
    });
});

document.getElementById('starRating').addEventListener('mouseleave', function() {
    const currentValue = parseInt(ratingValue.value);
    stars.forEach(s => {
        const sValue = parseInt(s.getAttribute('data-value'));
        if (sValue > currentValue) {
            s.style.color = '#ddd';
        }
    });
});

// Character count
const reviewContent = document.getElementById('reviewContent');
const charCount = document.getElementById('charCount');

reviewContent.addEventListener('input', function() {
    charCount.textContent = this.value.length;
});

// Form validation
document.getElementById('reviewForm').addEventListener('submit', function(e) {
    const rating = parseInt(ratingValue.value);
    const content = reviewContent.value.trim();
    
    if (rating === 0) {
        e.preventDefault();
        alert('Vui lòng chọn số sao để đánh giá!');
        return false;
    }
    
    if (content === '') {
        e.preventDefault();
        alert('Vui lòng nhập nội dung đánh giá!');
        return false;
    }
    
    if (content.length < 10) {
        e.preventDefault();
        alert('Nội dung đánh giá phải có ít nhất 10 ký tự!');
        return false;
    }
});
    </script>
</body>
</html>
<?php
$conn->close();
?>