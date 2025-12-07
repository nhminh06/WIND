<!-- =============================================
     FILE: manage_announcements.php 
     Quản lý thông báo (Admin) - Updated UI
============================================= -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../db/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

// Xử lý thêm thông báo mới
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action == 'add') {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $author = $_SESSION['username'] ?? 'Admin';
        $post_date = $_POST['post_date'];
        
        if (!empty($title) && !empty($content) && !empty($post_date)) {
            $sql = "INSERT INTO announcement (title, content, author, post_date) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssss', $title, $content, $author, $post_date);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Đã thêm thông báo thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi khi thêm thông báo!';
            }
        } else {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
        }
        
        header('Location: manage_announcements.php');
        exit();
    }
    
    if ($action == 'delete') {
        $id = (int)$_POST['id'];
        $sql = "DELETE FROM announcement WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Đã xóa thông báo!';
        } else {
            $_SESSION['error'] = 'Có lỗi khi xóa thông báo!';
        }
        
        header('Location: manage_announcements.php');
        exit();
    }
    
    if ($action == 'edit') {
        $id = (int)$_POST['id'];
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $post_date = $_POST['post_date'];
        
        if (!empty($title) && !empty($content) && !empty($post_date)) {
            $sql = "UPDATE announcement SET title = ?, content = ?, post_date = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssi', $title, $content, $post_date, $id);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Đã cập nhật thông báo!';
            } else {
                $_SESSION['error'] = 'Có lỗi khi cập nhật!';
            }
        } else {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
        }
        
        header('Location: manage_announcements.php');
        exit();
    }
}

// Phân trang
$items_per_page = 5;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, $current_page);

// Đếm tổng số thông báo
$sql_count = "SELECT COUNT(*) as total FROM announcement";
$result_count = $conn->query($sql_count);
$total_items = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_items / $items_per_page);
$offset = ($current_page - 1) * $items_per_page;

