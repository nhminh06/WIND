<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tài khoản</title>
  <link rel="stylesheet" href="../../../css/Main5.css" />
  <!-- Link Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

</head>
<body>
  <?php session_start(); ?>
  <?php 
  if(empty($_SESSION['username'])){
    header("Location: ../index/Note.php");
    exit();
  }
  ?>
  <?php 
   if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'){
    echo "<style>.admin-bt{opacity: 1; display: block;}</style>";
   }

  ?>
  <div class="users_container">
  
    <aside class="sidebar">
      <div class="profile">
        <button onclick="window.location.href = '../index/Webindex.php'" class="left-btn"><i class="bi bi-arrow-left-circle"></i></button>
        <div class="avatar">
  <img id="avatarImg" src="<?php echo "../../../../" . (!empty($_SESSION['avatar']) ? $_SESSION['avatar'] : 'img/avt1.jpg'); ?>" alt="Ảnh đại diện" style="cursor: pointer;">
  <input type="file" id="avatarInput" accept="image/*" style="display: none;">
</div>



        <div class="provider"><?php echo $_SESSION['username']; ?></div>

        
      </div>
      <button class="vip-button"><i class="bi bi-box2-heart"></i> Bạn là ưu tiên hạng đầu của chúng tôi</button>
      <ul class="menu">
        <li onclick="loadpage('Cards.html', this)"><i class="bi bi-bell-fill"></i> Thẻ của tôi</li>
        <li onclick="loadpage('Mytour.html', this)"><i class="bi bi-calendar3"></i> Đặt chỗ của tôi</li>
        <li onclick="loadpage('transaction-history.html', this)"><i class="bi bi-cart-dash"></i> Lịch sử giao dịch</li>
        <li onclick="loadpage('refund-section.html', this)"><i class="bi bi-envelope-paper-fill"></i> Hoàn tiền</li>
        <li onclick="loadpage('price-alert.html', this)"><i class="bi bi-bell-fill"></i> Cảnh báo giá vé máy bay</li>
        <li onclick="loadpage('user-tours.html', this)"><i class="bi bi-envelope-paper-fill"></i> Chi tiết hành khách đã lưu</li>
        <li onclick="loadpage('notification-settings.php', this)"><i class="bi bi-bell-fill"></i> Cài đặt thông báo</li>
        <li onclick="loadpage('settinguser.php' , this)" class="active"><i class="bi bi-gear"></i> Tài khoản của tôi</li>
        <li onclick="window.location.href = '../../../php/logout.php'" class="logout"><i class="bi bi-door-closed-fill"></i> Đăng xuất</li>
        <li><button onclick="window.location.href = '../../Admin/IndexController.php'" class="admin-bt">Mở giao diện quản lý</button></li>
      </ul>
    <div class="thongbao" 
     <?php 
     if(isset($_SESSION['thanhcong'])) { 
         $bgColor = ($_SESSION['thanhcong'] == 1) ? '#4BB543' : '#FF3333';
         echo 'style="display: flex; background-color: ' . $bgColor . ';"'; 
     } else { 
         echo 'style="display: none;"'; 
     } 
     ?>> 
    <?php 
    if(isset($_SESSION['thanhcong'])) {
        if($_SESSION['thanhcong'] == 1) {
            echo '<i class="bi bi-bookmark-check"></i> Chỉnh sửa thành công!';
        } else {
            echo '<i class="bi bi-x-circle"></i> Chỉnh sửa thất bại!';
        }
        unset($_SESSION['thanhcong']);
    }
    ?>
</div>
    </aside>


    <main id="content" class="content">

    </main>
  </div>
  <script src="../../../js/Main5.js">
  </script>
     <script>
       

  const avatarImg = document.getElementById("avatarImg");
  const avatarInput = document.getElementById("avatarInput");

  // Khi nhấn vào ảnh, bật chọn file
  avatarImg.addEventListener("click", () => avatarInput.click());

  // Khi chọn file mới
  avatarInput.addEventListener("change", () => {
    const file = avatarInput.files[0];
    if (file) {
      // Hiển thị xem trước ảnh
      const reader = new FileReader();
      reader.onload = e => avatarImg.src = e.target.result;
      reader.readAsDataURL(file);

      // Gửi ảnh lên server để lưu vào CSDL
      const formData = new FormData();
      formData.append('avatar', file);

      fetch('../../../php/UsersController/upload_avatar.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          $_SESSION['thanhcong'] = 1;
        } else {
          $_SESSION['thanhcong'] = 0;
        }
      })
      .catch(err => $_SESSION['thanhcong'] = 0);
    }
  });



setTimeout(function() {
    const thongbao = document.querySelector('.thongbao');
    if(thongbao) {
        thongbao.style.display = 'none';
    }
}, 5000);
  window.onload = function () {
    loadpage('settinguser.php', document.querySelector('li.active'));
  };
</script>
</body>
</html>
