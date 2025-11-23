<?php
session_start();
include '../../db/db.php';

$id = $_GET['id'] ?? 0;
$_SESSION['reply_user'] = $id;

$sql = "SELECT * FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

$title = "Thông tin người dùng";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Admin</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
  <aside class="sidebar">
    <h2 class="logo">WIND Admin</h2>
    <?php include '../../includes/Adminnav.php'; ?>
  </aside>

  <div class="main">
    <header class="header">
      <h1><?php echo $title; ?></h1>
      <div class="admin-info">
        <?php echo "<p>Xin chào " . ($_SESSION['username'] ?? 'Admin') . "</p>"; ?>
        <button onclick="window.location.href='../views/index/webindex.php'" class="logout">Trở lại</button>
      </div>
    </header>

    <section class="content">

    <?php if ($data): ?>
      <div class="reply-container">
        <h2 class="reply-title"><i class="bi bi-person-circle"></i> Thông tin chi tiết</h2>

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
          <span><?php echo htmlspecialchars($data['sdt'] ?: 'Không có'); ?></span>
        </div>

        <div class="detail-row">
          <span class="detail-label">Giới tính:</span>
          <span><?php echo htmlspecialchars($data['gioi_tinh'] ?: 'Không có'); ?></span>
        </div>

        <div class="detail-row">
          <span class="detail-label">Ngày sinh:</span>
          <span><?php echo $data['ngay_sinh'] ? date('d/m/Y', strtotime($data['ngay_sinh'])) : 'Không có'; ?></span>
        </div>

        <div class="detail-row">
          <span class="detail-label">Địa chỉ:</span>
          <span><?php echo htmlspecialchars($data['dia_chi'] ?: 'Không có'); ?></span>
        </div>


        <div class="detail-row">
          <span class="detail-label">Ảnh đại diện:</span>
          <div>
            <?php if ($data['avatar']): ?>
              <img src="../../<?php echo $data['avatar']; ?>" style="max-width:80px; border-radius:10px;">
            <?php else: ?>
              <p>Không có ảnh</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- ===========================
           FORM PHẢN HỒI USER
      ============================ -->
      <div class="reply-container">
        <h2 class="reply-title"><i class="bi bi-chat-dots-fill"></i> Gửi phản hồi</h2>

        <form action="../../php/UsersController/reply_user.php?<?php echo $data['id'] ?>" method="POST">
            <input type="hidden" name="loai" value="user">
            <input type="hidden" name="lien_he_id" value="<?php echo $id; ?>">

            <div class="input-group">
              <label for="noi_dung" class="input-label">Nội dung phản hồi:</label>
              <textarea 
                name="noi_dung"
                id="noi_dung"
                class="reply-textarea"
                placeholder="Nhập nội dung phản hồi đến người dùng..."
                required
              ></textarea>
            </div>

            <div class="action-buttons">
              <button type="submit" class="action-btn submit-btn">
                <i class="bi bi-send"></i> Gửi phản hồi
              </button>
              <a href="UserController.php" class="action-btn cancel-btn">
                <i class="bi bi-arrow-left"></i> Quay lại
              </a>
            </div>
        </form>

      </div>
    <?php else: ?>
      <div class="reply-container">
        <p style="text-align:center; color:#999;">Không tìm thấy dữ liệu!</p>
      </div>
    <?php endif; ?>

    </section>
  </div>
</body>
</html>

<?php $conn->close(); ?>
