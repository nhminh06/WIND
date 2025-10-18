<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Báo nghỉ</title>
  <link rel="stylesheet" href="../../css/Staff.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    .main-content {
      margin-left: 250px;
      padding: 40px;
    }
    .main-title {
      font-size: 28px;
      font-weight: 600;
      margin-bottom: 25px;
      color: #91aecaff;
      border-left: 5px solid #007bff;
      padding-left: 10px;
    }
    .card {
      border-radius: 10px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    .table th {
      background-color: #1e3d59;
      color: #fff;
      text-transform: uppercase;
    }
  </style>
</head>
<body>
  <?php include('menu.php'); ?>

  <div class="main-content">
    <h2 class="main-title">🩺 Báo Nghỉ / Xin Nghỉ Phép</h2>

    <div class="card p-4 mb-4">
      <h5 class="mb-3 text-primary">📝 Gửi đơn xin nghỉ</h5>
      <form action="SendLeave.php" method="POST">
        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Loại nghỉ:</label>
            <select name="leave_type" class="form-select" required>
              <option value="">-- Chọn loại nghỉ --</option>
              <option value="Nghỉ ốm">Nghỉ ốm</option>
              <option value="Nghỉ phép">Nghỉ phép</option>
              <option value="Nghỉ việc riêng">Nghỉ việc riêng</option>
              <option value="Khác">Khác</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Từ ngày:</label>
            <input type="date" name="start_date" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Đến ngày:</label>
            <input type="date" name="end_date" class="form-control" required>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Lý do nghỉ:</label>
          <textarea name="reason" class="form-control" rows="3" placeholder="Nhập lý do cụ thể..." required></textarea>
        </div>

        <div class="text-end">
          <button type="submit" class="btn btn-primary px-4">Gửi yêu cầu</button>
        </div>
      </form>
    </div>

    <div class="card p-4">
      <h5 class="mb-3 text-primary">📋 Lịch sử báo nghỉ</h5>
      <table class="table table-bordered text-center align-middle">
        <thead>
          <tr>
            <th>Ngày gửi</th>
            <th>Loại nghỉ</th>
            <th>Từ ngày</th>
            <th>Đến ngày</th>
            <th>Lý do</th>
            <th>Trạng thái</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>18/10/2025</td>
            <td>Nghỉ ốm</td>
            <td>18/10</td>
            <td>19/10</td>
            <td>Bị cảm nhẹ</td>
            <td><span class="badge bg-warning text-dark">Đang chờ</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
