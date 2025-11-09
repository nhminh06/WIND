<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
  <?php session_start(); ?>
      <aside class="sidebar">
    <h2 class="logo">WIND Admin</h2>
   <?php include '../../includes/Adminnav.php';?>
  </aside>

  <!-- Main -->
  <div class="main">
    <!-- Header -->
    <header class="header">
      <h1>Bảng điều khiển</h1>
      <div class="admin-info">
       <?php 
       echo "<p>Xin chào  " . $_SESSION['username'] . "</p>";
       ?>
        <button onclick="window.location.href='../views/index/webindex.php'" class="logout">Trở lại</button>
      </div>
    </header>

    <!-- Content -->
    <!-- Content -->
<section class="content">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
          
            <div class="stat-info">
                <h3>24</h3>
                <p>Thông báo mới</p>
            </div>
        </div>

        <div class="stat-card stat-warning">
           
            <div class="stat-info">
                <h3>12</h3>
                <p>Khiếu nại</p>
            </div>
        </div>

        <div class="stat-card stat-success">
            
            <div class="stat-info">
                <h3>47</h3>
                <p>Góp ý</p>
            </div>
        </div>

        <div class="stat-card stat-info">
           
            <div class="stat-info">
                <h3>6</h3>
                <p>Vấn đề chưa giải quyết</p>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="action-bar">
        <div class="filter-group">
            <button class="filter-btn active">
                <i class="bi bi-grid"></i> Tất cả
            </button>
            <button class="filter-btn">
                <i class="bi bi-exclamation-triangle"></i> Khiếu nại
            </button>
            <button class="filter-btn">
                <i class="bi bi-chat-left-text"></i> Góp ý
            </button>
            <button class="filter-btn">
                <i class="bi bi-star"></i> Đánh giá
            </button>
        </div>
        <div class="search-group">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Tìm kiếm theo tên hoặc email..." class="search-input">
        </div>
    </div>

    <!-- Khiếu nại List -->
    <div class="content-section">
        <div class="section-header">
            <h3><i class="bi bi-exclamation-triangle-fill"></i> Khiếu nại cần xử lý</h3>
            <span class="count-badge">12 mới</span>
        </div>

        <div class="list-container">
            <div class="list-item priority-high">
                <div class="item-status">
                    <span class="status-dot urgent"></span>
                </div>
                <div class="item-content">
                    <div class="item-header">
                        <div class="user-details">
                            <h4>Nguyễn Văn A</h4>
                            <span class="email">nguyenvana@email.com</span>
                        </div>
                        <div class="item-meta">
                    
                            <span class="time">5 phút trước</span>
                        </div>
                    </div>
                    <div class="item-body">
                        <p class="message">Hướng dẫn viên không xuất hiện đúng giờ, đoàn đang chờ tại điểm hẹn từ 30 phút trước. Yêu cầu hỗ trợ khẩn cấp và bồi thường chi phí chờ đợi.</p>
                    </div>
                    <div class="item-footer">
                        <button class="btn btn-primary"><i class="bi bi-check-circle"></i> Chưa xử lý</button>
                        <button class="btn btn-secondary"><i class="bi bi-reply"></i> Trả lời</button>
                        <button class="btn btn-outline"><i class="bi bi-archive"></i> Lưu trữ</button>
                    </div>
                </div>
            </div>

            <div class="list-item priority-medium">
                <div class="item-status">
                    <span class="status-dot warning"></span>
                </div>
                <div class="item-content">
                    <div class="item-header">
                        <div class="user-details">
                            <h4>Trần Thị B</h4>
                            <span class="email">tranthib@email.com</span>
                        </div>
                        <div class="item-meta">
                       
                            <span class="time">2 giờ trước</span>
                        </div>
                    </div>
                    <div class="item-body">
                        <p class="message">Khách sạn không đúng như hình ảnh quảng cáo. Phòng nhỏ hơn, view không đẹp như mô tả. Yêu cầu đổi phòng hoặc hoàn lại một phần chi phí.</p>
                    </div>
                    <div class="item-footer">
                        <button class="btn btn-primary"><i class="bi bi-check-circle"></i> Chưa xử lý</button>
                        <button class="btn btn-secondary"><i class="bi bi-reply"></i> Trả lời</button>
                        <button class="btn btn-outline"><i class="bi bi-archive"></i> Lưu trữ</button>
                    </div>
                </div>
            </div>

            <div class="list-item priority-low">
                <div class="item-status">
                    <span class="status-dot info"></span>
                </div>
                <div class="item-content">
                    <div class="item-header">
                        <div class="user-details">
                            <h4>Lê Văn C</h4>
                            <span class="email">levanc@email.com</span>
                        </div>
                        <div class="item-meta">
                    
                            <span class="time">5 giờ trước</span>
                        </div>
                    </div>
                    <div class="item-body">
                        <p class="message">Thời gian tham quan hơi vội vàng, không có đủ thời gian chụp ảnh. Mong các tour sau cải thiện lịch trình hợp lý hơn.</p>
                    </div>
                    <div class="item-footer">
                        <button class="btn btn-primary1"><i class="bi bi-check-circle"></i> Đã xử lý</button>
                        <button class="btn btn-secondary"><i class="bi bi-reply"></i> Trả lời</button>
                        <button class="btn btn-outline"><i class="bi bi-archive"></i> Lưu trữ</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Góp ý List -->
    <div class="content-section">
        <div class="section-header">
            <h3><i class="bi bi-chat-left-text-fill"></i> Góp ý từ khách hàng</h3>
            <span class="count-badge">47</span>
        </div>

        <div class="list-container">
            <div class="list-item">
                <div class="item-status">
                    <span class="status-dot success"></span>
                </div>
                <div class="item-content">
                    <div class="item-header">
                        <div class="user-details">
                            <h4>Phạm Thị D</h4>
                            <span class="email">phamthid@email.com</span>
                        </div>
                        <div class="item-meta">
                            <span class="rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-half"></i>
                            </span>
                            <span class="time">1 ngày trước</span>
                        </div>
                    </div>
                    <div class="item-body">
                        <p class="message">Chuyến đi rất tuyệt vời! Hướng dẫn viên nhiệt tình, khách sạn sạch sẽ. Tuy nhiên nên có thêm thời gian chụp ảnh ở Cầu Vàng vì nơi đó rất đẹp.</p>
                    </div>
                    <div class="item-footer">
                        <button class="btn btn-secondary"><i class="bi bi-reply"></i> Trả lời</button>
                        <button class="btn btn-outline"><i class="bi bi-heart"></i> Cảm ơn</button>
                    </div>
                </div>
            </div>

            <div class="list-item">
                <div class="item-status">
                    <span class="status-dot success"></span>
                </div>
                <div class="item-content">
                    <div class="item-header">
                        <div class="user-details">
                            <h4>Hoàng Văn E</h4>
                            <span class="email">hoangvane@email.com</span>
                        </div>
                        <div class="item-meta">
                            <span class="rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </span>
                            <span class="time">2 ngày trước</span>
                        </div>
                    </div>
                    <div class="item-body">
                        <p class="message">Quá tuyệt vời! Phố cổ Hội An lung linh với đèn lồng, hướng dẫn viên rất am hiểu lịch sử. Sẽ giới thiệu cho bạn bè và quay lại trong tương lai!</p>
                    </div>
                    <div class="item-footer">
                        <button class="btn btn-secondary"><i class="bi bi-reply"></i> Trả lời</button>
                        <button class="btn btn-outline"><i class="bi bi-heart"></i> Cảm ơn</button>
                    </div>
                </div>
            </div>

            <div class="list-item">
                <div class="item-status">
                    <span class="status-dot warning"></span>
                </div>
                <div class="item-content">
                    <div class="item-header">
                        <div class="user-details">
                            <h4>Đỗ Thị F</h4>
                            <span class="email">dothif@email.com</span>
                        </div>
                        <div class="item-meta">
                            <span class="rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star"></i>
                                <i class="bi bi-star"></i>
                            </span>
                            <span class="time">3 ngày trước</span>
                        </div>
                    </div>
                    <div class="item-body">
                        <p class="message">Các điểm tham quan rất đẹp, nhưng lịch trình hơi gấp gáp. Nên tăng thêm 1 ngày để khám phá kỹ hơn các di tích lịch sử.</p>
                    </div>
                    <div class="item-footer">
                        <button class="btn btn-secondary"><i class="bi bi-reply"></i> Trả lời</button>
                        <button class="btn btn-outline"><i class="bi bi-heart"></i> Cảm ơn</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
  </div>
</body>
</html>