<?php 
session_start();
include '../../db/db.php';

// Lấy thông tin user từ database
$sql = "SELECT * FROM user WHERE id = ".$_SESSION['user_id'];
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0){
    $row = mysqli_fetch_assoc($result);

    // Xử lý ngày sinh
    $ngay_sinh = $row['ngay_sinh'] ?? '2000-01-01';
    list($nam, $thang, $ngay) = explode('-', $ngay_sinh);
    $thang = (int)$thang;
    $ngay = (int)$ngay;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin - Cài đặt</title>
<link rel="stylesheet" href="../../css/Admin.css">
<link rel="stylesheet" href="../../css/AdminSettings.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
.content { padding: 0 !important; }
</style>
</head>
<body>

<aside class="sidebar">
    <h2 class="logo">WIND Admin</h2>
    <?php include '../../includes/Adminnav.php'; ?>
</aside>

<div class="main">
    <header class="header">
        <h1>Bảng điều khiển</h1>
        <div class="admin-info">
            <p>Xin chào <?= $_SESSION['username']; ?></p>
            <button onclick="window.location.href='../views/index/webindex.php'" class="logout">Trở lại</button>
        </div>
    </header>

    <section class="content">
        <div class="settings-wrapper">
            <div class="settings-container">

                <!-- Tabs -->
                <div class="settings-header">
                    <div class="settings-tabs">
                        <div class="settings-tab active-tab">Thông tin tài khoản</div>
                        <div class="settings-tab">Mật khẩu & Bảo mật</div>
                    </div>
                </div>

                <div class="settings-content">

                    <!-- Tab Thông tin tài khoản -->
                    <div class="account-section">

                        <!-- Form dữ liệu cá nhân -->
                        <form method="POST" action="../../php/UsersController/update_profile.php?id=<?= $_SESSION['user_id'] ?>">
                            <div class="settings-section">
                                <h2>Dữ liệu cá nhân</h2>
                                 <div class="avatar">
  <img id="avatarImg" src="<?php echo "../../../" . (!empty($_SESSION['avatar']) ? $_SESSION['avatar'] : 'img/avt2.jpg'); ?>" alt="Ảnh đại diện" style="cursor: pointer;">
  <input type="file" id="avatarInput" accept="image/*" style="display: none;">
</div>
                                <div class="settings-form-group">
                                    <label>Họ và tên đầy đủ</label>
                                    <input type="text" name="ho_ten" value="<?= htmlspecialchars($row['ho_ten']); ?>" class="settings-input">
                                </div>

                                <div class="settings-form-group">
                                    <label>Giới tính</label>
                                    <select name="gioi_tinh" class="settings-select">
                                        <option value="Nam" <?= $row['gioi_tinh']=='Nam'?'selected':''; ?>>Nam giới</option>
                                        <option value="Nữ" <?= $row['gioi_tinh']=='Nữ'?'selected':''; ?>>Nữ giới</option>
                                        <option value="Khác" <?= $row['gioi_tinh']=='Khác'?'selected':''; ?>>Khác</option>
                                    </select>
                                </div>

                                <div class="settings-form-group">
                                    <label>Ngày sinh</label>
                                    <div class="settings-date-group">
                                        <input class="settings-select" type="number" name="ngay" value="<?= $ngay ?>" min="1" max="31" placeholder="Ngày">
                                        <select name="thang" class="settings-select">
                                            <?php for($i=1;$i<=12;$i++): ?>
                                                <option value="<?= $i ?>" <?= $thang==$i?'selected':''; ?>>Tháng <?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                        <input class="settings-select" type="number" name="nam" value="<?= $nam ?>" min="1900" max="<?= date('Y') ?>" placeholder="Năm">
                                    </div>
                                </div>

                                <div class="settings-form-group">
                                    <label>Địa chỉ</label>
                                    <input type="text" name="dia_chi" value="<?= htmlspecialchars($row['dia_chi']); ?>" class="settings-input">
                                </div>

                                <div class="settings-button-group">
                                    <button type="button" class="settings-btn settings-btn-secondary">Có thể sau này</button>
                                    <button type="submit" class="settings-btn settings-btn-primary">Lưu</button>
                                </div>
                            </div>
                        </form>

                        <!-- Email -->
                        <div class="settings-section">
                            <h2>E-mail</h2>
                            <div class="settings-email-box">
                                <div><?= htmlspecialchars($row['email']); ?></div>
                                <div class="settings-verified">Người nhận thông báo</div>
                            </div>
                            <button class="settings-btn settings-link-btn">+ Chỉnh sửa Email</button>
                        </div>

                        <!-- SĐT -->
                        <div class="settings-section">
                            <h2>Số điện thoại</h2>
                            <div class="settings-phone-box"><?= htmlspecialchars($row['sdt']); ?></div>
                            <button class="settings-btn settings-link-btn">+ Chỉnh sửa số điện thoại</button>
                        </div>

                    </div>

                    <!-- Tab Mật khẩu & Bảo mật -->
                    <form method="POST" action="../../php/UsersController/change_password.php?id=<?= $_SESSION['user_id'] ?>" class="security-section" style="display:none;">
                        <div class="settings-section">
                            <h2>Mật khẩu & Bảo mật</h2>

                            <div class="settings-form-group">
                                <label>Mật khẩu hiện tại</label>
                                <input type="password" name="pwht" class="settings-input" required>
                            </div>

                            <div class="settings-form-group">
                                <label>Mật khẩu mới</label>
                                <input type="password" name="pwmoi" class="settings-input" required>
                            </div>

                            <div class="settings-form-group">
                                <label>Xác nhận mật khẩu mới</label>
                                <input type="password" name="xn_pwmoi" class="settings-input" required>
                            </div>

                            <div class="settings-button-group">
                                <button type="button" onclick="location.reload()" class="settings-btn settings-btn-secondary">Hủy</button>
                                <button type="submit" class="settings-btn settings-btn-primary">Lưu thay đổi</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>
</div>

<script>
const tabs = document.querySelectorAll('.settings-tab');
const accountSection = document.querySelector('.account-section');
const securitySection = document.querySelector('.security-section');

// Chuyển tab
tabs.forEach((tab, index) => {
    tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active-tab'));
        tab.classList.add('active-tab');

        if(index === 0){
            accountSection.style.display = 'block';
            securitySection.style.display = 'none';
        }else{
            accountSection.style.display = 'none';
            securitySection.style.display = 'block';
        }
    });
});
setTimeout(function() {
    const thongbao = document.querySelector('.thongbao');
    if(thongbao) {
        thongbao.style.display = 'none';
    }
}, 5000);

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

      fetch('../../php/UsersController/upload_avatar.php', {
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
</script>

</body>
</html>

<?php 
} else {
    echo "Không tìm thấy thông tin người dùng!";
}
?>
