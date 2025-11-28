<?php
session_start();
include '../../db/db.php';

// Phân trang cho Khiếu nại
$records_per_page_khieunai = 3;
$current_page_khieunai = isset($_GET['page_kn']) ? max(1, (int)$_GET['page_kn']) : 1;
$offset_khieunai = ($current_page_khieunai - 1) * $records_per_page_khieunai;

// Phân trang cho Góp ý
$records_per_page_gopy = 3;
$current_page_gopy = isset($_GET['page_gy']) ? max(1, (int)$_GET['page_gy']) : 1;
$offset_gopy = ($current_page_gopy - 1) * $records_per_page_gopy;

// Lấy tổng số khiếu nại
$sql_count_khieunai = "SELECT COUNT(*) as total FROM khieu_nai";
$result_count_khieunai = mysqli_query($conn, $sql_count_khieunai);
$total_khieunai = mysqli_fetch_assoc($result_count_khieunai)['total'];
$total_pages_khieunai = ceil($total_khieunai / $records_per_page_khieunai);

// Lấy tổng số góp ý
$sql_count_gopy = "SELECT COUNT(*) as total FROM gop_y";
$result_count_gopy = mysqli_query($conn, $sql_count_gopy);
$total_gopy = mysqli_fetch_assoc($result_count_gopy)['total'];
$total_pages_gopy = ceil($total_gopy / $records_per_page_gopy);

// Lấy số góp ý chưa xử lý
$sql_count_goy_y_chua_xuly = "SELECT COUNT(*) as total FROM gop_y WHERE trang_thai = 0";
$result_count_goy_y_chua_xuly = mysqli_query($conn, $sql_count_goy_y_chua_xuly);
$gopy_chua_xuly = mysqli_fetch_assoc($result_count_goy_y_chua_xuly)['total'];

// Lấy số khiếu nại chưa xử lý
$sql_count_chua_xuly = "SELECT COUNT(*) as total FROM khieu_nai WHERE trang_thai = 0";
$result_count_chua_xuly = mysqli_query($conn, $sql_count_chua_xuly);
$khieunai_chua_xuly = mysqli_fetch_assoc($result_count_chua_xuly)['total'];

// Tổng số chưa xử lý (góp ý + khiếu nại)
$chua_xuly = $gopy_chua_xuly + $khieunai_chua_xuly;

// Lấy danh sách khiếu nại với phân trang
$sql_khieunai = "SELECT * FROM khieu_nai ORDER BY created_at DESC LIMIT $records_per_page_khieunai OFFSET $offset_khieunai";
$result_khieunai = mysqli_query($conn, $sql_khieunai);

// Lấy danh sách góp ý với phân trang
$sql_gopy = "SELECT * FROM gop_y ORDER BY created_at DESC LIMIT $records_per_page_gopy OFFSET $offset_gopy";
$result_gopy = mysqli_query($conn, $sql_gopy);

// Hàm tính thời gian đã qua
function time_elapsed_string($datetime) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->d > 0) {
        return $diff->d . ' ngày trước';
    } elseif ($diff->h > 0) {
        return $diff->h . ' giờ trước';
    } elseif ($diff->i > 0) {
        return $diff->i . ' phút trước';
    } else {
        return 'Vừa xong';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Quản lý Liên hệ</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<style>
    .filter-btn > i{
        color: #15325f;
    }
    .phone{
        color: gray;
        font-size: 12px;
    }
    
    /* Empty State Styles */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 8px;
        margin: 2rem 0;
    }

    .empty-state i {
        font-size: 5rem;
        color: #e0e0e0;
        margin-bottom: 1.5rem;
        display: block;
    }

    .empty-state h4 {
        color: #666;
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }

    .empty-state p {
        color: #999;
        font-size: 0.95rem;
        line-height: 1.6;
        max-width: 500px;
        margin: 0 auto;
    }

    .empty-state-success {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    }

    .empty-state-success i {
        color: #86efac;
    }

    .empty-state-success h4 {
        color: #16a34a;
    }

    .empty-state-success p {
        color: #15803d;
    }

    /* Pagination Styles */

