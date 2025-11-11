<?php
session_start();
include('../../db/db.php');

if (!isset($_SESSION['id'])) {
    header("Location: ../../login.php");
    exit();
}

$staff_id = $_SESSION['id'];

// Lấy lịch sử báo nghỉ
$sql = "SELECT * FROM leave_requests WHERE staff_id = ? ORDER BY request_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
?>
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
    .alert-fixed {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      min-width: 300px;
    }
  </style>
</head>
<body>
  <?php include('menu.php'); ?>

  <div class="main-content">
    <h2 class="main-title">🩺 Báo Nghỉ / Xin Nghỉ Phép</h2>

    <?php if(isset($_SESSION['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show alert-fixed" role="alert">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show alert-fixed" role="alert">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="card p-4 mb-4">
      <h5 class="mb-3 text-primary">📝 Gửi đơn xin nghỉ</h5>
      <form action="SendLeave.php" method="POST" onsubmit="return validateForm()">
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
            <input type="date" name="start_date" id="start_date" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Đến ngày:</label>
            <input type="date" name="end_date" id="end_date" class="form-control" required>
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
      <div class="table-responsive">
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
            <?php if($result->num_rows > 0): ?>
              <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?php echo date('d/m/Y', strtotime($row['request_date'])); ?></td>
                  <td><?php echo htmlspecialchars($row['leave_type']); ?></td>
                  <td><?php echo date('d/m/Y', strtotime($row['start_date'])); ?></td>
                  <td><?php echo date('d/m/Y', strtotime($row['end_date'])); ?></td>
                  <td><?php echo htmlspecialchars($row['reason']); ?></td>
                  <td>
                    <?php 
                      $status = $row['status'];
                      $badge_class = 'bg-warning text-dark';
                      $status_text = 'Đang chờ';
                      
                      if($status == 'approved') {
                        $badge_class = 'bg-success';
                        $status_text = 'Đã duyệt';
                      } elseif($status == 'rejected') {
                        $badge_class = 'bg-danger';
                        $status_text = 'Từ chối';
                      }
                    ?>
                    <span class="badge <?php echo $badge_class; ?>"><?php echo $status_text; ?></span>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-muted">Chưa có đơn xin nghỉ nào</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    function validateForm() {
      const startDate = new Date(document.getElementById('start_date').value);
      const endDate = new Date(document.getElementById('end_date').value);
      const today = new Date();
      today.setHours(0, 0, 0, 0);

      if (startDate < today) {
        alert('Ngày bắt đầu không được ở quá khứ!');
        return false;
      }

      if (endDate < startDate) {
        alert('Ngày kết thúc phải sau ngày bắt đầu!');
        return false;
      }

      return true;
    }

    // Auto hide alerts
    setTimeout(function() {
      const alerts = document.querySelectorAll('.alert-fixed');
      alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      });
    }, 5000);
  </script>
</body>
</html>