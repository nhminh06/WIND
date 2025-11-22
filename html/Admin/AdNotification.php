<?php
session_start();
include '../../db/db.php';

$loai = $_GET['loai'] ?? '';
$id = $_GET['id'] ?? 0;
$from = $_GET['from'] ?? 'contact';

if ($loai == 'khieu_nai') {
    $sql = "SELECT * FROM khieu_nai WHERE id = ?";
    $title = "Chi tiết khiếu nại";
} else {
    $sql = "SELECT * FROM gop_y WHERE id = ?";
    $title = "Chi tiết góp ý";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Admin</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        
    </style>
</head>
<body>
  <aside class="sidebar">
    <h2 class="logo">WIND Admin</h2>
    <?php include '../../includes/Adminnav.php';?>
  </aside>

  <div class="main">
    <header class="header">
      <h1><?php echo $title; ?></h1>
      <div class="admin-info">
        <?php echo "<p>Xin chào " . (isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin') . "</p>"; ?>
        <button onclick="window.location.href='../views/index/webindex.php'" class="logout">Trở lại</button>
      </div>
    </header>

    <section class="content">
      <?php if ($data): ?>
      <div class="reply-container">
        <h2 class="reply-title"><i class="bi bi-file-earmark"></i> Thông tin chi tiết</h2>
        
        <div class="detail-row">
          <span class="detail-label">Họ tên:</span>
          <span><?php echo htmlspecialchars($data['ho_ten']); ?></span>
        </div>
        
        <div class="detail-row">
          <span class="detail-label">Email:</span>
          <span><?php echo htmlspecialchars($data['email']); ?></span>
        </div>
        
        <div class="detail-row">
          <span class="detail-label">SĐT:</span>
          <span><?php echo htmlspecialchars($data['sdt'] ?? 'Không có'); ?></span>
        </div>
        
        <div class="detail-row">
          <span class="detail-label">Ngày gửi:</span>
          <span><?php echo date('d/m/Y H:i', strtotime($data['created_at'])); ?></span>
        </div>
        
        <div class="detail-row">
          <span class="detail-label">Trạng thái:</span>
          <?php if ($data['trang_thai'] == 0): ?>
            <span class="status-badge status-pending">Chưa xử lý</span>
          <?php else: ?>
            <span class="status-badge status-done">Đã xử lý</span>
          <?php endif; ?>
        </div>
        
        <div class="detail-row">
          <span class="detail-label">Nội dung:</span>
          <div class="message-display">
            <?php echo nl2br(htmlspecialchars($data['noi_dung'])); ?>
          </div>
        </div>
      </div>

      <div class="reply-container">
        <h2 class="reply-title"><i class="bi bi-chat-fill"></i> Phản hồi</h2>
        
        <form action="../../php/ContactCTL/reply.php" method="POST">
          <input type="hidden" name="loai" value="<?php echo $loai; ?>">
          <input type="hidden" name="lien_he_id" value="<?php echo $id; ?>">
             <input type="hidden" name="from" value="<?php echo $from; ?>">
          
          <div class="input-group">
            <label class="input-label" for="noi_dung">Nội dung phản hồi:</label>
         
            <textarea 
              name="noi_dung" 
              id="noi_dung" 
              class="reply-textarea" 
              placeholder="Nhập nội dung phản hồi cho khách hàng..."
              required
            ></textarea>
          </div>
          
          <div class="action-buttons">
            <button type="submit" class="action-btn submit-btn">
              <i class="bi bi-send"></i> Gửi phản hồi
            </button>
            <a href="contactcontroller.php" class="action-btn cancel-btn">
              <i class="bi bi-arrow-left"></i> Quay lại
            </a>
          </div>
        </form>
      </div>
      <?php else: ?>
      <div class="reply-container">
        <p style="text-align: center; color: #999;">Không tìm thấy dữ liệu!</p>
      </div>
      <?php endif; ?>
    </section>
  </div>
</body>
</html>
<?php
$conn->close();
?>