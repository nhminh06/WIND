<?php
session_start();
include '../../db/db.php';
$sql_khieunai_archive = "SELECT * FROM khieu_nai 
                         WHERE trang_thai = 2 
                         AND created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)
                         ORDER BY created_at DESC";
$result_khieunai_archive = mysqli_query($conn, $sql_khieunai_archive);

// Lấy danh sách góp ý đã lưu trữ trong 1 ngày
$sql_gopy_archive = "SELECT * FROM gop_y 
                     WHERE trang_thai = 2 
                     AND created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)
                     ORDER BY created_at DESC";
$result_gopy_archive = mysqli_query($conn, $sql_gopy_archive);

// Đếm số lượng từ kết quả truy vấn
$count_khieunai_archive = mysqli_num_rows($result_khieunai_archive);
$count_gopy_archive = mysqli_num_rows($result_gopy_archive);

// Tổng số lưu trữ trong 1 ngày (khiếu nại + góp ý)
$total_archived_1day = $count_khieunai_archive + $count_gopy_archive;


// Lấy danh sách khiếu nại đã lưu trữ
$sql_khieunai = "SELECT * FROM khieu_nai WHERE trang_thai = 2 ORDER BY created_at DESC";
$result_khieunai = mysqli_query($conn, $sql_khieunai);

// Lấy danh sách góp ý đã lưu trữ
$sql_gopy = "SELECT * FROM gop_y WHERE trang_thai = 2 ORDER BY created_at DESC";
$result_gopy = mysqli_query($conn, $sql_gopy);

// Đếm số lượng
$count_khieunai = mysqli_num_rows($result_khieunai);
$count_gopy = mysqli_num_rows($result_gopy);
$total_archived = $count_khieunai + $count_gopy;

// Reset pointer
mysqli_data_seek($result_khieunai, 0);
mysqli_data_seek($result_gopy, 0);

// Hàm tính thời gian
function time_elapsed_string($datetime) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->d > 0) return $diff->d . ' ngày trước';
    if ($diff->h > 0) return $diff->h . ' giờ trước';
    if ($diff->i > 0) return $diff->i . ' phút trước';
    return 'Vừa xong';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Lưu trữ liên hệ</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<style>
    .phone {
        color: gray;
        font-size: 12px;
    }
    
   
</style>

<body>

<aside class="sidebar">
    <h2 class="logo">WIND Admin</h2>
    <?php include '../../includes/Adminnav.php'; ?>
</aside>

