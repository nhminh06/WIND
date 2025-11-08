<?php
session_start();
include '../../db/db.php';

// Xử lý tìm kiếm và lọc
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Query chính
$sql = "SELECT * FROM tour WHERE 1=1";

// Thêm điều kiện tìm kiếm
if (!empty($search)) {
    $sql .= " AND ten_tour LIKE ?";
}

// Thêm sắp xếp
switch ($sort) {
    case 'oldest':
        $sql .= " ORDER BY id ASC";
        break;
    case 'name':
        $sql .= " ORDER BY ten_tour ASC";
        break;
    case 'price_asc':
        $sql .= " ORDER BY gia ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY gia DESC";
        break;
    default:
        $sql .= " ORDER BY id DESC";
        break;
}

// Chuẩn bị và thực thi query
$stmt = $conn->prepare($sql);

if (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bind_param("s", $search_param);
}

$stmt->execute();
$result = $stmt->get_result();

// Đếm thống kê
$sql_stats = "SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN trang_thai = 1 THEN 1 ELSE 0 END) AS active,
    SUM(CASE WHEN trang_thai = 0 THEN 1 ELSE 0 END) AS hidden,
    AVG(gia) AS avg_price
    FROM tour";
$result_stats = $conn->query($sql_stats);
$stats = $result_stats->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Tour - Admin</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        tbody tr:hover {
            background: #414242ff;
        }
        
        
    </style>
</head>
<body>
  <aside class="sidebar">
    <h2 class="logo">WIND Admin</h2>
    <?php include '../../includes/Adminnav.php';?>
  </aside>

  <div class="main">
    <header class="header">
      <h1>Quản lý Tour</h1>
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
          <p>Tổng số tour</p>
        </div>
        <div class="stat-card">
          <h3><?php echo $stats['active']; ?></h3>
          <p>Đang hiển thị</p>
        </div>
        <div class="stat-card">
          <h3><?php echo $stats['hidden']; ?></h3>
          <p>Đang ẩn</p>
        </div>
        <div class="stat-card">
          <h3><?php echo number_format($stats['avg_price'], 0, ',', '.'); ?> ₫</h3>
          <p>Giá trung bình</p>
        </div>
      </div>

      <!-- Header with Add Button -->
      <div class="content-header">
        <h2>Danh sách tour</h2>
        <a href="add_tour.php" class="btn-add">
          <i class="bi bi-plus-circle"></i>
          Thêm tour mới
        </a>
      </div>

      <!-- Search and Filter -->
      <form method="GET" action="" class="search-filter">
        <div class="search-box">
          <input type="text" name="search" placeholder="Tìm kiếm tour..." value="<?php echo htmlspecialchars($search); ?>">
          <button type="submit"><i class="bi bi-search"></i></button>
        </div>
        <select name="sort" class="filter-select" onchange="this.form.submit()">
          <option value="newest" <?php echo ($sort == 'newest') ? 'selected' : ''; ?>>Mới nhất</option>
          <option value="oldest" <?php echo ($sort == 'oldest') ? 'selected' : ''; ?>>Cũ nhất</option>
          <option value="name" <?php echo ($sort == 'name') ? 'selected' : ''; ?>>Tên A-Z</option>
          <option value="price_asc" <?php echo ($sort == 'price_asc') ? 'selected' : ''; ?>>Giá tăng dần</option>
          <option value="price_desc" <?php echo ($sort == 'price_desc') ? 'selected' : ''; ?>>Giá giảm dần</option>
        </select>
      </form>

      <!-- Table -->
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Hình ảnh</th>
              <th>Tên Tour</th>
              <th>Số ngày</th>
              <th>Giá</th>
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
                <?php if (!empty($row['hinh_anh'])): ?>
                  <img src="../../uploads/<?php echo htmlspecialchars($row['hinh_anh']); ?>" 
                       alt="<?php echo htmlspecialchars($row['ten_tour']); ?>" 
                       class="thumbnail-img">
                <?php else: ?>
                  <div class="no-image">
                    <i class="bi bi-image"></i>
                  </div>
                <?php endif; ?>
              </td>
              <td><strong><?php echo htmlspecialchars($row['ten_tour']); ?></strong></td>
              <td>
                <i class="bi bi-calendar3"></i>
                <?php echo $row['so_ngay']; ?> ngày
              </td>
              <td class="price-text">
                <?php echo number_format($row['gia'], 0, ',', '.'); ?> ₫
              </td>
              <td>
                <?php if ($row['trang_thai'] == 1): ?>
                  <span class="badge-active">
                    <i class="bi bi-eye"></i> Đang hiển thị
                  </span>
                <?php else: ?>
                  <span class="badge-hidden">
                    <i class="bi bi-eye-slash"></i> Đang ẩn
                  </span>
                <?php endif; ?>
              </td>
              <td>
                <div class="action-buttons">
                  <a href="../../php/tourCTL/Hiddentour.php?id=<?php echo $row['id']; ?>" 
                     class="btn-icon btn-view" 
                     title="<?php echo ($row['trang_thai'] == 1) ? 'Ẩn tour' : 'Hiển thị tour'; ?>">
                    <i class="bi bi-<?php echo ($row['trang_thai'] == 1) ? 'eye-slash' : 'eye'; ?>"></i>
                  </a>
                  <a href="../../php/tourCTL/UDtour.php?id=<?php echo $row['id']; ?>" 
                     class="btn-icon btn-edit" 
                     title="Chỉnh sửa">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <button class="btn-icon btn-delete" 
                          title="Xóa" 
                          onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo addslashes($row['ten_tour']); ?>')">
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
                  <p>Không tìm thấy tour nào</p>
                </div>
              </td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>

  <script>
    function confirmDelete(tourId, tourName) {
      if (confirm('Bạn có chắc chắn muốn xóa tour "' + tourName + '"?\nHành động này không thể hoàn tác!')) {
        window.location.href = '../../php/tourCTL/DLTtour.php?id=' + tourId;
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