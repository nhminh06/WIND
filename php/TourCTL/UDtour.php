<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa tour</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body{
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f0f4f8;
        }
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
    </style>
</head>
<body>
    <?php
    include "../../db/db.php";
    $tour_id = $_GET['id'];
    
    // Lấy thông tin tour chính
    $sql_tour = "SELECT t.*, tc.ma_tour, tc.diem_khoi_hanh, lkh.ngay_khoi_hanh, 
                 lkh.gia_nguoi_lon, lkh.gia_tre_em, lkh.gia_tre_nho
                 FROM tour t
                 LEFT JOIN tour_chi_tiet tc ON t.id = tc.tour_id
                 LEFT JOIN lich_khoi_hanh lkh ON t.id = lkh.tour_id
                 WHERE t.id = '$tour_id'
                 LIMIT 1";
    $result_tour = mysqli_query($conn, $sql_tour);
    $tour = mysqli_fetch_assoc($result_tour);
    
    // Lấy tất cả dịch vụ
    $sql_dv = "SELECT ten_dich_vu FROM dich_vu WHERE tour_id = '$tour_id'";
    $result_dv = mysqli_query($conn, $sql_dv);
    $dichVuArr = [];
    while($row_dv = mysqli_fetch_assoc($result_dv)) {
        $dichVuArr[] = $row_dv['ten_dich_vu'];
    }
    $dichVu = implode("\n", $dichVuArr);
    
    // Lấy tất cả lộ trình
    $sql_lt = "SELECT noi_dung FROM lich_trinh WHERE tour_id = '$tour_id' ORDER BY ngay ASC";
    $result_lt = mysqli_query($conn, $sql_lt);
    $loTrinhArr = [];
    while($row_lt = mysqli_fetch_assoc($result_lt)) {
        $loTrinhArr[] = $row_lt['noi_dung'];
    }
    $loTrinh = implode("\n", $loTrinhArr);
    
    // Lấy tất cả trải nghiệm
    $sql_tn = "SELECT noi_dung FROM trai_nghiem WHERE tour_id = '$tour_id'";
    $result_tn = mysqli_query($conn, $sql_tn);
    $traiNghiemArr = [];
    while($row_tn = mysqli_fetch_assoc($result_tn)) {
        $traiNghiemArr[] = $row_tn['noi_dung'];
    }
    $traiNghiem = implode("\n", $traiNghiemArr);
    ?>
    
    <section class="content p-4">
        <div class="container tour-form">
            <h2 class="mb-4 text-primary">Cập nhật Tour</h2>
            <form action="UDController.php?id=<?php echo $tour['id']; ?>" method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="maTour" class="form-label">Mã Tour</label>
                        <input type="text" value="<?php echo $tour['ma_tour']; ?>" class="form-control" id="maTour" name="maTour" placeholder="VD: VN001">
                    </div>

                    <div class="col-md-6">
                        <label for="tenTour" class="form-label">Tên Tour</label>
                        <input type="text" value="<?php echo $tour['ten_tour']; ?>" class="form-control" id="tenTour" name="tenTour" placeholder="VD: Hành trình miền Trung">
                    </div>

                    <div class="col-md-6">
                        <label for="loaiTour" class="form-label">Loại Tour</label>
                        <select id="loaiTour" name="loaiTour" class="form-select">
                            <option selected disabled>-- Chọn loại tour --</option>
                            <option value="1" <?php echo ($tour['loai_banner'] == 1) ? 'selected' : ''; ?>>Trong nước</option>
                            <option value="2" <?php echo ($tour['loai_banner'] == 2) ? 'selected' : ''; ?>>Nước ngoài</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="ngayKhoiHanh" class="form-label">Ngày khởi hành</label>
                        <input type="date" value="<?php echo $tour['ngay_khoi_hanh']; ?>" class="form-control" id="ngayKhoiHanh" name="ngayKhoiHanh">
                    </div>

                    <div class="col-md-6">
                        <label for="diemKhoiHanh" class="form-label">Điểm khởi hành</label>
                        <input type="text" value="<?php echo $tour['diem_khoi_hanh']; ?>" class="form-control" id="diemKhoiHanh" name="diemKhoiHanh" placeholder="VD: Hà Nội, TP. Hồ Chí Minh...">
                    </div>

                    <div class="col-md-6">
                        <label for="soNgay" class="form-label">Số ngày</label>
                        <input type="number" value="<?php echo $tour['so_ngay']; ?>" class="form-control" id="soNgay" name="soNgay" placeholder="VD: 5">
                    </div>

                    <div class="col-md-4">
                        <label for="giaNguoiLon" class="form-label">Giá người lớn (VNĐ)</label>
                        <input type="number" value="<?php echo $tour['gia_nguoi_lon']; ?>" class="form-control" id="giaNguoiLon" name="giaNguoiLon" placeholder="VD: 4500000">
                    </div>

                    <div class="col-md-4">
                        <label for="giaTreEm" class="form-label">Giá trẻ em (2 - 9 tuổi)</label>
                        <input type="number" value="<?php echo $tour['gia_tre_em']; ?>" class="form-control" id="giaTreEm" name="giaTreEm" placeholder="VD: 3000000">
                    </div>

                    <div class="col-md-4">
                        <label for="giaTreNho" class="form-label">Giá trẻ nhỏ (&lt; 2 tuổi)</label>
                        <input type="number" value="<?php echo $tour['gia_tre_nho']; ?>" class="form-control" id="giaTreNho" name="giaTreNho" placeholder="VD: 1000000">
                    </div>

                    <div class="col-md-6">
                        <label for="anhDaiDien" class="form-label">Ảnh đại diện</label>
                        <input type="file" class="form-control" id="anhDaiDien" name="anhDaiDien" accept="image/*">
                        <div class="form-text">Ảnh hiện tại: <?php echo $tour['hinh_anh']; ?></div>
                    </div>

                    <div class="col-md-6">
                        <label for="banner" class="form-label">Ảnh Banner (tối đa 5 ảnh)</label>
                        <input type="file" class="form-control" id="banner" name="banner[]" accept="image/*" multiple>
                        <div class="form-text">Giữ Ctrl (hoặc Cmd) để chọn nhiều ảnh. Bỏ trống nếu không muốn thay đổi.</div>
                    </div>

                    <div class="col-12">
                        <label for="dichVu" class="form-label">Dịch vụ</label>
                        <textarea class="form-control" id="dichVu" name="dichVu" rows="5" placeholder="Liệt kê các dịch vụ đi kèm tour (mỗi dòng 1 dịch vụ)..."><?php echo $dichVu; ?></textarea>
                    </div>

                    <div class="col-12">
                        <label for="loTrinh" class="form-label">Lộ trình chi tiết</label>
                        <textarea class="form-control" id="loTrinh" name="loTrinh" rows="5" placeholder="Nhập các điểm đến và hoạt động từng ngày (mỗi dòng 1 ngày)..."><?php echo $loTrinh; ?></textarea>
                    </div>

                    <div class="col-12">
                        <label for="traiNghiem" class="form-label">Trải nghiệm nổi bật</label>
                        <textarea class="form-control" id="traiNghiem" name="traiNghiem" rows="5" placeholder="Những trải nghiệm đặc biệt du khách sẽ có (mỗi dòng 1 trải nghiệm)..."><?php echo $traiNghiem; ?></textarea>
                    </div>

                    <div class="col-12 text-end mt-3">
                        <button type="button" onclick="window.location.href='../../html/Admin/TourController.php'" class="btn btn-secondary me-2">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật Tour</button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <script>
        document.getElementById('banner').addEventListener('change', function() {
            if (this.files.length > 5) {
                alert('Bạn chỉ được chọn tối đa 5 ảnh banner!');
                this.value = '';
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>