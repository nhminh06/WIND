<?php
include '../../db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uploadDir = '../../uploads/';

    // === 1. Upload ảnh đại diện ===
    $anhDaiDien = $_FILES['anhDaiDien']['name'];
    $tmpDaiDien = $_FILES['anhDaiDien']['tmp_name'];
    $pathDaiDien = $uploadDir . basename($anhDaiDien);
    move_uploaded_file($tmpDaiDien, $pathDaiDien);

    // === 2. Lưu thông tin tour ===
    $matour  = $_POST['maTour'];
    $tentour = $_POST['tenTour'];
    $loaitour = $_POST['loaiTour'];
    $ngaykhoihanh = $_POST['ngayKhoiHanh'];
    $songay = $_POST['soNgay'];
    $gianuoilon = $_POST['giaNguoiLon'];
    $giatreem = $_POST['giaTreEm'];
    $giatrenho = $_POST['giaTreNho'];
    $diemKhoiHanh = $_POST['diemKhoiHanh'];
    $dichVu = $_POST['dichVu'];       // textarea nhiều dòng
    $loTrinh = $_POST['loTrinh'];     // textarea nhiều dòng
    $traiNghiem = $_POST['traiNghiem']; // textarea nhiều dòng

    // --- Thêm tour ---
    $sql = "INSERT INTO tour (ten_tour, hinh_anh, so_ngay, gia, trang_thai, loai_banner)
            VALUES ('$tentour', '$anhDaiDien', '$songay', '$gianuoilon', 1, '$loaitour')";
    mysqli_query($conn, $sql);
    $tour_id = mysqli_insert_id($conn);

    // --- Thêm tour chi tiết ---
    $sql2 = "INSERT INTO tour_chi_tiet (tour_id, ma_tour, diem_khoi_hanh)
             VALUES ('$tour_id','$matour', '$diemKhoiHanh')";
    mysqli_query($conn, $sql2);

    // --- Thêm lịch khởi hành ---
    $sql5 = "INSERT INTO lich_khoi_hanh (tour_id, ngay_khoi_hanh, gia_nguoi_lon, gia_tre_em, gia_tre_nho)
             VALUES ('$tour_id', '$ngaykhoihanh', '$gianuoilon', '$giatreem', '$giatrenho')";
    mysqli_query($conn, $sql5);

    // === 3. Xử lý Dịch vụ ===
   $dichVuArr = array_filter(array_map('trim', explode("\n", $dichVu)));
foreach ($dichVuArr as $dv) {
    $sql3 = "INSERT INTO dich_vu (tour_id, ten_dich_vu)
             VALUES ('$tour_id', '$dv')";
    mysqli_query($conn, $sql3);
}

    // === 4. Xử lý Trải nghiệm ===
    $traiNghiemArr = array_filter(array_map('trim', explode("\n", $traiNghiem)));
foreach ($traiNghiemArr as $tn) {
    $sql4 = "INSERT INTO trai_nghiem (tour_id, noi_dung)
             VALUES ('$tour_id', '$tn')";
    mysqli_query($conn, $sql4);
}


    // === 5. Upload nhiều ảnh banner + Lộ trình ===
    $loTrinhArr = array_filter(array_map('trim', explode("\n", $loTrinh)));
    $ngay = 1;

    foreach ($_FILES['banner']['name'] as $key => $value) {
        if (!empty($_FILES['banner']['name'][$key])) {
            $fileName = $_FILES['banner']['name'][$key];
            $tmpName = $_FILES['banner']['tmp_name'][$key];
            $path = $uploadDir . basename($fileName);
            move_uploaded_file($tmpName, $path);

            // Lưu ảnh vào bảng tour_anh
            $sqlBanner = "INSERT INTO tour_anh (tour_id, hinh_anh)
                          VALUES ('$tour_id', '$fileName')";
            mysqli_query($conn, $sqlBanner);

            // Lưu lộ trình (1 dòng tương ứng 1 ảnh)
            $noiDung = isset($loTrinhArr[$key]) ? $loTrinhArr[$key] : "Ngày $ngay: Chưa có mô tả";
            $sqlLT = "INSERT INTO lich_trinh (tour_id, ngay, noi_dung, hinh_anh)
                      VALUES ('$tour_id', '$ngay', '$noiDung', '$fileName')";
            mysqli_query($conn, $sqlLT);

            $ngay++;
        }
    }

   header("Location: ../../html/views/index/detailed_tour.php?id=$tour_id");
}
?>
