<?php
include '../../db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['id'])) {
    $tour_id = mysqli_real_escape_string($conn, $_GET['id']);
    $uploadDir = '../../uploads/';

    // === Lấy dữ liệu từ form ===
    $matour = mysqli_real_escape_string($conn, $_POST['maTour']);
    $tentour = mysqli_real_escape_string($conn, $_POST['tenTour']);
    $loaitour = mysqli_real_escape_string($conn, $_POST['loaiTour']);
    $ngaykhoihanh = mysqli_real_escape_string($conn, $_POST['ngayKhoiHanh']);
    $songay = mysqli_real_escape_string($conn, $_POST['soNgay']);
    $gianuoilon = mysqli_real_escape_string($conn, $_POST['giaNguoiLon']);
    $giatreem = mysqli_real_escape_string($conn, $_POST['giaTreEm']);
    $giatrenho = mysqli_real_escape_string($conn, $_POST['giaTreNho']);
    $diemKhoiHanh = mysqli_real_escape_string($conn, $_POST['diemKhoiHanh']);
    $dichVu = $_POST['dichVu'];
    $loTrinh = $_POST['loTrinh'];
    $traiNghiem = $_POST['traiNghiem'];

    // === 1. Xử lý ảnh đại diện ===
    if (!empty($_FILES['anhDaiDien']['name'])) {
        $anhDaiDien = $_FILES['anhDaiDien']['name'];
        $tmpDaiDien = $_FILES['anhDaiDien']['tmp_name'];
        $pathDaiDien = $uploadDir . basename($anhDaiDien);
        
        if (!move_uploaded_file($tmpDaiDien, $pathDaiDien)) {
            die("Lỗi: Không thể upload ảnh đại diện!");
        }
    } else {
        // Giữ nguyên ảnh cũ nếu không chọn ảnh mới
        $sqlOld = "SELECT hinh_anh FROM tour WHERE id = '$tour_id'";
        $resultOld = mysqli_query($conn, $sqlOld);
        $rowOld = mysqli_fetch_assoc($resultOld);
        $anhDaiDien = $rowOld['hinh_anh'];
    }

    // === 2. Cập nhật bảng tour ===
    $sql = "UPDATE tour 
            SET ten_tour = '$tentour',
                hinh_anh = '$anhDaiDien',
                so_ngay = '$songay',
                gia = '$gianuoilon',
                loai_banner = '$loaitour'
            WHERE id = '$tour_id'";
    
    if (!mysqli_query($conn, $sql)) {
        die("Lỗi cập nhật tour: " . mysqli_error($conn));
    }

    // === 3. Cập nhật bảng tour_chi_tiet ===
    $sql2 = "UPDATE tour_chi_tiet 
             SET ma_tour = '$matour',
                 diem_khoi_hanh = '$diemKhoiHanh'
             WHERE tour_id = '$tour_id'";
    
    if (!mysqli_query($conn, $sql2)) {
        // Nếu chưa có record, thử insert
        $sql2_insert = "INSERT INTO tour_chi_tiet (tour_id, ma_tour, diem_khoi_hanh)
                        VALUES ('$tour_id', '$matour', '$diemKhoiHanh')";
        mysqli_query($conn, $sql2_insert);
    }

    // === 4. Cập nhật bảng lich_khoi_hanh ===
    $sql3 = "UPDATE lich_khoi_hanh
             SET ngay_khoi_hanh = '$ngaykhoihanh',
                 gia_nguoi_lon = '$gianuoilon',
                 gia_tre_em = '$giatreem',
                 gia_tre_nho = '$giatrenho'
             WHERE tour_id = '$tour_id'";
    
    if (!mysqli_query($conn, $sql3)) {
        // Nếu chưa có record, thử insert
        $sql3_insert = "INSERT INTO lich_khoi_hanh (tour_id, ngay_khoi_hanh, gia_nguoi_lon, gia_tre_em, gia_tre_nho)
                        VALUES ('$tour_id', '$ngaykhoihanh', '$gianuoilon', '$giatreem', '$giatrenho')";
        mysqli_query($conn, $sql3_insert);
    }

    // === 5. Cập nhật dịch vụ ===
    mysqli_query($conn, "DELETE FROM dich_vu WHERE tour_id = '$tour_id'");
    $dichVuArr = array_filter(array_map('trim', explode("\n", $dichVu)));
    foreach ($dichVuArr as $dv) {
        $dv = mysqli_real_escape_string($conn, $dv);
        $sqlDV = "INSERT INTO dich_vu (tour_id, ten_dich_vu)
                  VALUES ('$tour_id', '$dv')";
        mysqli_query($conn, $sqlDV);
    }

    // === 6. Cập nhật trải nghiệm ===
    mysqli_query($conn, "DELETE FROM trai_nghiem WHERE tour_id = '$tour_id'");
    $traiNghiemArr = array_filter(array_map('trim', explode("\n", $traiNghiem)));
    foreach ($traiNghiemArr as $tn) {
        $tn = mysqli_real_escape_string($conn, $tn);
        $sqlTN = "INSERT INTO trai_nghiem (tour_id, noi_dung)
                  VALUES ('$tour_id', '$tn')";
        mysqli_query($conn, $sqlTN);
    }

    // === 7. Xử lý lộ trình + ảnh banner (LOGIC MỚI) ===
    
    // Bước 1: Lấy danh sách ảnh cũ từ database
    $sqlOldAnh = "SELECT hinh_anh FROM tour_anh WHERE tour_id = '$tour_id' ORDER BY id ASC";
    $resultAnh = mysqli_query($conn, $sqlOldAnh);
    $oldImages = [];
    while ($r = mysqli_fetch_assoc($resultAnh)) {
        $oldImages[] = $r['hinh_anh'];
    }

    // Bước 2: Tách lộ trình thành từng dòng
    $loTrinhArr = array_filter(array_map('trim', explode("\n", $loTrinh)));
    
    // Bước 3: Xử lý ảnh mới được upload
    $newImages = [];
    $hasNewUpload = false;
    
    if (isset($_FILES['banner']['name']) && !empty($_FILES['banner']['name'][0])) {
        foreach ($_FILES['banner']['name'] as $key => $value) {
            if (!empty($_FILES['banner']['name'][$key]) && $_FILES['banner']['error'][$key] == 0) {
                $fileName = time() . '_' . basename($_FILES['banner']['name'][$key]); // Thêm timestamp để tránh trùng tên
                $tmpName = $_FILES['banner']['tmp_name'][$key];
                $path = $uploadDir . $fileName;
                
                if (move_uploaded_file($tmpName, $path)) {
                    $newImages[] = $fileName;
                    $hasNewUpload = true;
                }
            }
        }
    }

    // Bước 4: Quyết định dùng ảnh nào
    // Nếu có ảnh mới upload -> dùng ảnh mới
    // Nếu không có ảnh mới -> giữ nguyên ảnh cũ
    $finalImages = $hasNewUpload ? $newImages : $oldImages;

    // Bước 5: Xóa dữ liệu cũ
    mysqli_query($conn, "DELETE FROM lich_trinh WHERE tour_id = '$tour_id'");
    
    if ($hasNewUpload) {
        // Chỉ xóa ảnh cũ khi có ảnh mới
        mysqli_query($conn, "DELETE FROM tour_anh WHERE tour_id = '$tour_id'");
    }

    // Bước 6: Lưu lại dữ liệu mới
    $maxCount = max(count($finalImages), count($loTrinhArr));
    
    for ($i = 0; $i < $maxCount; $i++) {
        $ngay = $i + 1;
        $fileName = isset($finalImages[$i]) ? $finalImages[$i] : null;
        $noiDung = isset($loTrinhArr[$i]) ? mysqli_real_escape_string($conn, $loTrinhArr[$i]) : "Ngày $ngay: Chưa có mô tả";

        // Chỉ lưu khi có ảnh
        if ($fileName) {
            // Lưu ảnh vào tour_anh (chỉ khi có ảnh mới)
            if ($hasNewUpload) {
                $sqlBanner = "INSERT INTO tour_anh (tour_id, hinh_anh)
                              VALUES ('$tour_id', '$fileName')";
                mysqli_query($conn, $sqlBanner);
            }

            // Lưu lộ trình (luôn lưu lại)
            $sqlLT = "INSERT INTO lich_trinh (tour_id, ngay, noi_dung, hinh_anh)
                      VALUES ('$tour_id', '$ngay', '$noiDung', '$fileName')";
            mysqli_query($conn, $sqlLT);
        }
    }

    // === 8. Redirect về trang danh sách ===
    header("Location: ../../html/views/index/detailed_tour.php?id=$tour_id");
    exit;
} else {
    die("Invalid request method!");
}