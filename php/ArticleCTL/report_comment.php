<?php
session_start();
include '../../db/db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $bai_viet_id = intval($_POST['khampha_id']); // ID bài viết
    $binh_luan_id = intval($_POST['binh_luan_id']); // ID bình luận

    // Kiểm tra người dùng đã đăng nhập
    if(isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0){
        $nguoi_bao_cao_id = $_SESSION['user_id'];

        // Lấy thông tin người báo cáo
        $stmt = $conn->prepare("SELECT ho_ten, email, sdt FROM user WHERE id = ?");
        $stmt->bind_param("i", $nguoi_bao_cao_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $nguoi_bao_cao = $result->fetch_assoc();
        $stmt->close();

        $ten_nguoi_bao_cao = $nguoi_bao_cao['ho_ten'];
        $email_nguoi_bao_cao = $nguoi_bao_cao['email'];
        $sdt_nguoi_bao_cao = $nguoi_bao_cao['sdt'];
    } else {
        header("Location: ../../html/views/index/login.php");
        exit();
    }

    // Lấy thông tin bình luận bị báo cáo
    $stmt_bl = $conn->prepare("SELECT bl.user_id AS nguoi_bi_bao_cao_id, bl.noi_dung, u.ho_ten AS ten_nguoi_bi_bao_cao 
                               FROM binh_luan bl
                               JOIN user u ON bl.user_id = u.id
                               WHERE bl.id = ?");
    $stmt_bl->bind_param("i", $binh_luan_id);
    $stmt_bl->execute();
    $result_bl = $stmt_bl->get_result();
    $binh_luan = $result_bl->fetch_assoc();
    $stmt_bl->close();

    if(!$binh_luan){
        $_SESSION['error'] = 1;
        $_SESSION['text_error'] = "Bình luận không tồn tại!";
        header("Location: ../../html/views/index/detailed_explore.php?id=$bai_viet_id");
        exit();
    }

    $nguoi_bi_bao_cao_id = $binh_luan['nguoi_bi_bao_cao_id'];
    $ten_nguoi_bi_bao_cao = $binh_luan['ten_nguoi_bi_bao_cao'];
    $noi_dung_binh_luan = $binh_luan['noi_dung'];

    // Lấy tên bài viết
    $stmt_bv = $conn->prepare("SELECT tieu_de FROM khampha WHERE khampha_id = ?");
    $stmt_bv->bind_param("i", $bai_viet_id);
    $stmt_bv->execute();
    $result_bv = $stmt_bv->get_result();
    $bai_viet = $result_bv->fetch_assoc();
    $stmt_bv->close();

    $ten_bai_viet = $bai_viet['tieu_de'] ?? 'Bài viết không xác định';

    // Nội dung báo cáo đầy đủ
    $noi_dung_bao_cao = "Người báo cáo: $ten_nguoi_bao_cao (ID: $nguoi_bao_cao_id)\n";
    $noi_dung_bao_cao .= "Bình luận của người dùng: $ten_nguoi_bi_bao_cao (ID: $nguoi_bi_bao_cao_id)\n";
    $noi_dung_bao_cao .= "Bài viết: $ten_bai_viet (ID: $bai_viet_id)\n";
    $noi_dung_bao_cao .= "Nội dung bình luận: $noi_dung_binh_luan";

    // Thêm vào bảng khieu_nai
    $stmt_insert = $conn->prepare("INSERT INTO khieu_nai (user_id, ho_ten, email, noi_dung, sdt, trang_thai) VALUES (?, ?, ?, ?, ?, 0)");
    $stmt_insert->bind_param("isssi", $nguoi_bao_cao_id, $ten_nguoi_bao_cao, $email_nguoi_bao_cao, $noi_dung_bao_cao, $sdt_nguoi_bao_cao);

    if($stmt_insert->execute()){
        $_SESSION['success'] = 1;
        $_SESSION['text_success'] = "Báo cáo đã gửi đến quản trị!";
    } else {
        $_SESSION['error'] = 1;
        $_SESSION['text_error'] = "Lỗi gửi báo cáo: " . $stmt_insert->error;
    }

    $stmt_insert->close();
    $conn->close();

    header("Location: ../../html/views/index/detailed_explore.php?id=$bai_viet_id");
    exit();
}
?>
