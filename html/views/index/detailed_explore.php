<?php
Session_start();
include '../../../db/db.php';

// Lấy khampha_id từ URL (ví dụ: chitiet.php?id=1)
$khampha_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Truy vấn lấy thông tin bài viết
$sql = "SELECT bv.*, k.tieu_de as khampha_tieu_de 
        FROM bai_viet bv
        JOIN khampha k ON bv.khampha_id = k.khampha_id
        WHERE bv.khampha_id = ? AND bv.trang_thai = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $khampha_id);
$stmt->execute();
$result = $stmt->get_result();
$bai_viet = $result->fetch_assoc();
if($bai_viet['trang_thai'] != 1){
    header("Location: ../../../html/views/index/empty.php");
}

// Kiểm tra xem có bài viết không
if (!$bai_viet) {
    die("Không tìm thấy bài viết. Vui lòng kiểm tra lại khampha_id = " . $khampha_id);
}

// Truy vấn lấy các mục của bài viết
$sql_muc = "SELECT * FROM bai_viet_muc WHERE bai_viet_id = ? ORDER BY id";
$stmt_muc = $conn->prepare($sql_muc);
$stmt_muc->bind_param("i", $bai_viet['id']);
$stmt_muc->execute();
$result_muc = $stmt_muc->get_result();

// Truy vấn lấy các bài viết liên quan (cùng loại)
$sql_lienquan = "SELECT k.*, ka.duong_dan_anh 
                 FROM khampha k
                 LEFT JOIN khampha_anh ka ON k.khampha_id = ka.khampha_id
                 WHERE k.loai_id = (SELECT loai_id FROM khampha WHERE khampha_id = ?)
                 AND k.khampha_id != ?
                 GROUP BY k.khampha_id
                 LIMIT 6";
$stmt_lq = $conn->prepare($sql_lienquan);
$stmt_lq->bind_param("ii", $khampha_id, $khampha_id);
$stmt_lq->execute();
$result_lienquan = $stmt_lq->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($bai_viet['tieu_de']); ?></title>
    <link rel="stylesheet" href="../../../css/Main5.css">
    <link rel="stylesheet" href="../../../css/Main5_1.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body{
            background: url('https://images.pexels.com/photos/8892/pexels-photo.jpg?_gl=1*h8o504*_ga*MTY1MzgzNDc3Ni4xNzUyMTU3Nzk0*_ga_8JE65Q40S6*czE3NjA2NzE4MDQkbzUkZzEkdDE3NjA2NzIwNTkkajYwJGwwJGgw') no-repeat center center fixed;
            background-size: cover;
        }
    </style>
</head>
<body>
    <?php include '../../../includes/header.php' ?>
    <div class="detailed_explore_container">
        <div class="detailed_explore_container_tt">
            <h2><?php echo htmlspecialchars($bai_viet['tieu_de']); ?></h2>
            
            <?php if($result_muc->num_rows > 0): ?>
                <?php while($muc = $result_muc->fetch_assoc()): ?>
                <div class="detailed_explore_card ex_card fade-up">
                    <h3><?php echo htmlspecialchars($muc['tieu_de_muc']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($muc['noi_dung'])); ?></p>
                    <?php if(!empty($muc['hinh_anh'])): ?>
                    <div class="detailed_explore_img">
                        <img src="<?php echo htmlspecialchars("../../../" . $muc['hinh_anh']); ?>" alt="<?php echo htmlspecialchars($muc['tieu_de_muc']); ?>">
                    </div>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Chưa có nội dung chi tiết.</p>
            <?php endif; ?>
            
            <button class="xemchuyendi">Xem chuyến đi</button>
        </div>
        
        <div class="detailed_explore_container_qt ex_card fade-right">
            <h3>Có thể bạn quan tâm</h3><br>
            <?php if($result_lienquan->num_rows > 0): ?>
                <?php while($lienquan = $result_lienquan->fetch_assoc()): ?>
                <div onclick="window.location.href='detailed_explore.php?id=<?php echo $lienquan['khampha_id']; ?>'" class="detailed_explore_container_qt_card">
                    <img src="<?php echo htmlspecialchars("../" . $lienquan['duong_dan_anh'] ?? '../../../img/default.png'); ?>" alt="<?php echo htmlspecialchars($lienquan['tieu_de']); ?>">
                    <p><?php echo htmlspecialchars($lienquan['tieu_de']); ?></p>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Chưa có bài viết liên quan.</p>
            <?php endif; ?>
        </div>
    </div>
  
<div class="comment_section">
    <div class="comment_wrapper">
        <!-- HEADER -->
        <h3 class="comment_header"><i class="bi bi-chat-fill"></i> Bình luận (24)</h3>

        <!-- FORM VIẾT COMMENT -->
        <form class="comment_form" action="../../../php/ArticleCTL/comment.php" method="post">
            <textarea name="comment" placeholder="Viết bình luận của bạn..."></textarea>
            <input type="hidden" name="khampha_id" value="<?php echo $khampha_id; ?>">
           <?php
           if(isset($_SESSION['username'])) {
               $username = $_SESSION['username'];
               $layuser = "SELECT id FROM user WHERE ho_ten = '$username'";
               $kq_layuser = mysqli_query($conn, $layuser);
               $user = mysqli_fetch_assoc($kq_layuser);
               $user_id = $user['id'];}
              else {
                  $user_id = 0;
              }
             
           ?>
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <button type="submit">Gửi bình luận</button>
        </form>

        <!-- DANH SÁCH COMMENT -->
        <div class="comment_list">
            <!-- Comment 1 -->
           <?php
           $laycm = "SELECT 
    bl.id AS binh_luan_id,
    bl.khampha_id,
    bl.noi_dung,
    bl.so_luot_thich,
    bl.ngay_tao,
    u.id AS user_id,
    u.ho_ten AS ten_user,
    u.avatar
FROM binh_luan bl
LEFT JOIN user u ON bl.user_id = u.id
WHERE bl.khampha_id = $khampha_id
ORDER BY bl.ngay_tao DESC;
";
           $laykqcm = mysqli_query($conn, $laycm);

           while($cm = mysqli_fetch_assoc($laykqcm)) {

           ?>
            <div class="comment_item">
                <div class="comment_user">
                    <span class="user_name"><?php echo $cm['ten_user'] ?></span>
                    <span class="comment_date"><?php echo $cm['ngay_tao'] ?></span>
                </div>
                <div class="comment_content">
                    <?php echo $cm['noi_dung'] ?>
                </div>
                <div class="comment_actions">
                    <button class="action_btn"><i class="bi bi-heart-fill"></i> Thích (<?php echo $cm['so_luot_thich'] ?>)</button>
                    <button class="action_btn"><i class="bi bi-flag-fill"></i> Báo cáo</button>
                </div>
            </div>
           <?php } ?>

          
        </div>
    </div>
</div>
    <?php include '../../../includes/footer.php'?>
</body>
<script src="../../../js/Main5.js"></script>
</html>
<?php
$stmt->close();
$stmt_muc->close();
$stmt_lq->close();
$conn->close();
?>