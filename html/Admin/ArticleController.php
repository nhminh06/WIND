<?php
session_start();
include '../../db/db.php';

// Xử lý tìm kiếm và lọc
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$loai_filter = isset($_GET['loai']) ? intval($_GET['loai']) : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Phân trang
$records_per_page = 10;
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($current_page - 1) * $records_per_page;

// Đếm tổng số bản ghi
$sql_count = "SELECT COUNT(*) as total 
FROM bai_viet bv
LEFT JOIN khampha k ON bv.khampha_id = k.khampha_id
WHERE 1=1";

if (!empty($search)) {
    $sql_count .= " AND bv.tieu_de LIKE ?";
}

if ($loai_filter > 0) {
    $sql_count .= " AND k.loai_id = ?";
}

$stmt_count = $conn->prepare($sql_count);

if (!empty($search) && $loai_filter > 0) {
    $search_param = "%$search%";
    $stmt_count->bind_param("si", $search_param, $loai_filter);
} elseif (!empty($search)) {
    $search_param = "%$search%";
    $stmt_count->bind_param("s", $search_param);
} elseif ($loai_filter > 0) {
    $stmt_count->bind_param("i", $loai_filter);
}

$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_records = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Query chính
$sql = "SELECT 
    bv.id AS bai_viet_id, 
    bv.tieu_de, 
    bv.ngay_tao,
    bv.trang_thai, 
    k.loai_id,
    CASE 
        WHEN k.loai_id = 1 THEN 'Làng nghề'
        WHEN k.loai_id = 2 THEN 'Ẩm thực'
        WHEN k.loai_id = 3 THEN 'Văn hóa'
        ELSE 'Chưa phân loại'
    END AS ten_loai,
    (SELECT COUNT(*) FROM bai_viet_muc WHERE bai_viet_id = bv.id) AS so_muc
FROM bai_viet bv
LEFT JOIN khampha k ON bv.khampha_id = k.khampha_id
WHERE 1=1";

// Thêm điều kiện tìm kiếm
if (!empty($search)) {
    $sql .= " AND bv.tieu_de LIKE ?";
}

// Thêm điều kiện lọc loại
if ($loai_filter > 0) {
    $sql .= " AND k.loai_id = ?";
}

// Thêm sắp xếp
switch ($sort) {
    case 'oldest':
        $sql .= " ORDER BY bv.ngay_tao ASC";
        break;
    case 'name':
        $sql .= " ORDER BY bv.tieu_de ASC";
        break;
    default:
        $sql .= " ORDER BY bv.ngay_tao DESC";
        break;
}

// Thêm LIMIT và OFFSET
$sql .= " LIMIT ? OFFSET ?";

// Chuẩn bị và thực thi query
$stmt = $conn->prepare($sql);

