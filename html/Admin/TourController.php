<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Thêm Tour</title>

  <!-- CSS -->
  <link rel="stylesheet" href="../../css/Admin.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <style>
    /* 🎨 Chỉ đổi màu vùng form */
    .tour-form {
      background: linear-gradient(135deg, #f9fafc, #eef3ff);
      border: 2px solid #d8e3ff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
    }
   
 th img{
  width: 200px;
 }
    .tour-form .form-label {
      font-weight: 600;
      color: #1e3a8a;
    }

    .tour-form input,
    .tour-form select,
    .tour-form textarea {
      border: 1px solid #b0c4ff;
      box-shadow: none;
    }

    .tour-form input:focus,
    .tour-form select:focus,
    .tour-form textarea:focus {
      border-color: #4f46e5;
      box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }

    .tour-form button {
      border-radius: 8px;
      font-weight: 600;
    }
    .sidebar .menu {
  padding: 0 !important;
  margin: 0 !important;
}


    .btn-primary {
      background-color: #4f46e5;
      border-color: #4f46e5;
    }

    .btn-primary:hover {
      background-color: #4338ca;
      border-color: #4338ca;
    }

    .form-text {
      color: #6b7280;
      font-size: 0.9em;
    }

    .header {
      background: #f8fafc;
      border-bottom: 3px solid #3b82f6;
      color: #111827;
      font-weight: 700;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .header h1 {
      color: #1e40af;
      font-weight: 700;
    }
    

    .header .admin-info p {
      color: #1e293b;
      font-weight: 600;
    }
  </style>
</head>
<body>
  <?php session_start(); ?>

  <!-- Sidebar -->
  <aside class="sidebar">
  <h2 class="logo">WIND Admin</h2>
  <?php include '../../includes/Adminnav.php';?>
</aside>

<div class="main">
  <header class="header">
    <h1>Bảng điều khiển</h1>
    <div class="admin-info">
      <?php echo "<p>Xin chào " . $_SESSION['username'] . "</p>"; ?>
      <button onclick="window.location.href='../views/user/users.php'" class="logout">Đăng xuất</button>
    </div>
  </header>

    <!-- Content -->
    <section class="content p-4">
      <div class="container tour-form">
        <form action="../../php/TourCTL/AddTour.php" method="POST" enctype="multipart/form-data">
  <div class="row g-3">

    <div class="col-md-6">
      <label for="maTour" class="form-label">Mã Tour</label>
      <input type="text" class="form-control" id="maTour" name="maTour" placeholder="VD: VN001">
    </div>

    <div class="col-md-6">
      <label for="tenTour" class="form-label">Tên Tour</label>
      <input type="text" class="form-control" id="tenTour" name="tenTour" placeholder="VD: Hành trình miền Trung">
    </div>

    <div class="col-md-6">
      <label for="loaiTour" class="form-label">Loại Tour</label>
      <select id="loaiTour" name="loaiTour" class="form-select">
        <option selected disabled>-- Chọn loại tour --</option>
        <option value="1">Trong nước</option>
        <option value="2">Nước ngoài</option>
      </select>
    </div>

    <div class="col-md-6">
      <label for="ngayKhoiHanh" class="form-label">Ngày khởi hành</label>
      <input type="date" class="form-control" id="ngayKhoiHanh" name="ngayKhoiHanh">
    </div>

    <div class="col-md-6">
      <label for="diemKhoiHanh" class="form-label">Điểm khởi hành</label>
      <input type="text" class="form-control" id="diemKhoiHanh" name="diemKhoiHanh" placeholder="VD: Hà Nội, TP. Hồ Chí Minh...">
    </div>

    <div class="col-md-6">
      <label for="soNgay" class="form-label">Số ngày</label>
      <input type="number" class="form-control" id="soNgay" name="soNgay" placeholder="VD: 5">
    </div>

    <!-- ✅ 3 ô nhập giá khác nhau -->
    <div class="col-md-4">
      <label for="giaNguoiLon" class="form-label">Giá người lớn (VNĐ)</label>
      <input type="number" class="form-control" id="giaNguoiLon" name="giaNguoiLon" placeholder="VD: 4500000">
    </div>

    <div class="col-md-4">
      <label for="giaTreEm" class="form-label">Giá trẻ em (2 - 9 tuổi)</label>
      <input type="number" class="form-control" id="giaTreEm" name="giaTreEm" placeholder="VD: 3000000">
    </div>

    <div class="col-md-4">
      <label for="giaTreNho" class="form-label">Giá trẻ nhỏ (&lt; 2 tuổi)</label>
      <input type="number" class="form-control" id="giaTreNho" name="giaTreNho" placeholder="VD: 1000000">
    </div>

    <div class="col-md-6">
      <label for="anhDaiDien" class="form-label">Ảnh đại diện</label>
      <input type="file" class="form-control" id="anhDaiDien" name="anhDaiDien" accept="image/*">
    </div>

    <div class="col-md-6">
      <label for="banner" class="form-label">Ảnh Banner (tối đa 5 ảnh)</label>
      <input type="file" class="form-control" id="banner" name="banner[]" accept="image/*" multiple>
      <div class="form-text">Giữ Ctrl (hoặc Cmd) để chọn nhiều ảnh.</div>
    </div>

  

    <div class="col-12">
      <label for="dichVu" class="form-label">Dịch vụ</label>
      <textarea class="form-control" id="dichVu" name="dichVu" rows="3" placeholder="Liệt kê các dịch vụ đi kèm tour..."></textarea>
    </div>

    <div class="col-12">
      <label for="loTrinh" class="form-label">Lộ trình chi tiết</label>
      <textarea class="form-control" id="loTrinh" name="loTrinh" rows="3" placeholder="Nhập các điểm đến và hoạt động từng ngày..."></textarea>
    </div>

    <div class="col-12">
      <label for="traiNghiem" class="form-label">Trải nghiệm nổi bật</label>
      <textarea class="form-control" id="traiNghiem" name="traiNghiem" rows="3" placeholder="Những trải nghiệm đặc biệt du khách sẽ có..."></textarea>
    </div>

    <div class="col-12 text-end mt-3">
      <button type="reset" class="btn btn-secondary me-2">Hủy</button>
      <button type="submit" class="btn btn-primary">Lưu Tour</button>
    </div>
  </div>
</form>

      </div>
    </section>

    <section class="content p-4">
  <div class="container mt-4">
    <h2 class="mb-3 text-primary fw-bold">Danh sách Tour hiện có</h2>
    <div class="table-responsive shadow-sm rounded-3">
      <table class="table table-hover align-middle" id="tourTable">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th>Hình ảnh</th>
            <th>Tên Tour</th>
            <th>Số ngày</th>
            <th>Giá (VNĐ)</th>
            <th class="text-center">Hành động</th>
          </tr>
          <?php
          include '../../db/db.php';
          $sql_hienthitour = "SELECT * FROM tour";
          $result_hienthi = mysqli_query($conn, $sql_hienthitour);
          while ($row_ht = mysqli_fetch_assoc($result_hienthi)) {
          ?> 
           <tr>
            <th></th>
            <th><img src="<?php echo "../../uploads/" . $row_ht['hinh_anh'] ?>" alt=""></th>
            <th><?php echo $row_ht['ten_tour']; ?></th>
            <th><?php echo $row_ht['so_ngay']; ?></th>
            <th><?php echo $row_ht['gia']; ?></th>
            <th class="text-center">
              <button onclick="window.location.href='../../php/tourCTL/UDtour.php?id=<?php echo $row_ht['id']; ?>'" class="btn btn-warning"><i class="bi bi-pen-fill"></i></button>
           <button onclick="confirmDelete(<?php echo $row_ht['id']; ?>, '<?php echo addslashes($row_ht['ten_tour']); ?>')" class="btn btn-danger"><i class="bi bi-trash3-fill"></i></button>
           <button  onclick="window.location.href='../../php/tourCTL/Hiddentour.php?id=<?php echo $row_ht['id']; ?>'" class="btn btn-info"><?php 
if ($row_ht['trang_thai'] == 1) {
    echo '<i class="bi bi-eye-fill"></i>'; 
} else {
    echo '<i class="bi bi-eye-slash-fill"></i>'; 
}
?>
</button>
            </th>
          </tr>
          <?php } ?>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</section>

  </div>

  <script>
    document.getElementById('banner').addEventListener('change', function() {
      if (this.files.length > 5) {
        alert('Bạn chỉ được chọn tối đa 5 ảnh banner!');
        this.value = '';
      }
    });
  </script>
<script>
function confirmDelete(tourId, tourName) {
    if (confirm('Bạn có chắc muốn xóa tour "' + tourName + '" không?')) {
        window.location.href = '../../php/tourCTL/DLTtour.php?id=' + tourId;
    }
}
</script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
</body>
</html>
