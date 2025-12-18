<?php
session_start();
include '../../../db/db.php';

// Lấy user_id từ session
$user_id = $_SESSION['user_id'] ?? 1; // Tạm thời dùng 1 nếu chưa login

// Lấy phản hồi theo user_id
$sql = "
SELECT 
    ph.id,
    ph.loai,
    ph.noi_dung,
    ph.created_at
FROM phan_hoi ph
WHERE ph.user_id = ?
ORDER BY ph.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Hàm tính thời gian
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) return 'Vừa xong';
    if ($diff < 3600) return floor($diff / 60) . ' phút trước';
    if ($diff < 86400) return floor($diff / 3600) . ' giờ trước';
    if ($diff < 2592000) return floor($diff / 86400) . ' ngày trước';
    return date('d/m/Y', $timestamp);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Thông báo của tôi</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../../../css/rpusers.css" />
  <style>

    
    h1 {
      color: #333;
      margin-bottom: 10px;
    }
    p {
      color: #666;
      margin-bottom: 30px;
    }

  </style>
</head>
<body>
  <h1>Thông báo của tôi</h1>
  <p>Các thông báo và phản hồi từ hệ thống.</p>

  <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="notification-item">
        <div class="notification-header">
          <span class="notification-title">
            Phản hồi <?= $row['loai'] == 'khieu_nai' ? 'khiếu nại' : 'góp ý' ?> của bạn
          </span>
          <span class="notification-time"><?= timeAgo($row['created_at']) ?></span>
        </div>
        <div class="notification-content">
          <?= htmlspecialchars($row['noi_dung']) ?>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <div class="empty-state">
      <p ><i style="font-size: 70px;"  class="bi bi-journal-x"></i></p>
      <p>Bạn chưa có thông báo nào</p>
    </div>
  <?php endif; ?>

  <?php
  $stmt->close();
  $conn->close();
  ?>
</body>
</html>