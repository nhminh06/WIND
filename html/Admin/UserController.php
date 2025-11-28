<?php
session_start();
include '../../db/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

// Xử lý tìm kiếm và lọc
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Phân trang
$records_per_page = 10;
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($current_page - 1) * $records_per_page;

// Đếm tổng số bản ghi
$sql_count = "SELECT COUNT(*) as total FROM user WHERE 1=1";

$count_params = [];
$count_types = '';

if (!empty($search)) {
    $sql_count .= " AND (ho_ten LIKE ? OR email LIKE ? OR sdt LIKE ?)";
    $search_param = "%$search%";
    $count_params[] = $search_param;
    $count_params[] = $search_param;
    $count_params[] = $search_param;
    $count_types .= 'sss';
}

if (!empty($role_filter)) {
    $sql_count .= " AND role = ?";
    $count_params[] = $role_filter;
    $count_types .= 's';
}

if ($status_filter !== '') {
    $sql_count .= " AND trang_thai = ?";
    $count_params[] = $status_filter;
    $count_types .= 'i';
}

$stmt_count = $conn->prepare($sql_count);

if (!empty($count_params)) {
    $stmt_count->bind_param($count_types, ...$count_params);
}

$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_records = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Query chính
$sql = "SELECT * FROM user WHERE 1=1";

// Thêm điều kiện tìm kiếm
if (!empty($search)) {
    $sql .= " AND (ho_ten LIKE ? OR email LIKE ? OR sdt LIKE ?)";
}

// Thêm điều kiện lọc role
if (!empty($role_filter)) {
    $sql .= " AND role = ?";
}

// Thêm điều kiện lọc trạng thái
if ($status_filter !== '') {
    $sql .= " AND trang_thai = ?";
}

// Thêm sắp xếp
switch ($sort) {
    case 'oldest':
        $sql .= " ORDER BY id ASC";
        break;
    case 'name':
        $sql .= " ORDER BY ho_ten ASC";
        break;
    default:
        $sql .= " ORDER BY id DESC";
        break;
}

// Thêm LIMIT và OFFSET
$sql .= " LIMIT ? OFFSET ?";

// Chuẩn bị và thực thi query
$stmt = $conn->prepare($sql);

// Bind parameters
$params = [];
$types = '';