</style>
<body>
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
    <section class="content">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-info">
                <h3><?php echo $total_khieunai + $total_gopy; ?></h3>
                <p>Tổng liên hệ</p>
            </div>
        </div>

        <div class="stat-card stat-warning">
            <div class="stat-info">
                <h3><?php echo $total_khieunai; ?></h3>
                <p>Khiếu nại</p>
            </div>
        </div>

        <div class="stat-card stat-success">
            <div class="stat-info">
                <h3><?php echo $total_gopy; ?></h3>
                <p>Góp ý</p>
            </div>
        </div>

        <div class="stat-card stat-info">
            <div class="stat-info">
                <h3><?php echo $chua_xuly; ?></h3>
                <p>Chưa xử lý</p>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="action-bar">
        <div class="filter-group">
            <button class="filter-btn active" onclick="filterAll()">
                <i class="bi bi-grid"></i> Tất cả
            </button>
            <button class="filter-btn" onclick="filterKhieuNai()">
                <i class="bi bi-exclamation-triangle"></i> Khiếu nại
            </button>
            <button class="filter-btn" onclick="filterGopY()">
                <i class="bi bi-chat-left-text"></i> Góp ý
            </button>
            <button class="filter-btn-box" onclick="window.location.href='storage.php'">
                <i class="bi bi-box2-fill"></i> Lưu trữ
            </button>
        </div>
        <div class="search-group">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Tìm kiếm theo tên hoặc email..." class="search-input" id="searchInput" onkeyup="searchItems()">
        </div>
    </div>

    <!-- Khiếu nại List -->
    <div class="content-section section-khieunai">
        <div class="section-header">
            <h3><i class="bi bi-exclamation-triangle-fill"></i> Khiếu nại cần xử lý</h3>
            <span class="count-badge"><?php echo $khieunai_chua_xuly; ?> mới</span>
        </div>

        <div class="list-container">
            <?php 
            $has_khieunai = false;
            while($row = mysqli_fetch_assoc($result_khieunai)): 
                $has_khieunai = true;
            ?>
            <div class="list-item <?php echo $row['trang_thai'] == 0 ? 'priority-high' : 'priority-low'; ?>" data-type="khieunai" data-name="<?php echo strtolower($row['ho_ten']); ?>" data-email="<?php echo strtolower($row['email']); ?>">
                <div class="item-status">
                    <span class="status-dot <?php echo $row['trang_thai'] == 0 ? 'urgent' : 'info'; ?>"></span>
                </div>
                <div class="item-content">
                    <div class="item-header">
                        <div class="user-details">
                            <h4><?php echo htmlspecialchars($row['ho_ten']); ?></h4>
                            <span class="email"><?php echo htmlspecialchars($row['email']); ?></span>
                            <?php if($row['sdt']): ?>
                                <span class="phone"> | <?php echo htmlspecialchars($row['sdt']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="item-meta">
                            <span class="time"><?php echo time_elapsed_string($row['created_at']); ?></span>
                        </div>
                    </div>
                    <div class="item-body">
                        <p class="message"><?php echo  nl2br(htmlspecialchars($row['noi_dung'])); ?></p>
                    </div>
                    <div class="item-footer">
                        <button class="btn 
                        <?php 
                        if($row['trang_thai'] == 0) echo 'btn-primary';
                        elseif($row['trang_thai'] == 1) echo 'btn-primary1';
                        else echo 'btn-outline-wl-r';
                        ?>" 
                        onclick="updateStatus(<?php echo $row['id']; ?>, 'khieu_nai', <?php echo $row['trang_thai']; ?>)">
                            <i class="bi bi-check-circle"></i> 
                            <?php 
                            if($row['trang_thai'] == 0) echo 'Chưa xử lý';
                            elseif($row['trang_thai'] == 1) echo 'Đã xử lý';
                            else echo 'Đã lưu trữ';
                            ?>
                        </button>

                        <button class="btn btn-secondary" onclick="window.location.href='AdNotification.php?loai=khieu_nai&id=<?php echo $row['id']; ?>'">
                            <i class="bi bi-reply"></i> Trả lời
                        </button>

                        <button class="btn btn-outline-wl" onclick="archiveItem(<?php echo $row['id']; ?>, 'khieu_nai')">
                            <i class="bi bi-archive"></i> Lưu trữ
                        </button>

                        <button class="btn btn-outline" onclick="confirmDelete(<?php echo $row['id']; ?>, 'khieu_nai')">
                            <i class="bi bi-trash"></i> Xóa
                        </button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            
            <?php if(!$has_khieunai): ?>
            <div class="empty-state empty-state-success">
                <i class="bi bi-check-circle"></i>
                <h4>Không có khiếu nại nào</h4>
                <p>Tuyệt vời! Hiện tại không có khiếu nại nào cần xử lý. Tất cả khách hàng đều hài lòng.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Pagination cho Khiếu nại -->
        <?php if ($total_pages_khieunai > 1): ?>
        <div class="pagination-container">
            <div class="pagination-info">
                Trang <?php echo $current_page_khieunai; ?> / <?php echo $total_pages_khieunai; ?> 
                (<?php echo $total_khieunai; ?> khiếu nại)
            </div>
            
            <ul class="pagination">
                <!-- Previous Page -->
                <?php if ($current_page_khieunai > 1): ?>
                <li>
                    <a href="?page_kn=<?php echo $current_page_khieunai - 1; ?>&page_gy=<?php echo $current_page_gopy; ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php else: ?>
                <li><span class="disabled"><i class="bi bi-chevron-left"></i></span></li>
                <?php endif; ?>
                
                <!-- Page Numbers -->
                <?php for ($i = 1; $i <= $total_pages_khieunai; $i++): ?>
                <li>
                    <?php if ($i == $current_page_khieunai): ?>
                        <span class="active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page_kn=<?php echo $i; ?>&page_gy=<?php echo $current_page_gopy; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endif; ?>
                </li>
                <?php endfor; ?>
                
                <!-- Next Page -->
                <?php if ($current_page_khieunai < $total_pages_khieunai): ?>
                <li>
                    <a href="?page_kn=<?php echo $current_page_khieunai + 1; ?>&page_gy=<?php echo $current_page_gopy; ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <?php else: ?>
                <li><span class="disabled"><i class="bi bi-chevron-right"></i></span></li>
                <?php endif; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>

    <!-- Góp ý List -->
    <div class="content-section section-gopy">
        <div class="section-header">
            <h3><i class="bi bi-chat-left-text-fill"></i> Góp ý từ khách hàng</h3>
            <span class="count-badge"><?php echo $total_gopy; ?></span>
        </div>

        <div class="list-container">
            <?php 
            $has_gopy = false;
            while($row = mysqli_fetch_assoc($result_gopy)): 
                $has_gopy = true;
            ?>
            <div class="list-item" data-type="gopy" data-name="<?php echo strtolower($row['ho_ten']); ?>" data-email="<?php echo strtolower($row['email']); ?>">
                <div class="item-status">
                    <span class="status-dot success"></span>
                </div>
                <div class="item-content">
                    <div class="item-header">
                        <div class="user-details">
                            <h4><?php echo htmlspecialchars($row['ho_ten']); ?></h4>
                            <span class="email"><?php echo htmlspecialchars($row['email']); ?></span>
                            <?php if($row['sdt']): ?>
                                <span class="phone"> | <?php echo htmlspecialchars($row['sdt']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="item-meta">
                            <span class="time"><?php echo time_elapsed_string($row['created_at']); ?></span>
                        </div>
                    </div>
                    <div class="item-body">
                        <p class="message"><?php echo  nl2br(htmlspecialchars($row['noi_dung'])); ?></p>
                    </div>
                    <div class="item-footer">
                        <button class="btn 
                        <?php 
                        if($row['trang_thai'] == 0) echo 'btn-primary';
                        elseif($row['trang_thai'] == 1) echo 'btn-primary1';
                        else echo 'btn-outline-wl-r';
                        ?>" 
                        onclick="updateStatus(<?php echo $row['id']; ?>, 'gop_y', <?php echo $row['trang_thai']; ?>)">
                            <i class="bi bi-check-circle"></i> 
                            <?php 
                            if($row['trang_thai'] == 0) echo 'Chưa xử lý';
                            elseif($row['trang_thai'] == 1) echo 'Đã xử lý';
                            else echo 'Đã lưu trữ';
                            ?>
                        </button>

                        <button class="btn btn-secondary" onclick="window.location.href='AdNotification.php?loai=gop_y&id=<?php echo $row['id']; ?>'">
                            <i class="bi bi-reply"></i> Trả lời
                        </button>

                        <button class="btn btn-outline-wl" onclick="archiveItem(<?php echo $row['id']; ?>, 'gop_y')">
                            <i class="bi bi-archive"></i> Lưu trữ
                        </button>

                        <button class="btn btn-outline" onclick="confirmDelete(<?php echo $row['id']; ?>, 'gop_y')">
                            <i class="bi bi-trash"></i> Xóa
                        </button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            
            <?php if(!$has_gopy): ?>
            <div class="empty-state">
                <i class="bi bi-chat-dots"></i>
                <h4>Chưa có góp ý nào</h4>
                <p>Hiện tại chưa có góp ý từ khách hàng. Các góp ý mới sẽ xuất hiện ở đây.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Pagination cho Góp ý -->
        <?php if ($total_pages_gopy > 1): ?>
        <div class="pagination-container">
            <div class="pagination-info">
                Trang <?php echo $current_page_gopy; ?> / <?php echo $total_pages_gopy; ?> 
                (<?php echo $total_gopy; ?> góp ý)
            </div>
            
            <ul class="pagination">
                <!-- Previous Page -->
                <?php if ($current_page_gopy > 1): ?>
                <li>
                    <a href="?page_kn=<?php echo $current_page_khieunai; ?>&page_gy=<?php echo $current_page_gopy - 1; ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php else: ?>
                <li><span class="disabled"><i class="bi bi-chevron-left"></i></span></li>
                <?php endif; ?>
                
                <!-- Page Numbers -->
                <?php for ($i = 1; $i <= $total_pages_gopy; $i++): ?>
                <li>
                    <?php if ($i == $current_page_gopy): ?>
                        <span class="active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page_kn=<?php echo $current_page_khieunai; ?>&page_gy=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endif; ?>
                </li>
                <?php endfor; ?>
                
                <!-- Next Page -->
                <?php if ($current_page_gopy < $total_pages_gopy): ?>
                <li>
                    <a href="?page_kn=<?php echo $current_page_khieunai; ?>&page_gy=<?php echo $current_page_gopy + 1; ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <?php else: ?>
                <li><span class="disabled"><i class="bi bi-chevron-right"></i></span></li>
                <?php endif; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>

    <!-- Empty state khi search không có kết quả -->
    <div id="noSearchResults" class="empty-state" style="display: none;">
        <i class="bi bi-search"></i>
        <h4>Không tìm thấy kết quả</h4>
        <p>Không có kết quả nào phù hợp với từ khóa tìm kiếm của bạn. Vui lòng thử lại với từ khóa khác.</p>
    </div>

</section>
  </div>

  <script>
// Thêm function xác nhận xóa
function confirmDelete(id, table) {
    if(confirm('Bạn có chắc chắn muốn xóa vĩnh viễn mục này không? Hành động này không thể hoàn tác!')) {
        window.location.href = '../../php/ContactCTL/delete_contact.php?id=' + id + '&table=' + table + '&from=contact';
    }
}

// Update status function
function updateStatus(id, table, currentStatus) {
    if(confirm('Bạn có chắc muốn thay đổi trạng thái?')) {
        const newStatus = currentStatus == 0 ? 1 : 0;
        const urlParams = new URLSearchParams(window.location.search);
        const pageKn = urlParams.get('page_kn') || 1;
        const pageGy = urlParams.get('page_gy') || 1;
        window.location.href = `../../php/ContactCTL/update_status.php?id=${id}&table=${table}&status=${newStatus}&page_kn=${pageKn}&page_gy=${pageGy}`;
    }
}

// Delete function (cho nút Lưu trữ)
function archiveItem(id, table) {
    if(confirm('Bạn có chắc muốn lưu trữ mục này?')) {
        const urlParams = new URLSearchParams(window.location.search);
        const pageKn = urlParams.get('page_kn') || 1;
        const pageGy = urlParams.get('page_gy') || 1;
        window.location.href = `../../php/ContactCTL/update_status.php?id=${id}&table=${table}&action=archive&page_kn=${pageKn}&page_gy=${pageGy}`;
    }
}

// Filter functions
function filterAll() {
    document.querySelectorAll('.list-item').forEach(item => {
        item.style.display = 'flex';
    });
    document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'block';
    });
    document.getElementById('noSearchResults').style.display = 'none';
    setActiveFilter(0);
}

