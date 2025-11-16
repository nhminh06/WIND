<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt tài khoản</title>
    <link rel="stylesheet" href="../css/Main5.css">
    <style>
        /* CSS cho các nút */
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .actions button.gray {
            background-color: #3dcce2ff;
            color: white;
        }

        .actions button.gray:hover {
            background-color: #03bfccff;
        }

        .email-section, .phone-section, .form-section {
            margin-top: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .form-section h2, .email-section h2, .phone-section h2 {
            margin-bottom: 15px;
            color: #333;
        }

        .form-section label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }

        .form-section input, .form-section select,
        .email-section input, .phone-section input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }

        .birthdate {
            display: flex;
            gap: 10px;
        }

        .birthdate input, .birthdate select {
            flex: 1;
        }

        .email-item {
            background: #f1f1f1;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
        }

        .notify-tag {
            background: #dff0d8;
            color: green;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
        }

        .security-section {
            padding: 20px;
            background-color: #ffffffff;
            border-radius: 8px;
        }

        .security-section h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .security-section label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }

        .security-section input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }

        .security-section .save {
            background-color: #4ecbedff;
            color: white;
        }

        .security-section .save:hover {
            background-color: #04b0bcff;
        }
    </style>
</head>

<body>
  <?php 
  session_start();
  include '../../../db/db.php';
  $sql = "SELECT * FROM user WHERE id = ".$_SESSION['user_id'];
  $result = mysqli_query($conn, $sql);
  if(mysqli_num_rows($result) > 0){
    $row = mysqli_fetch_assoc($result);

    $_SESSION['avatar'] = $row['avatar'];
    
    // Xử lý ngày sinh
    $ngay_sinh = $row['ngay_sinh'] ?? '2000-01-01';
    list($nam, $thang, $ngay) = explode('-', $ngay_sinh);
    $thang = (int)$thang;
    $ngay = (int)$ngay;
  ?>

  <h1>Cài đặt</h1>
  <div class="tabs11">
    <span class="active-tab" id="tabAccount">Thông tin tài khoản</span>
    <span class="scrt" id="tabSecurity">Mật khẩu & Bảo mật</span>
  </div>

  <!-- Form chính - Gửi tất cả dữ liệu một lần -->
  <form action="../../../php/UsersController/EditUser.php?id=<?php echo $_SESSION['user_id']; ?>" method="POST" class="personal-data-form" id="accountSection">
    
    <!-- Dữ liệu cá nhân -->
    <div class="form-section">
      <h2>Dữ liệu cá nhân</h2>
      
      <label>Họ và tên đầy đủ</label>
      <input type="text" name="ho_ten" value="<?php echo htmlspecialchars($row['ho_ten']); ?>" />

      <label>Giới tính</label>
      <select name="gioi_tinh" class="settings-select">
        <option value="Nam" <?= $row['gioi_tinh']=='Nam'?'selected':''; ?>>Nam giới</option>
        <option value="Nữ" <?= $row['gioi_tinh']=='Nữ'?'selected':''; ?>>Nữ giới</option>
        <option value="Khác" <?= $row['gioi_tinh']=='Khác'?'selected':''; ?>>Khác</option>
      </select>

      <label>Ngày sinh</label>
      <div class="birthdate">
        <input type="number" name="ngay" value="<?= $ngay ?>" min="1" max="31" />
        
        <select name="thang">
          <option value="1" <?= $thang == 1 ? 'selected' : '' ?>>Tháng một</option>
          <option value="2" <?= $thang == 2 ? 'selected' : '' ?>>Tháng hai</option>
          <option value="3" <?= $thang == 3 ? 'selected' : '' ?>>Tháng ba</option>
          <option value="4" <?= $thang == 4 ? 'selected' : '' ?>>Tháng tư</option>
          <option value="5" <?= $thang == 5 ? 'selected' : '' ?>>Tháng năm</option>
          <option value="6" <?= $thang == 6 ? 'selected' : '' ?>>Tháng sáu</option>
          <option value="7" <?= $thang == 7 ? 'selected' : '' ?>>Tháng bảy</option>
          <option value="8" <?= $thang == 8 ? 'selected' : '' ?>>Tháng tám</option>
          <option value="9" <?= $thang == 9 ? 'selected' : '' ?>>Tháng chín</option>
          <option value="10" <?= $thang == 10 ? 'selected' : '' ?>>Tháng mười</option>
          <option value="11" <?= $thang == 11 ? 'selected' : '' ?>>Tháng mười một</option>
          <option value="12" <?= $thang == 12 ? 'selected' : '' ?>>Tháng mười hai</option>
        </select>
        
        <input type="number" name="nam" value="<?= $nam ?>" min="1900" max="<?= date('Y') ?>" />
      </div>

      <label>Địa chỉ</label>
      <input type="text" name="dia_chi" value="<?php echo htmlspecialchars($row['dia_chi']); ?>" placeholder="Địa chỉ" />
      <div class="actions">
      <button type="submit" class="gray">Lưu tất cả thông tin</button>
    </div>
    </div>
     

    <!-- Email -->
    <div class="email-section">
      <h2>E-mail</h2>
      <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" />
    </div>

    <!-- Số điện thoại -->
    <div class="phone-section">
      <h2>Số điện thoại di động</h2>

      <input type="text" name="sdt" value="<?php echo htmlspecialchars($row['sdt']); ?>" />
    </div>

    <!-- Nút Lưu cho toàn bộ form -->
   
  </form>

  <!-- Form Mật khẩu -->
  <form method="POST" class="security-section" id="securitySection" style="display:none;" action="../../../php/UsersController/change_password.php?id=<?php echo $_SESSION['user_id']; ?>">
    <h2>Mật khẩu & Bảo mật</h2>

    <label for="currentPassword">Mật khẩu hiện tại</label>
    <input type="password" id="currentPassword" placeholder="Nhập mật khẩu hiện tại" name="pwht" required>

    <label for="newPassword">Mật khẩu mới</label>
    <input type="password" id="newPassword" placeholder="Nhập mật khẩu mới" name="pwmoi" required>

    <label for="confirmPassword">Xác nhận mật khẩu mới</label>
    <input type="password" id="confirmPassword" placeholder="Nhập lại mật khẩu mới" name="xn_pwmoi" required>

    <div class="actions">
      <button type="button" class="gray" onclick="location.reload()" style="background-color: #ccc;">Hủy</button>
      <button class="save" type="submit">Lưu thay đổi</button>
    </div>
  </form>
  <?php } ?>
  

  <script src="../js/Main5.js"></script>
  
  <script>
 

    // Ẩn thông báo sau 5 giây
    setTimeout(function() {
      const thongbao = document.querySelector('.thongbao');
      if(thongbao) {
        thongbao.style.display = 'none';
      }
    }, 5000);
  </script>

</body>
</html>