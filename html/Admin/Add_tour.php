<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <?php session_start(); ?>
      <aside class="sidebar">
    <h2 class="logo">WIND Admin</h2>
   <?php include '../../includes/Adminnav.php';?>
  </aside>

  <!-- Main -->
  <div class="main">
    <!-- Header -->
    <header class="header">
      <h1>Bảng điều khiển</h1>
      <div class="admin-info">
       <?php 
       echo "<p>Xin chào  " . $_SESSION['username'] . "</p>";
       ?>
        <button onclick="window.location.href='../views/index/webindex.php'" class="logout">Trở lại</button>
      </div>
    </header>

    <!-- Content -->
    <section class="content">
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
        <option value="1">Trong ngày</option>
        <option value="2">Dài ngày</option>
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
    <div class="col-md-12">
      <label class="form-label">Vị trí trực thuộc</label>
      <input name="vitri" class="form-control">
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
  </div>
</body>
</html>