<?php
session_start();
include '../../db/db.php';

// Lấy tổng số khiếu nại
$sql_count_khieunai = "SELECT COUNT(*) as total FROM khieu_nai";
$result_count_khieunai = mysqli_query($conn, $sql_count_khieunai);
$total_khieunai = mysqli_fetch_assoc($result_count_khieunai)['total'];

// Lấy tổng số góp ý
$sql_count_gopy = "SELECT COUNT(*) as total FROM gop_y";
$result_count_gopy = mysqli_query($conn, $sql_count_gopy);
$total_gopy = mysqli_fetch_assoc($result_count_gopy)['total'];

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
// Lấy danh sách khiếu nại (sắp xếp theo mới nhất)
$sql_khieunai = "SELECT * FROM khieu_nai ORDER BY created_at DESC";
$result_khieunai = mysqli_query($conn, $sql_khieunai);

// Lấy danh sách góp ý (sắp xếp theo mới nhất)
$sql_gopy = "SELECT * FROM gop_y ORDER BY created_at DESC";
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
<html lang="en">
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
            <span class="count-badge"><?php echo $chua_xuly; ?> mới</span>
        </div>

        <div class="list-container">
            <?php while($row = mysqli_fetch_assoc($result_khieunai)): ?>
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
                        <p class="message"><?php echo htmlspecialchars($row['noi_dung']); ?></p>
                    </div>
                                <div class="item-footer">
                    <button class="btn 
                    <?php 
                    if($row['trang_thai'] == 0) echo 'btn-primary';
                    elseif($row['trang_thai'] == 1) echo 'btn-primary1';
                    else echo 'btn-outline-wl-r'; // trạng thái 2: đã lưu trữ
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
        </div>
    </div>

    <!-- Góp ý List -->
    <div class="content-section section-gopy">
        <div class="section-header">
            <h3><i class="bi bi-chat-left-text-fill"></i> Góp ý từ khách hàng</h3>
            <span class="count-badge"><?php echo $total_gopy; ?></span>
        </div>

        <div class="list-container">
            <?php while($row = mysqli_fetch_assoc($result_gopy)): ?>
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
                        <p class="message"><?php echo htmlspecialchars($row['noi_dung']); ?></p>
                    </div>
                                        <div class="item-footer">
                        <button class="btn 
                    <?php 
                    if($row['trang_thai'] == 0) echo 'btn-primary';
                    elseif($row['trang_thai'] == 1) echo 'btn-primary1';
                    else echo 'btn-outline-wl-r'; // trạng thái 2: đã lưu trữ
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
        </div>
    </div>
</section>
  </div>

  <script>
    // Filter functions
  // Thêm function xác nhận xóa
function confirmDelete(id, table) {
    if(confirm('Bạn có chắc chắn muốn xóa vĩnh viễn mục này không? Hành động này không thể hoàn tác!')) {
        window.location.href = '../../php/ContactCTL/delete_contact.php?id=' + id + '&table=' + table;
    }
}

// Update status function
function updateStatus(id, table, currentStatus) {
    if(confirm('Bạn có chắc muốn thay đổi trạng thái?')) {
        const newStatus = currentStatus == 0 ? 1 : 0;
        window.location.href = `../../php/ContactCTL/update_status.php?id=${id}&table=${table}&status=${newStatus}`;
    }
}

// Delete function (cho nút Lưu trữ)
function archiveItem(id, table) {
    if(confirm('Bạn có chắc muốn lưu trữ mục này?')) {
        window.location.href = `../../php/ContactCTL/update_status.php?id=${id}&table=${table}&action=archive`;
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
  </script>
 

</body>
</html>