<?php
session_start();
include('../../db/db.php');

if (!isset($_SESSION['id'])) {
    header("Location: ../../login.php");
    exit();
}

$staff_id = $_SESSION['id'];

// Lấy lịch tour của nhân viên
$sql = "SELECT * FROM tour_schedule WHERE staff_id = ? ORDER BY start_date DESC";
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
  <title>Lịch Tour Của Tôi</title>
  <link rel="stylesheet" href="../../css/Staff.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    body {
      background-color: #f4f6f8;
      font-family: "Segoe UI", sans-serif;
    }
    .main-content {
      margin-left: 250px;
      padding: 40px;
    }
    .main-title {
      font-size: 28px;
      font-weight: 600;
      color: #91aecaff;
      margin-bottom: 25px;
      border-left: 5px solid #007bff;
      padding-left: 10px;
    }
    .table {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    thead th {
      background-color: #1e3d59 !important;
      color: white;
      text-transform: uppercase;
      font-size: 15px;
    }
    tbody tr:hover {
      background-color: #f0f4ff;
      transition: 0.2s;
    }
    .badge {
      font-size: 13px;
      padding: 6px 10px;
      border-radius: 8px;
    }
    .note {
      color: #6c757d;
      font-style: italic;
    }
  </style>
</head>
<body>

  <?php include('menu.php'); ?>

  <div class="main-content">
    <h2 class="main-title">🧭 Lịch Tour Của Tôi</h2>

    <div class="table-responsive">
      <table class="table table-bordered text-center align-middle">
        <thead>
          <tr>
            <th>Mã Tour</th>
            <th>Tên Tour</th>
            <th>Ngày khởi hành</th>
            <th>Ngày kết thúc</th>
            <th>Địa điểm</th>
            <th>Trạng thái</th>
            <th>Ghi chú</th>
          </tr>
        </thead>
        <tbody>
          <?php if($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <?php
                $status = $row['status'];
                $badge_class = 'bg-info text-dark';
                $status_text = 'Đang chuẩn bị';
                
                switch($status) {
                  case 'completed':
                    $badge_class = 'bg-success';
                    $status_text = 'Hoàn thành';
                    break;
                  case 'upcoming':
                    $badge_class = 'bg-warning text-dark';
                    $status_text = 'Sắp khởi hành';
                    break;
                  case 'cancelled':
                    $badge_class = 'bg-danger';
                    $status_text = 'Đã hủy';
                    break;
                }
              ?>
              <tr>
                <td><strong><?php echo htmlspecialchars($row['tour_code']); ?></strong></td>
                <td><?php echo htmlspecialchars($row['tour_name']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['start_date'])); ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['end_date'])); ?></td>
                <td><?php echo htmlspecialchars($row['location']); ?></td>
                <td><span class="badge <?php echo $badge_class; ?>"><?php echo $status_text; ?></span></td>
                <td class="note"><?php echo htmlspecialchars($row['notes']); ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-muted">Chưa có lịch tour nào</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>