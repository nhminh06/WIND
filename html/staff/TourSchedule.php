<?php
session_start();
include('menu.php'); 
include('../../db/db.php');

// FIX: Đổi từ user_id sang id để thống nhất
if (!isset($_SESSION['user_id'])) {
  header("Location: ../../login.php");
  exit();
}

$staff_id = $_SESSION['user_id']; // ✅ Thống nhất dùng user_id

// Lấy danh sách tour của nhân viên hiện tại
$sql = "SELECT 
          ts.id,
          ts.tour_code,
          ts.tour_name,
          ts.start_date,
          ts.end_date,
          ts.location,
          ts.status,
          ts.notes
        FROM tour_schedule ts
        WHERE ts.staff_id = ?
        ORDER BY ts.start_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();

// Hàm chuyển đổi trạng thái sang tiếng Việt và badge
function getStatusBadge($status) {
  $badges = [
    'preparing' => ['text' => 'Đang chuẩn bị', 'class' => 'bg-info text-dark'],
    'upcoming' => ['text' => 'Sắp khởi hành', 'class' => 'bg-warning text-dark'],
    'completed' => ['text' => 'Hoàn thành', 'class' => 'bg-success'],
    'cancelled' => ['text' => 'Đã hủy', 'class' => 'bg-danger']
  ];
  return $badges[$status] ?? ['text' => $status, 'class' => 'bg-secondary'];
}

// Hàm format ngày
function formatDate($date) {
  return date('d/m/Y', strtotime($date));
}
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
    .empty-state {
      text-align: center;
      padding: 40px;
      color: #6c757d;
    }
  </style>
</head>
<body>
  <div class="main-content">
    <h2 class="main-title">🧭 Lịch Tour Của Tôi</h2>

    <?php if ($result->num_rows > 0): ?>
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
          <?php while ($tour = $result->fetch_assoc()): 
            $badge = getStatusBadge($tour['status']);
          ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($tour['tour_code']); ?></strong></td>
            <td><?php echo htmlspecialchars($tour['tour_name']); ?></td>
            <td><?php echo formatDate($tour['start_date']); ?></td>
            <td><?php echo formatDate($tour['end_date']); ?></td>
            <td><?php echo htmlspecialchars($tour['location']); ?></td>
            <td>
              <span class="badge <?php echo $badge['class']; ?>">
                <?php echo $badge['text']; ?>
              </span>
            </td>
            <td class="note">
              <?php echo htmlspecialchars($tour['notes'] ?? 'Không có ghi chú'); ?>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
      <div>📅</div>
      <h4>Chưa có lịch tour nào</h4>
      <p>Hiện tại bạn chưa được phân công tour nào.</p>
    </div>
    <?php endif; ?>
  </div>

  <?php
  $stmt->close();
  $conn->close();
  ?>
</body>
</html>