<div class="main">
    <header class="header">
        <h1>Lưu trữ liên hệ</h1>
        <div class="admin-info">
            <p>Xin chào <?php echo $_SESSION['username']; ?></p>
            <button onclick="window.location.href='Contact.php'" class="logout">Trở lại</button>
        </div>
    </header>

    <section class="content">
        
        <!-- Archive Info Header -->
      

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card stat-warning">
                <div class="stat-info">
                    <h3><?php echo $total_archived; ?></h3>
                    <p>Tổng lưu trữ</p>
                </div>
            </div>

            <div class="stat-card stat-primary">
                <div class="stat-info">
                    <h3><?php echo $count_khieunai; ?></h3>
                    <p>Khiếu nại đã lưu</p>
                </div>
            </div>

            <div class="stat-card stat-success">
                <div class="stat-info">
                    <h3><?php echo $count_gopy; ?></h3>
                    <p>Góp ý đã lưu</p>
                </div>
            </div>

            <div class="stat-card stat-info">
                <div class="stat-info">
                    <h3><?php echo $total_archived_1day ?></h3>
                    <p>Lưu trữ gần đây</p>
                </div>
            </div>
        </div>

          <div class="archive-header-info">
            <i class="bi bi-archive-fill"></i>
            <div>
                <h2>Lưu trữ liên hệ</h2>
                <p>Quản lý các khiếu nại và góp ý đã được lưu trữ. Bạn có thể khôi phục hoặc xóa vĩnh viễn.</p>
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
                <button class="filter-btn-box" onclick="window.location.href='ContactController.php'">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </button>
            </div>
            <div class="search-group">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Tìm kiếm theo tên hoặc email..." class="search-input" id="searchInput" onkeyup="searchItems()">
            </div>
        </div>

        <!-- Khiếu nại đã lưu trữ -->
        <div class="content-section section-khieunai">
            <div class="section-header">
                <h3><i class="bi bi-exclamation-triangle-fill"></i> Khiếu nại đã lưu trữ</h3>
                <span class="count-badge"><?php echo $count_khieunai; ?></span>
            </div>

            <?php if($count_khieunai > 0): ?>
            <div class="list-container">
                <?php while($row = mysqli_fetch_assoc($result_khieunai)): ?>
                <div class="list-item archived" data-type="khieunai" data-name="<?php echo strtolower($row['ho_ten']); ?>" data-email="<?php echo strtolower($row['email']); ?>">
                    <div class="item-status">
                        <span class="status-dot" style="background: #f59e0b;"></span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <div class="user-details">
                                <h4>
                                    <?php echo htmlspecialchars($row['ho_ten']); ?>
                                    <span class="archive-badge">
                                        <i class="bi bi-archive"></i> Đã lưu trữ
                                    </span>
                                </h4>
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
                            <p class="message"><?php echo htmlspecialchars($row['noi_dung']); ?></p>
                        </div>
                        <div class="item-footer">
                            <button class="btn btn-restore" onclick="restoreItem(<?php echo $row['id']; ?>, 'khieu_nai')">
                                <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                            </button>
                            <button class="btn btn-secondary" onclick="window.location.href='AdNotification.php?loai=khieu_nai&id=<?php echo $row['id']; ?>&from=storage'">
                                <i class="bi bi-reply"></i> Trả lời
                            </button>
                            <button class="btn btn-delete-forever" onclick="deleteForever(<?php echo $row['id']; ?>, 'khieu_nai')">
                                <i class="bi bi-trash3"></i> Xóa vĩnh viễn
                            </button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="empty-archive">
                <i class="bi bi-inbox"></i>
                <h4>Không có khiếu nại nào trong lưu trữ</h4>
                <p>Các khiếu nại đã lưu trữ sẽ xuất hiện ở đây</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Góp ý đã lưu trữ -->
        <div class="content-section section-gopy">
            <div class="section-header">
                <h3><i class="bi bi-chat-left-text-fill"></i> Góp ý đã lưu trữ</h3>
                <span class="count-badge"><?php echo $count_gopy; ?></span>
            </div>

            <?php if($count_gopy > 0): ?>
            <div class="list-container">
                <?php while($row = mysqli_fetch_assoc($result_gopy)): ?>
                <div class="list-item archived" data-type="gopy" data-name="<?php echo strtolower($row['ho_ten']); ?>" data-email="<?php echo strtolower($row['email']); ?>">
                    <div class="item-status">
                        <span class="status-dot" style="background: #f59e0b;"></span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <div class="user-details">
                                <h4>
                                    <?php echo htmlspecialchars($row['ho_ten']); ?>
                                    <span class="archive-badge">
                                        <i class="bi bi-archive"></i> Đã lưu trữ
                                    </span>
                                </h4>
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
                            <p class="message"><?php echo htmlspecialchars($row['noi_dung']); ?></p>
                        </div>
                        <div class="item-footer">
                            <button class="btn btn-restore" onclick="restoreItem(<?php echo $row['id']; ?>, 'gop_y')">
                                <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                            </button>
                            <button class="btn btn-secondary" onclick="window.location.href='AdNotification.php?loai=gop_y&id=<?php echo $row['id']; ?>&from=storage'">
                                <i class="bi bi-reply"></i> Trả lời
                            </button>
                            <button class="btn btn-delete-forever" onclick="deleteForever(<?php echo $row['id']; ?>, 'gop_y')">
                                <i class="bi bi-trash3"></i> Xóa vĩnh viễn
                            </button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="empty-archive">
                <i class="bi bi-inbox"></i>
                <h4>Không có góp ý nào trong lưu trữ</h4>
                <p>Các góp ý đã lưu trữ sẽ xuất hiện ở đây</p>
            </div>
            <?php endif; ?>
        </div>

    </section>
</div>

<script>
// Filter functions
function filterAll() {
    document.querySelectorAll('.list-item').forEach(item => {
        item.style.display = 'flex';
    });
    document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'block';
    });
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
    setActiveFilter(2);
}

function setActiveFilter(index) {
    document.querySelectorAll('.filter-btn').forEach((btn, i) => {
        btn.classList.toggle('active', i === index);
    });
}

// Search function
function searchItems() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.list-item').forEach(item => {
        const name = item.getAttribute('data-name');
        const email = item.getAttribute('data-email');
        if (name.includes(searchValue) || email.includes(searchValue)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

// Restore item function
function restoreItem(id, table) {
    if(confirm("Bạn có chắc muốn khôi phục mục này?")) {
        window.location.href = "../../php/ContactCTL/restore_contact.php?id=" + id + "&table=" + table;
    }
}

// Delete forever function
function deleteForever(id, table) {
    if(confirm("CẢNH BÁO: Bạn có chắc chắn muốn xóa vĩnh viễn mục này không?\n\nHành động này không thể hoàn tác!")) {
        window.location.href = "../../php/ContactCTL/delete_contact.php?id=" + id + "&table=" + table  + "&from=storage";
    }
}

</script>

</body>
</html>