// Bind parameters
if (!empty($search) && $loai_filter > 0) {
    $search_param = "%$search%";
    $stmt->bind_param("siii", $search_param, $loai_filter, $records_per_page, $offset);
} elseif (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bind_param("sii", $search_param, $records_per_page, $offset);
} elseif ($loai_filter > 0) {
    $stmt->bind_param("iii", $loai_filter, $records_per_page, $offset);
} else {
    $stmt->bind_param("ii", $records_per_page, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

// Đếm số lượng theo loại
$sql_stats = "SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN k.loai_id = 1 THEN 1 ELSE 0 END) AS langnhe,
    SUM(CASE WHEN k.loai_id = 2 THEN 1 ELSE 0 END) AS amthuc,
    SUM(CASE WHEN k.loai_id = 3 THEN 1 ELSE 0 END) AS vanhoa
    FROM bai_viet bv
    LEFT JOIN khampha k ON bv.khampha_id = k.khampha_id";
$result_stats = $conn->query($sql_stats);
$stats = $result_stats->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý bài viết - Admin</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        tbody tr:hover {
            background: #414242ff;
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
      <h1>Quản lý bài viết</h1>
      <div class="admin-info">
        <?php echo "<p>Xin chào " . (isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin') . "</p>"; ?>
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
          <p>Tổng bài viết</p>
        </div>
        <div class="stat-card">
          <h3><?php echo $stats['langnhe']; ?></h3>
          <p>Làng nghề</p>
        </div>
        <div class="stat-card">
          <h3><?php echo $stats['amthuc']; ?></h3>
          <p>Ẩm thực</p>
        </div>
        <div class="stat-card">
          <h3><?php echo $stats['vanhoa']; ?></h3>
          <p>Văn hóa</p>
        </div>
      </div>

      <!-- Header with Add Button -->
      <div class="content-header">
        <h2>Danh sách bài viết</h2>
        <a href="AddArticleController.php" class="btn-add">
          <i class="bi bi-plus-circle"></i>
          Thêm bài viết mới
        </a>
      </div>

      <!-- Search and Filter -->
      <form method="GET" action="" class="search-filter">
        <div class="search-box">
          <input type="text" name="search" placeholder="Tìm kiếm bài viết..." value="<?php echo htmlspecialchars($search); ?>">
          <button type="submit"><i class="bi bi-search"></i></button>
        </div>
        <select name="loai" class="filter-select" onchange="this.form.submit()">
          <option value="">Tất cả loại</option>
          <option value="1" <?php echo ($loai_filter == 1) ? 'selected' : ''; ?>>Làng nghề</option>
          <option value="2" <?php echo ($loai_filter == 2) ? 'selected' : ''; ?>>Ẩm thực</option>
          <option value="3" <?php echo ($loai_filter == 3) ? 'selected' : ''; ?>>Văn hóa</option>
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
              <th>Tiêu đề bài viết</th>
              <th>Loại</th>
              <th>Số mục</th>
              <th>Ngày tạo</th>
              <th>Trạng thái</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td>#<?php echo str_pad($row['bai_viet_id'], 3, '0', STR_PAD_LEFT); ?></td>
              <td><?php echo htmlspecialchars($row['tieu_de']); ?></td>
              <td>
                <?php
                $badge_class = '';
                $loai_name = strtolower(str_replace(' ', '', $row['loai_id']));
                switch($loai_name) {
                    case '1':
                        $badge_class = 'badge-langnghề';
                        break;
                    case '2':
                        $badge_class = 'badge-ẩmthực';
                        break;
                    case '3':
                        $badge_class = 'badge-vănhóa';
                        break;
                }
                ?>
                <span class="badge <?php echo $badge_class; ?>"><?php echo $row['ten_loai']; ?></span>
              </td>
              <td><?php echo $row['so_muc']; ?> mục</td>
              <td><?php echo date('d/m/Y', strtotime($row['ngay_tao'])); ?></td>
              <td>
                <?php if ($row['trang_thai'] == 1): ?>
                  <span class="badge badge-langnghề" style="background: #d4edda; color: #155724;">Hiển thị</span>
                <?php else: ?>
                  <span class="badge badge-vănhóa" style="background: #f8d7da; color: #721c24;">Đã ẩn</span>
                <?php endif; ?>
              </td>
              <td>
                <div class="action-buttons">
                  <a href="hide_article.php?id=<?php echo $row['bai_viet_id']; ?>" class="btn-icon btn-view" title="Xem chi tiết">
                    <i class="bi bi-eye"></i>
                  </a>
                  <a href="edit_article.php?id=<?php echo $row['bai_viet_id']; ?>" class="btn-icon btn-edit" title="Chỉnh sửa">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <button class="btn-icon btn-delete" title="Xóa" onclick="confirmDelete(<?php echo $row['bai_viet_id']; ?>)">
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
                  <p>Không tìm thấy bài viết nào</p>
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
          trong tổng số <?php echo $total_records; ?> bài viết
        </div>
        
        <ul class="pagination">
          <!-- First Page -->
          <?php if ($current_page > 1): ?>
          <li>
            <a href="?page=1&search=<?php echo urlencode($search); ?>&loai=<?php echo $loai_filter; ?>&sort=<?php echo $sort; ?>">
              <i class="bi bi-chevron-double-left"></i>
            </a>
          </li>
          <?php endif; ?>
          
          <!-- Previous Page -->
          <?php if ($current_page > 1): ?>
          <li>
            <a href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>&loai=<?php echo $loai_filter; ?>&sort=<?php echo $sort; ?>">
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
              echo '<li><a href="?page=1&search=' . urlencode($search) . '&loai=' . $loai_filter . '&sort=' . $sort . '">1</a></li>';
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
              <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&loai=<?php echo $loai_filter; ?>&sort=<?php echo $sort; ?>">
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
              echo '<li><a href="?page=' . $total_pages . '&search=' . urlencode($search) . '&loai=' . $loai_filter . '&sort=' . $sort . '">' . $total_pages . '</a></li>';
          }
          ?>
          
          <!-- Next Page -->
          <?php if ($current_page < $total_pages): ?>
          <li>
            <a href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>&loai=<?php echo $loai_filter; ?>&sort=<?php echo $sort; ?>">
              <i class="bi bi-chevron-right"></i>
            </a>
          </li>
          <?php else: ?>
          <li><span class="disabled"><i class="bi bi-chevron-right"></i></span></li>
          <?php endif; ?>
          
          <!-- Last Page -->
          <?php if ($current_page < $total_pages): ?>
          <li>
            <a href="?page=<?php echo $total_pages; ?>&search=<?php echo urlencode($search); ?>&loai=<?php echo $loai_filter; ?>&sort=<?php echo $sort; ?>">
              <i class="bi bi-chevron-double-right"></i>
            </a>
          </li>
          <?php endif; ?>
        </ul>
      </div>
      <?php endif; ?>
    </section>
  </div>

  <script>
    function confirmDelete(id) {
      if (confirm('Bạn có chắc chắn muốn xóa bài viết này?\nHành động này không thể hoàn tác!')) {
        window.location.href = '../../php/ArticleCTL/delete_article.php?id=' + id;
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