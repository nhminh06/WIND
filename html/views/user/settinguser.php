<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cài đặt tài khoản</title>
    <link rel="stylesheet" href="../css/Main5.css">
     
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
        <span class="active-tab">Thông tin tài khoản</span>
        <span class="scrt">Mật khẩu & Bảo mật</span>
      </div>

      <div class="form-section">
        <h2>Dữ liệu cá nhân</h2>
        <label>Họ và tên đầy đủ</label>
        <input type="text" value="<?php echo htmlspecialchars($row['ho_ten']); ?>" />

        <label>Giới tính</label>
        <select name="gender">
            <option value="male" <?php echo ($row['gioi_tinh'] == 'Nam') ? 'selected' : ''; ?>>Nam giới</option>
            <option value="female" <?php echo ($row['gioi_tinh'] == 'Nữ') ? 'selected' : ''; ?>>Nữ giới</option>
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
        <input value="<?php echo $row['dia_chi'] ?>" type="text" placeholder="Địa chỉ" disabled />

        <div class="actions">
          <button disabled class="gray">Có thể sau này</button>
          <button disabled class="gray">Cứu</button>
        </div>
      </div>

      <div class="email-section">
        <h2>E-mail</h2>
        <div class="email-item">
          <span><?php echo htmlspecialchars($row['email']); ?></span>
          <span class="notify-tag">Người nhận thông báo</span>
        </div>
        <button class="add-btn">+ Chỉnh sửa Email</button>
      </div>

      <div class="phone-section">
        <h2>Số điện thoại di động</h2>
        <span style="background-color: #dddddd;padding: 5px;margin: 10px 10px 10px 0;display: inline-block;border-radius: 4px;"><?php echo htmlspecialchars($row['sdt']); ?></span> <br>
        <button class="add-btn">+ Chỉnh sửa số điện thoại di động</button>
      </div>

   <form method="POST" class="security-section" style="display:none;" action="../../../php/UsersController/change_password.php?id=<?php echo $_SESSION['user_id']; ?>">
  <h2>Mật khẩu & Bảo mật</h2>

  <label for="currentPassword">Mật khẩu hiện tại</label>
  <input type="password" id="currentPassword" placeholder="Nhập mật khẩu hiện tại" name="pwht">

  <label for="newPassword">Mật khẩu mới</label>
  <input type="password" id="newPassword" placeholder="Nhập mật khẩu mới" name="pwmoi">

  <label for="confirmPassword">Xác nhận mật khẩu mới</label>
  <input type="password" id="confirmPassword" placeholder="Nhập lại mật khẩu mới" name="xn_pwmoi">

  <div class="actions">
    <button class="gray">Hủy</button>
    <button class="save" type="submit">Lưu thay đổi</button>
  </div>
</form>

<?php 
  }
?>
  <script src="../js/Main5.js"></script>
 

</body>
</html>