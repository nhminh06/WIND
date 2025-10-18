<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ca làm việc</title>
  <link rel="stylesheet" href="../../css/Staff.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    /* Khung chính khi có sidebar */
    .main-content {
      margin-left: 250px; /* Để không bị sidebar đè */
      padding: 40px;
      transition: 0.3s;
    }

    .main-title {
      font-size: 28px;
      font-weight: 600;
      margin-bottom: 25px;
      color: #91aecaff;
      border-left: 5px solid #007bff;
      padding-left: 10px;
    }

    /* Bảng dữ liệu */
    .table {
      width: 100%;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
      background-color: white;
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

    td, th {
      vertical-align: middle;
    }
  </style>
</head>
<body>
  <?php include('menu.php'); ?>

  <div class="main-content">
    <h2 class="main-title">📅 Ca Làm Việc Của Bạn</h2>

    <table class="table table-bordered table-striped text-center align-middle">
      <thead>
        <tr>
          <th>Ngày</th>
          <th>Tour</th>
          <th>Giờ khởi hành</th>
          <th>Giờ kết thúc</th>
          <th>Ghi chú</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>18/10</td>
          <td>Đà Nẵng - Hội An</td>
          <td>06:00</td>
          <td>21:30</td>
          <td>Tour tốt, khách hài lòng</td>
        </tr>
      </tbody>
    </table>
  </div>
</body>
</html>