if (!empty($search)) {
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

if (!empty($role_filter)) {
    $params[] = $role_filter;
    $types .= 's';
}

if ($status_filter !== '') {
    $params[] = $status_filter;
    $types .= 'i';
}

$params[] = $records_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt->bind_param($types, ...$params);

$stmt->execute();
$result = $stmt->get_result();

// Đếm thống kê
$sql_stats = "SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) AS admin_count,
    SUM(CASE WHEN role = 'staff' THEN 1 ELSE 0 END) AS staff_count,
    SUM(CASE WHEN role = 'user' THEN 1 ELSE 0 END) AS user_count,
    SUM(CASE WHEN trang_thai = 1 THEN 1 ELSE 0 END) AS active
    FROM user";
$result_stats = $conn->query($sql_stats);
$stats = $result_stats->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Người dùng - Admin</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        tbody tr:hover {
            background: #414242ff;
        }
        
        .badge-admin {
            background: #dc3545;
            color: white;
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-staff {
            background: #28a745;
            color: white;
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-user {
            background: #007bff;
            color: white;
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-active {
            background: #d4edda;
            color: #155724;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
        }
        
        .badge-inactive {
            background: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
        }
        
        .user-info h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
        }

        .contact-info {
            font-size: 13px;
        }

        .contact-info i {
            color: #666;
            margin-right: 5px;
        }

        /* Pagination Styles */
       
    </style>
</head>
<body>
  <aside class="sidebar">
    <h2 class="logo">WIND Admin</h2>
    <?php include '../../includes/Adminnav.php';?>
  </aside>

  <div class="main">
    <header class="header">
      <h1>Quản lý Người dùng</h1>
      <div class="admin-info">
        <?php echo "<p>Xin chào " . (isset($_SESSION['ho_ten']) ? $_SESSION['ho_ten'] : 'Admin') . "</p>"; ?>
        <button onclick="window.location.href='../views/index/webindex.php'" class="logout">Trở lại</button>
      </div>
    </header>

    <section class="content">
      <!-- Thông báo -->
      <?php if(isset($_SESSION['success'])): ?>
      <div class="alert alert-success">
          <i class="bi bi-check-circle-fill"></i>
          <?php 
          echo $_SESSION['success']; 
          unset($_SESSION['success']);
          ?>
      </div>
      <?php endif; ?>

      <?php if(isset($_SESSION['error'])): ?>
      <div class="alert alert-error">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <?php 
          echo $_SESSION['error']; 
          unset($_SESSION['error']);
          ?>
      </div>
      <?php endif; ?>

      <!-- Statistics Cards -->
      <div class="stats-cards">
        <div class="stat-card">
          <h3><?php echo $stats['total']; ?></h3>
          <p>Tổng người dùng</p>
        </div>
        <div class="stat-card">
          <h3><?php echo $stats['admin_count']; ?></h3>
          <p>Quản trị viên</p>
        </div>
        <div class="stat-card">
          <h3><?php echo $stats['staff_count']; ?></h3>
          <p>Nhân viên</p>
        </div>
        <div class="stat-card">
          <h3><?php echo $stats['active']; ?></h3>
          <p>Đang hoạt động</p>
        </div>
      </div>

      <!-- Header with Add Button -->
      <div class="content-header">
        <h2>Danh sách người dùng</h2>
        <a href="add_user.php" class="btn-add">
          <i class="bi bi-person-plus"></i>
          Thêm người dùng
        </a>
      </div>

      <!-- Search and Filter -->
      <form method="GET" action="" class="search-filter">
        <div class="search-box">
          <input type="text" name="search" placeholder="Tìm kiếm người dùng..." value="<?php echo htmlspecialchars($search); ?>">
          <button type="submit"><i class="bi bi-search"></i></button>
        </div>
        <select name="role" class="filter-select" onchange="this.form.submit()">
          <option value="">Tất cả vai trò</option>
          <option value="admin" <?php echo ($role_filter == 'admin') ? 'selected' : ''; ?>>Quản trị viên</option>
          <option value="staff" <?php echo ($role_filter == 'staff') ? 'selected' : ''; ?>>Nhân viên</option>
          <option value="user" <?php echo ($role_filter == 'user') ? 'selected' : ''; ?>>Người dùng</option>
        </select>
        <select name="status" class="filter-select" onchange="this.form.submit()">
          <option value="">Tất cả trạng thái</option>
          <option value="1" <?php echo ($status_filter === '1') ? 'selected' : ''; ?>>Hoạt động</option>
          <option value="0" <?php echo ($status_filter === '0') ? 'selected' : ''; ?>>Đã khóa</option>
        </select>
        <select name="sort" class="filter-select" onchange="this.form.submit()">
          <option value="newest" <?php echo ($sort == 'newest') ? 'selected' : ''; ?>>Mới nhất</option>
          <option value="oldest" <?php echo ($sort == 'oldest') ? 'selected' : ''; ?>>Cũ nhất</option>
          <option value="name" <?php echo ($sort == 'name') ? 'selected' : ''; ?>>Tên A-Z</option>
        </select>
        <input type="hidden" name="page" value="<?php echo $current_page; ?>">
      </form>

      <!-- Table -->
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Người dùng</th>
              <th>Liên hệ</th>
              <th>Vai trò</th>
              <th>Ngày tạo</th>
              <th>Trạng thái</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td>#<?php echo str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?></td>
              <td>
                <div class="user-info">
                  <h4><?php echo htmlspecialchars($row['ho_ten']); ?></h4>
                </div>
              </td>
              <td>
                <div class="contact-info">
                  <div><i class="bi bi-envelope"></i><?php echo htmlspecialchars($row['email']); ?></div>
                  <div><i class="bi bi-telephone"></i><?php echo htmlspecialchars($row['sdt'] ?? 'Chưa có'); ?></div>
                </div>
              </td>
              <td>
                <?php
                $badge_class = '';
                $icon = '';
                $text = '';
                switch($row['role']) {
                    case 'admin':
                        $badge_class = 'badge-admin';
                        $icon = 'bi-shield-check';
                        $text = 'Admin';
                        break;
                    case 'staff':
                        $badge_class = 'badge-staff';
                        $icon = 'bi-person-badge';
                        $text = 'Staff';
                        break;
                    case 'user':
                        $badge_class = 'badge-user';
                        $icon = 'bi-person';
                        $text = 'User';
                        break;
                }
                ?>
                <span class="<?php echo $badge_class; ?>">
                  <i class="<?php echo $icon; ?>"></i> <?php echo $text; ?>
                </span>
              </td>
              <td>
                <span style="font-size: 13px; color: #666;">
                  <i class="bi bi-calendar3"></i> 
                  <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                </span>
              </td>
              <td>
                <?php if ($row['trang_thai'] == 1): ?>
                  <span class="badge-active">
                    <i class="bi bi-check-circle"></i> Hoạt động
                  </span>
                <?php else: ?>
                  <span class="badge-inactive">
                    <i class="bi bi-x-circle"></i> Đã khóa
                  </span>
                <?php endif; ?>
              </td>
              <td>
                <div class="action-buttons">
                  <a href="AdNotificationuser.php?id=<?php echo $row['id']; ?>" class="btn-icon btn-view-1" title="Liên hệ">
                    <i class="bi bi-chat-dots"></i>
                  </a>

                  <a href="#" 
                    class="btn-icon btn-edit" 
                    title="Phân quyền" 
                    onclick="openRoleDialog(<?php echo $row['id']; ?>)">
                    <i class="bi bi-person-gear"></i>
                  </a>

                  <?php if ($row['trang_thai'] == 1): ?>
                  <a href="../../php/UserCTL/lock_user.php?id=<?php echo $row['id']; ?>" 
                     class="btn-icon btn-view" 
                     title="Khóa tài khoản"
                     onclick="return confirm('Bạn có chắc muốn khóa tài khoản <?php echo htmlspecialchars($row['ho_ten']); ?>?')">
                    <i class="bi bi-lock"></i>
                  </a>
                  <?php else: ?>
                  <a href="../../php/UserCTL/unlock_user.php?id=<?php echo $row['id']; ?>" 
                     class="btn-icon btn-view" 
                     title="Mở khóa tài khoản"
                     onclick="return confirm('Bạn có chắc muốn mở khóa tài khoản <?php echo htmlspecialchars($row['ho_ten']); ?>?')">
                    <i class="bi bi-unlock"></i>
                  </a>
                  <?php endif; ?>
                  <button class="btn-icon btn-delete" 
                          title="Xóa người dùng" 
                          onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['ho_ten']); ?>')">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
              <?php endwhile; ?>
            <?php else: ?>
            <tr>
              <td colspan="7">
                <div class="no-data">
                  <i class="bi bi-inbox"></i>
                  <p>Không tìm thấy người dùng nào</p>
                </div>
              </td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php if ($total_pages > 1): ?>
      <div class="pagination-container">
        <div class="pagination-info">
          Hiển thị <?php echo $offset + 1; ?> - <?php echo min($offset + $records_per_page, $total_records); ?> 
          trong tổng số <?php echo $total_records; ?> người dùng
        </div>
        
        <ul class="pagination">
          <!-- First Page -->
          <?php if ($current_page > 1): ?>
          <li>
            <a href="?page=1&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>&status=<?php echo urlencode($status_filter); ?>&sort=<?php echo $sort; ?>">
              <i class="bi bi-chevron-double-left"></i>
            </a>
          </li>
          <?php endif; ?>
          
          <!-- Previous Page -->
          <?php if ($current_page > 1): ?>
          <li>
            <a href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>&status=<?php echo urlencode($status_filter); ?>&sort=<?php echo $sort; ?>">
              <i class="bi bi-chevron-left"></i>
            </a>
          </li>
          <?php else: ?>
          <li><span class="disabled"><i class="bi bi-chevron-left"></i></span></li>
          <?php endif; ?>
          
          <!-- Page Numbers -->
          <?php
          $start_page = max(1, $current_page - 2);
          $end_page = min($total_pages, $current_page + 2);
          
          if ($start_page > 1) {
              echo '<li><a href="?page=1&search=' . urlencode($search) . '&role=' . urlencode($role_filter) . '&status=' . urlencode($status_filter) . '&sort=' . $sort . '">1</a></li>';
              if ($start_page > 2) {
                  echo '<li><span>...</span></li>';
              }
          }
          
          for ($i = $start_page; $i <= $end_page; $i++):
          ?>
          <li>
            <?php if ($i == $current_page): ?>
              <span class="active"><?php echo $i; ?></span>
            <?php else: ?>
              <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>&status=<?php echo urlencode($status_filter); ?>&sort=<?php echo $sort; ?>">
                <?php echo $i; ?>
              </a>
            <?php endif; ?>
          </li>
          <?php 
          endfor;
          
          if ($end_page < $total_pages) {
              if ($end_page < $total_pages - 1) {
                  echo '<li><span>...</span></li>';
              }
              echo '<li><a href="?page=' . $total_pages . '&search=' . urlencode($search) . '&role=' . urlencode($role_filter) . '&status=' . urlencode($status_filter) . '&sort=' . $sort . '">' . $total_pages . '</a></li>';
          }
          ?>
          
          <!-- Next Page -->
          <?php if ($current_page < $total_pages): ?>
          <li>
            <a href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>&status=<?php echo urlencode($status_filter); ?>&sort=<?php echo $sort; ?>">
              <i class="bi bi-chevron-right"></i>
            </a>
          </li>
          <?php else: ?>
          <li><span class="disabled"><i class="bi bi-chevron-right"></i></span></li>
          <?php endif; ?>
          
          <!-- Last Page -->
          <?php if ($current_page < $total_pages): ?>
          <li>
            <a href="?page=<?php echo $total_pages; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>&status=<?php echo urlencode($status_filter); ?>&sort=<?php echo $sort; ?>">
              <i class="bi bi-chevron-double-right"></i>
            </a>
          </li>
          <?php endif; ?>
        </ul>
      </div>
      <?php endif; ?>
    </section>
  </div>

<!-- Hộp thoại phân quyền -->
<div id="roleDialog" class="overlay" style="display:none;">
  <div class="dialog-box">
    <h3>Chọn quyền mới</h3>
    <form action="../../php/UserCTL/change_role.php" method="GET">
         <input type="hidden" id="userId" name="id">

      <select name="role" class="select-css" required>
        <option value="">-- Chọn quyền --</option>
        <option class="option-css" style="color: #dc3545;
        <?php if($_SESSION['rank']!=1){
          echo "display:none;";
        }  ?>
        " value="admin">Admin</option>
        <option class="option-css" style="color: #28a745;" value="staff">Staff</option>
        <option class="option-css" style="color: #449ce3ff;" value="user">User</option>
      </select>

      <div style="margin-top:120px; text-align:right;">
        <button class="role-btn" style="background: #dc3545;" type="button" onclick="closeRoleDialog()">Hủy</button>
        <button class="role-btn" style="background: #198754;" type="submit">Lưu</button>
      </div>
    </form>
  </div>
</div>

  <script>
    function openRoleDialog(id) {
  document.getElementById('userId').value = id;
  document.getElementById('roleDialog').style.display = 'flex';
}

function closeRoleDialog() {
  document.getElementById('roleDialog').style.display = 'none';
}

    function confirmDelete(userId, userName) {
      if (confirm('Bạn có chắc chắn muốn XÓA VĨNH VIỄN tài khoản "' + userName + '"?\n\nHành động này sẽ:\n- Xóa tất cả dữ liệu người dùng\n- KHÔNG THỂ HOÀN TÁC!\n\nNhấn OK để xác nhận xóa.')) {
        window.location.href = '../../php/UsersController/delete_user.php?id=' + userId;
      }
    }

    // Auto hide alerts after 5 seconds
    setTimeout(function() {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 300);
      });
    }, 5000);
  </script>
</body>
</html>
<?php
$conn->close();
?>