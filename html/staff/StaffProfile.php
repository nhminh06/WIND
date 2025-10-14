<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <link rel="stylesheet" href="../../../WIND//css/Staff.css">
  <title>Hồ sơ nhân viên</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
  <?php include('Menu.php'); ?> <!-- Menu trái tái sử dụng -->
  
  <div class="content flex-grow-1 p-4">
    <h2 class="text-primary mb-4">Hồ sơ nhân viên</h2>
    <div class="card shadow p-4" style="max-width: 600px;">
      <div class="text-center mb-3">
        <img src="../../../WIND/img/about.png" alt="Avatar" class="rounded-circle" style="width:100px;">
      </div>
      <form>
        <div class="mb-3">
          <label class="form-label">Họ và tên</label>
          <input type="text" class="form-control" value="Nguyễn Văn A">
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" value="a.nguyen@travel.com">
        </div>
        <div class="mb-3">
          <label class="form-label">Chức vụ</label>
          <input type="text" class="form-control" value="Hướng dẫn viên">
        </div>
        <div class="mb-3">
          <label class="form-label">Kinh nghiệm</label>
          <textarea class="form-control" rows="3">3 năm dẫn tour trong và ngoài nước.</textarea>
        </div>
        <button class="btn btn-success w-100">Lưu thay đổi</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
