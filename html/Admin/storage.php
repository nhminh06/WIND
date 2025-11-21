<?php
session_start();
include '../../db/db.php';

// Lấy danh sách khiếu nại đã lưu trữ
$sql_khieunai = "SELECT * FROM khieu_nai WHERE trang_thai = 2 ORDER BY created_at DESC";
$result_khieunai = mysqli_query($conn, $sql_khieunai);

// Lấy danh sách góp ý đã lưu trữ
$sql_gopy = "SELECT * FROM gop_y WHERE trang_thai = 2 ORDER BY created_at DESC";
$result_gopy = mysqli_query($conn, $sql_gopy);

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Lưu trữ liên hệ</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<style>
    .archive-card { border-left: 5px solid #999; }
    .btn-restore { background: #1c7ed6; color: white; }
    .btn-delete { background: #e03131; color: white; }
    .tab-btn.active { background: #15325f; color: white; }
    .tab-btn { padding: 10px 20px; border-radius: 5px; cursor: pointer; border: 1px solid #15325f; }
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
            <button onclick="window.location.href='../views/index/webindex.php'" class="logout">Trở lại</button>
        </div>
    </header>

    <section class="content">

        <!-- Tabs -->
        <div style="display:flex; gap:10px; margin-bottom:20px;">
            <button class="tab-btn active" onclick="openTab('kanaiTab')">
                <i class="bi bi-exclamation-triangle"></i> Khiếu nại đã lưu trữ
            </button>
            <button class="tab-btn" onclick="openTab('gopyTab')">
                <i class="bi bi-chat-left-text"></i> Góp ý đã lưu trữ
            </button>
        </div>

        <!-- Khiếu nại -->
        <div id="kanaiTab" class="tab-section">
            <h3><i class="bi bi-archive"></i> Khiếu nại đã lưu trữ</h3>
            <div class="list-container">
    <?php while($row = mysqli_fetch_assoc($result_khieunai)): ?>
    <div class="list-item priority-low archive-card">

        <div class="item-status">
            <span class="status-dot info"></span>
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
                        <button class="btn btn-restore"
                            onclick="restoreItem(<?php echo $row['id']; ?>, 'khieu_nai')">
                            <i class="bi bi-arrow-clockwise"></i> Khôi phục
                        </button>



                <button class="btn btn-delete"
                    onclick="deleteForever(<?php echo $row['id']; ?>, 'khieu_nai')">
                    <i class="bi bi-trash"></i> Xóa vĩnh viễn
                </button>
            </div>
        </div>

    </div>
    <?php endwhile; ?>
</div>

        </div>

        <!-- Góp ý -->
        <div id="gopyTab" class="tab-section" style="display:none;">
            <h3><i class="bi bi-archive"></i> Góp ý đã lưu trữ</h3>
          <div class="list-container">
    <?php while($row = mysqli_fetch_assoc($result_gopy)): ?>
    <div class="list-item priority-low archive-card">

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
               <button class="btn btn-restore"
                    onclick="restoreItem(<?php echo $row['id']; ?>, 'gop_y')">
                    <i class="bi bi-arrow-clockwise"></i> Khôi phục
                </button>



                <button class="btn btn-delete"
                    onclick="deleteForever(<?php echo $row['id']; ?>, 'gop_y')">
                    <i class="bi bi-trash"></i> Xóa vĩnh viễn
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
function openTab(tabName) {
    document.querySelectorAll('.tab-section').forEach(tab => tab.style.display = 'none');
    document.getElementById(tabName).style.display = 'block';

    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
}

function restoreItem(id, table) {
    if(confirm("Khôi phục mục này về trạng thái Chưa xử lý?")) {
        window.location.href = "../../php/ContactCTL/restore_contact.php?id=" + id + "&table=" + table;
    }
}

function deleteForever(id, table) {
    if(confirm("Bạn có chắc muốn xóa vĩnh viễn?")) {
        window.location.href = "../../php/ContactCTL/delete_forever.php?id=" + id + "&table=" + table;
    }
}
</script>

</body>
</html>
