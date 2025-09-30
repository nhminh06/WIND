<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tài khoản</title>
  <link rel="stylesheet" href="../../css/Main5.css" />
  <!-- Link Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

</head>
<body>
  <?php session_start(); ?>
  <?php 
   if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'){
    echo "<style>.admin-bt{opacity: 1; display: block;}</style>";
   }
  ?>
  <div class="users_container">
  
    <aside class="sidebar">
      <div class="profile">
        <div class="avatar">
          <?php
          
          ?>
        </div>
        <div class="provider">
          <?php
          if (isset($_SESSION['username'])) {
            echo "<p>Xin chào, " . htmlspecialchars($_SESSION['username']) . "</p>";
          } else {
            echo "<p>Khách</p>";
          }?>
        </div>
      </div>
      <button class="vip-button"><i class="bi bi-box2-heart"></i> Bạn là ưu tiên hạng đầu của chúng tôi</button>
      <ul class="menu">
        <li onclick="loadpage('Cards.html', this)"><i class="bi bi-bell-fill"></i> Thẻ của tôi</li>
        <li onclick="loadpage('Mytour.html', this)"><i class="bi bi-calendar3"></i> Đặt chỗ của tôi</li>
        <li onclick="loadpage('transaction-history.html', this)"><i class="bi bi-cart-dash"></i> Lịch sử giao dịch</li>
        <li onclick="loadpage('refund-section.html', this)"><i class="bi bi-envelope-paper-fill"></i> Hoàn tiền</li>
        <li onclick="loadpage('price-alert.html', this)"><i class="bi bi-bell-fill"></i> Cảnh báo giá vé máy bay</li>
        <li onclick="loadpage('user-tours.html', this)"><i class="bi bi-envelope-paper-fill"></i> Chi tiết hành khách đã lưu</li>
        <li onclick="loadpage('notification-settings.html', this)"><i class="bi bi-bell-fill"></i> Cài đặt thông báo</li>
        <li onclick="loadpage('settinguser.html' , this)" class="active"><i class="bi bi-gear"></i> Tài khoản của tôi</li>
        <li class="logout"><i class="bi bi-door-closed-fill"></i> Đăng xuất</li>
        <li><button onclick="window.location.href = '../../html/Admin/IndexController.php'" class="admin-bt">Mở giao diện quản lý</button></li>
      </ul>
    </aside>


    <main id="content" class="content">

    </main>
  </div>
  <script src="../../js/Main5.js"></script>
     <script>

  window.onload = function () {
    loadpage('settinguser.html', document.querySelector('li.active'));
  };
</script>
</body>
</html>
