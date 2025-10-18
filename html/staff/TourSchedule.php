<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lịch Tour Của Tôi</title>
  <link rel="stylesheet" href="../../../WIND/css/Staff.css">
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
        <tr>
          <td>T001</td>
          <td>Đà Nẵng - Hội An</td>
          <td>18/10/2025</td>
          <td>19/10/2025</td>
          <td>Quảng Nam</td>
          <td><span class="badge bg-success">Hoàn thành</span></td>
          <td class="note">Khách hài lòng, tour diễn ra suôn sẻ</td>
        </tr>

        <tr>
          <td>T002</td>
          <td>Huế - Bà Nà Hills</td>
          <td>22/10/2025</td>
          <td>23/10/2025</td>
          <td>Huế, Đà Nẵng</td>
          <td><span class="badge bg-warning text-dark">Sắp khởi hành</span></td>
          <td class="note">Chuẩn bị hồ sơ khách và phương tiện</td>
        </tr>

        <tr>
          <td>T003</td>
          <td>Đà Lạt 3 Ngày 2 Đêm</td>
          <td>28/10/2025</td>
          <td>30/10/2025</td>
          <td>Lâm Đồng</td>
          <td><span class="badge bg-info text-dark">Đang chuẩn bị</span></td>
          <td class="note">Liên hệ khách sạn & đặt vé cáp treo</td>
        </tr>
      </tbody>
    </table>
  </div>
</body>
</html>