// Lấy danh sách thông báo với phân trang
$sql = "SELECT * FROM announcement ORDER BY post_date DESC, created_at DESC LIMIT $offset, $items_per_page";
$result = $conn->query($sql);
$announcements = [];
while ($row = $result->fetch_assoc()) {
    $announcements[] = $row;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Thông báo - Admin</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .announcement-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 5px solid #007bff;
        }

        .announcement-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .announcement-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .announcement-title i {
            color: #007bff;
        }

        .announcement-meta {
            display: flex;
            gap: 20px;
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .announcement-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .announcement-content {
            color: #555;
            line-height: 1.8;
            white-space: pre-wrap;
        }

        .announcement-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-announcement {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-edit {
            background: #007bff;
            color: white;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .add-announcement-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .add-announcement-section h2 {
            margin: 0 0 20px 0;
            font-size: 24px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .btn-submit {
            background: #007bff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: #0056b3;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .modal-header h2 {
            margin: 0;
            color: #333;
        }

        .close {
            font-size: 28px;
            font-weight: bold;
            color: #999;
            cursor: pointer;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
            background: white;
            border-radius: 12px;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            color: #ddd;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .section-title {
            font-size: 22px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

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
      <button class="menu-toggle">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <h1>Quản lý Thông báo</h1>
      <div class="admin-info">
        <?php echo "<p>Xin chào " . (isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin') . "</p>"; ?>
        <button onclick="window.location.href='ContactController.php'" class="logout">Trở lại</button>
      </div>
    </header>

    <section class="content">
      <!-- Thông báo -->
      <?php if(isset($_SESSION['success'])): ?>
      <div class="alert alert-success">
          <i class="bi bi-check-circle-fill"></i>
          <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
      </div>
      <?php endif; ?>

      <?php if(isset($_SESSION['error'])): ?>
      <div class="alert alert-error">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
      </div>
      <?php endif; ?>

      <!-- Form thêm thông báo -->
      <div class="add-announcement-section">
          <h2>
              <i class="bi bi-plus-circle"></i>
              Tạo Thông báo Mới
          </h2>
          <form method="POST" action="">
              <input type="hidden" name="action" value="add">
              
              <div class="form-group">
                  <label><i class="bi bi-card-heading"></i> Tiêu đề</label>
                  <input type="text" name="title" placeholder="Nhập tiêu đề thông báo..." required>
              </div>
              
              <div class="form-group">
                  <label><i class="bi bi-text-paragraph"></i> Nội dung</label>
                  <textarea name="content" placeholder="Nhập nội dung chi tiết thông báo..." required></textarea>
              </div>
              
              <div class="form-group">
                  <label><i class="bi bi-calendar"></i> Ngày đăng</label>
                  <input type="date" name="post_date" value="<?php echo date('Y-m-d'); ?>" required>
              </div>
              
              <button type="submit" class="btn-submit">
                  <i class="bi bi-send"></i>
                  Gửi Thông báo
              </button>
          </form>
      </div>

      <!-- Danh sách thông báo -->
      <h2 style="color: #fff;" class="section-title">
          <i class="bi bi-list-ul"></i>
          Danh sách Thông báo (<?php echo $total_items; ?> tổng - Trang <?php echo $current_page; ?>/<?php echo $total_pages; ?>)
      </h2>

      <?php if (empty($announcements)): ?>
          <div class="empty-state">
              <i class="bi bi-inbox"></i>
              <h3>Chưa có thông báo nào</h3>
              <p>Hãy tạo thông báo đầu tiên để gửi đến nhân viên</p>
          </div>
      <?php else: ?>
          <?php foreach ($announcements as $announcement): ?>
          <div class="announcement-card">
              <div class="announcement-header">
                  <h3 class="announcement-title">
                      <i class="bi bi-megaphone-fill"></i>
                      <?php echo htmlspecialchars($announcement['title']); ?>
                  </h3>
              </div>
              
              <div class="announcement-meta">
                  <span>
                      <i class="bi bi-person-circle"></i>
                      <?php echo htmlspecialchars($announcement['author']); ?>
                  </span>
                  <span>
                      <i class="bi bi-calendar-event"></i>
                      <?php echo date('d/m/Y', strtotime($announcement['post_date'])); ?>
                  </span>
                  <span>
                      <i class="bi bi-clock"></i>
                      <?php echo date('H:i d/m/Y', strtotime($announcement['created_at'])); ?>
                  </span>
              </div>
              
              <div class="announcement-content">
                  <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
              </div>
              
              <div class="announcement-actions">
                  <button class="btn-announcement btn-edit" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($announcement)); ?>)">
                      <i class="bi bi-pencil"></i>
                      Sửa
                  </button>
                  <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa thông báo này?');">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?php echo $announcement['id']; ?>">
                      <button type="submit" class="btn-announcement btn-delete">
                          <i class="bi bi-trash"></i>
                          Xóa
                      </button>
                  </form>
              </div>
          </div>
          <?php endforeach; ?>

          <!-- Pagination -->
          <?php if ($total_pages > 1): ?>
          <div class="pagination-container">
              <div class="pagination-info">
                  Hiển thị <?php echo $offset + 1; ?> - <?php echo min($offset + $items_per_page, $total_items); ?> 
                  trong tổng số <?php echo $total_items; ?> thông báo
              </div>
              
              <ul class="pagination">
                  <!-- First Page -->
                  <?php if ($current_page > 1): ?>
                  <li>
                      <a href="?page=1">
                          <i class="bi bi-chevron-double-left"></i>
                      </a>
                  </li>
                  <?php endif; ?>
                  
                  <!-- Previous Page -->
                  <?php if ($current_page > 1): ?>
                  <li>
                      <a href="?page=<?php echo $current_page - 1; ?>">
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
                      echo '<li><a href="?page=1">1</a></li>';
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
                          <a href="?page=<?php echo $i; ?>">
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
                      echo '<li><a href="?page=' . $total_pages . '">' . $total_pages . '</a></li>';
                  }
                  ?>
                  
                  <!-- Next Page -->
                  <?php if ($current_page < $total_pages): ?>
                  <li>
                      <a href="?page=<?php echo $current_page + 1; ?>">
                          <i class="bi bi-chevron-right"></i>
                      </a>
                  </li>
                  <?php else: ?>
                  <li><span class="disabled"><i class="bi bi-chevron-right"></i></span></li>
                  <?php endif; ?>
                  
                  <!-- Last Page -->
                  <?php if ($current_page < $total_pages): ?>
                  <li>
                      <a href="?page=<?php echo $total_pages; ?>">
                          <i class="bi bi-chevron-double-right"></i>
                      </a>
                  </li>
                  <?php endif; ?>
              </ul>
          </div>
          <?php endif; ?>
      <?php endif; ?>
    </section>
  </div>

  <!-- Modal sửa thông báo -->
  <div id="editModal" class="modal">
      <div class="modal-content">
          <div class="modal-header">
              <h2><i class="bi bi-pencil"></i> Sửa Thông báo</h2>
              <span class="close" onclick="closeEditModal()">&times;</span>
          </div>
          <form method="POST" action="">
              <input type="hidden" name="action" value="edit">
              <input type="hidden" name="id" id="edit_id">
              
              <div class="form-group">
                  <label><i class="bi bi-card-heading"></i> Tiêu đề</label>
                  <input type="text" name="title" id="edit_title" required>
              </div>
              
              <div class="form-group">
                  <label><i class="bi bi-text-paragraph"></i> Nội dung</label>
                  <textarea name="content" id="edit_content" required></textarea>
              </div>
              
              <div class="form-group">
                  <label><i class="bi bi-calendar"></i> Ngày đăng</label>
                  <input type="date" name="post_date" id="edit_post_date" required>
              </div>
              
              <button type="submit" class="btn-submit">
                  <i class="bi bi-check-circle"></i>
                  Cập nhật
              </button>
          </form>
      </div>
  </div>

  <div class="sidebar-overlay"></div>
  <script src="../../js/Main5.js"></script>
  <script>
      function openEditModal(announcement) {
          document.getElementById('edit_id').value = announcement.id;
          document.getElementById('edit_title').value = announcement.title;
          document.getElementById('edit_content').value = announcement.content;
          document.getElementById('edit_post_date').value = announcement.post_date;
          document.getElementById('editModal').style.display = 'block';
      }
      
      function closeEditModal() {
          document.getElementById('editModal').style.display = 'none';
      }
      
      window.onclick = function(event) {
          const modal = document.getElementById('editModal');
          if (event.target == modal) {
              closeEditModal();
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
<?php $conn->close(); ?>