function filterKhieuNai() {
    document.querySelectorAll('.list-item').forEach(item => {
        item.style.display = 'none';
    });
    document.querySelectorAll('[data-type="khieunai"]').forEach(item => {
        item.style.display = 'flex';
    });
    document.querySelector('.section-gopy').style.display = 'none';
    document.querySelector('.section-khieunai').style.display = 'block';
    document.getElementById('noSearchResults').style.display = 'none';
    setActiveFilter(1);
}

function filterGopY() {
    document.querySelectorAll('.list-item').forEach(item => {
        item.style.display = 'none';
    });
    document.querySelectorAll('[data-type="gopy"]').forEach(item => {
        item.style.display = 'flex';
    });
    document.querySelector('.section-khieunai').style.display = 'none';
    document.querySelector('.section-gopy').style.display = 'block';
    document.getElementById('noSearchResults').style.display = 'none';
    setActiveFilter(2);
}

function setActiveFilter(index) {
    document.querySelectorAll('.filter-btn').forEach((btn, i) => {
        btn.classList.toggle('active', i === index);
    });
}

// Search function with empty state
function searchItems() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase();
    let hasResults = false;

    document.querySelectorAll('.list-item').forEach(item => {
        const name = item.getAttribute('data-name');
        const email = item.getAttribute('data-email');
        if (name.includes(searchValue) || email.includes(searchValue)) {
            item.style.display = 'flex';
            hasResults = true;
        } else {
            item.style.display = 'none';
        }
    });

    // Hiển thị empty state nếu không có kết quả và đang search
    const noResultsDiv = document.getElementById('noSearchResults');
    if (!hasResults && searchValue.length > 0) {
        document.querySelectorAll('.content-section').forEach(section => {
            section.style.display = 'none';
        });
        noResultsDiv.style.display = 'block';
    } else {
        document.querySelectorAll('.content-section').forEach(section => {
            section.style.display = 'block';
        });
        noResultsDiv.style.display = 'none';
    }
}
setTimeout(function() {
    const thongbao = document.querySelector('.thongbao');
    if(thongbao) {
        thongbao.style.display = 'none';
    }
}, 5000);

  </script>

</body>
</html>