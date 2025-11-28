<?php
session_start();
include '../../../db/db.php';

// Lấy khampha_id từ URL (ví dụ: chitiet.php?id=1)
$khampha_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Truy vấn lấy thông tin bài viết
$sql = "SELECT bv.*, k.tieu_de as khampha_tieu_de , bv.tour_id
        FROM bai_viet bv
        JOIN khampha k ON bv.khampha_id = k.khampha_id
        WHERE bv.khampha_id = ? AND bv.trang_thai = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $khampha_id);
$stmt->execute();
$result = $stmt->get_result();
$bai_viet = $result->fetch_assoc();

if(!$bai_viet || $bai_viet['trang_thai'] != 1){
    header("Location: ../../../html/views/index/empty.php");
    exit();
}

// ==================== PHẦN SỬA CHÍNH ====================
// LẤY USER_ID TỪ SESSION - KHÔNG QUERY DATABASE
if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']); 
} else {
    $user_id = 0; // Guest user (chưa đăng nhập)
}
// ========================================================

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
            background: url('https://i.pinimg.com/1200x/5c/8c/67/5c8c67d3c5a64d138c6f67689b73cb36.jpg') no-repeat center center fixed;
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

            <button onclick="window.location.href='detailed_tour.php?id=<?php echo $bai_viet['tour_id']; ?>'" class="xemchuyendi">Xem chuyến đi</button>
        </div>
        
        <div class="detailed_explore_container_qt ex_card fade-right">
            <h3>Có thể bạn quan tâm</h3><br>
            <?php if($result_lienquan->num_rows > 0): ?>
                <?php while($lienquan = $result_lienquan->fetch_assoc()): ?>
                <div onclick="window.location.href='detailed_explore.php?id=<?php echo $lienquan['khampha_id']; ?>'" class="detailed_explore_container_qt_card">
                    <img src="<?php echo htmlspecialchars("../" . ($lienquan['duong_dan_anh'] ?? '../../../img/default.png')); ?>" alt="<?php echo htmlspecialchars($lienquan['tieu_de']); ?>">
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
        <?php
        $sql_dembl = "SELECT COUNT(*) AS tong_binh_luan FROM binh_luan WHERE khampha_id = ?";
        $stmt_dembl = $conn->prepare($sql_dembl);
        $stmt_dembl->bind_param("i", $khampha_id);
        $stmt_dembl->execute();
        $result_dembl = $stmt_dembl->get_result();
        $so_binh_luan = $result_dembl->fetch_assoc()['tong_binh_luan'];
        $stmt_dembl->close();
        ?>
        <h3 class="comment_header"><i class="bi bi-chat-fill"></i> Bình luận (<?php echo $so_binh_luan; ?>)</h3>

        <!-- FORM VIẾT COMMENT -->
        <form class="comment_form" action="../../../php/ArticleCTL/comment.php" method="post">
            <textarea name="comment" placeholder="Viết bình luận của bạn..." required></textarea>
            <input type="hidden" name="khampha_id" value="<?php echo $khampha_id; ?>">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            
            <?php if($user_id == 0): ?>
                <p style="color: red; font-size: 14px;">⚠️ Bạn cần <a href="../../../html/views/index/login.php" style="color: #007bff;">đăng nhập</a> để bình luận.</p>
            <?php endif; ?>
            
            <button type="submit" <?php echo ($user_id == 0) ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : ''; ?>>Gửi bình luận</button>
        </form>

        <div class="comment_list">
           <?php
           // SỬ DỤNG PREPARED STATEMENT CHO BÌNH LUẬN
           $sql_laycm = "SELECT 
                bl.id AS binh_luan_id,
                bl.khampha_id,
                bl.noi_dung,
                bl.so_luot_thich,
                bl.ngay_tao,
                bl.user_id,
                u.ho_ten AS ten_user,
                u.avatar,
                lb.thich AS thich
            FROM binh_luan bl
            LEFT JOIN user u ON bl.user_id = u.id
            LEFT JOIN like_binhluan lb 
                ON bl.id = lb.binh_luan_id AND lb.user_id = ?
            WHERE bl.khampha_id = ?
            ORDER BY bl.ngay_tao DESC";
            
           $stmt_laycm = $conn->prepare($sql_laycm);
           $stmt_laycm->bind_param("ii", $user_id, $khampha_id);
           $stmt_laycm->execute();
           $result_laycm = $stmt_laycm->get_result();

           if($result_laycm->num_rows > 0):
               while($cm = $result_laycm->fetch_assoc()): 
           ?>
            <div class="comment_item">
                <div class="comment_user">
                  <div class="user-cm">  
                     <div class="avatar-cm">
  <img id="avatarImg" src="<?php echo "../../../../" . (!empty($cm['avatar']) ? $cm['avatar'] : 'img/avatamacdinh.png'); ?>" alt="Ảnh đại diện" style="cursor: pointer;">
  <input type="file" id="avatarInput" accept="image/*" style="display: none;">
</div>  
                  <span class="user_name"><?php echo htmlspecialchars($cm['ten_user']); ?></span></div>
                    <span class="comment_date"><?php echo htmlspecialchars($cm['ngay_tao']); ?></span>
                </div>
                <div class="comment_content">
                    <?php echo nl2br(htmlspecialchars($cm['noi_dung'])); ?>
                </div>
                <div class="comment_actions">
                    <!-- NÚT LIKE - CHỈ TRUYỀN id VÀ khampha_id -->
                    <button onclick="<?php 
                        if($user_id == 0) {
                            echo "alert('Vui lòng đăng nhập để thích bình luận!'); return false;";
                        } else {
                            echo "window.location.href='../../../php/ArticleCTL/like_comment.php?id=" . $cm['binh_luan_id'] . "&khampha_id=" . $khampha_id . "'";
                        }
                    ?>" class="action_btn">
                        <i class="bi bi-heart-fill" style="color: <?php echo ($cm['thich'] == 1) ? 'red' : 'white'; ?>"></i> 
                        Thích (<?php echo $cm['so_luot_thich']; ?>)
                    </button>
                    
                   <!-- NÚT BÁO CÁO -->
                    <?php if($user_id != 0): ?>
                    <form action="../../../php/ArticleCTL/report_comment.php" method="post" style="display:inline;">
                        <input type="hidden" name="khampha_id" value="<?php echo $khampha_id; ?>">
                        <input type="hidden" name="binh_luan_id" value="<?php echo $cm['binh_luan_id']; ?>">
                        <input type="hidden" name="nguoi_bao_cao" value="<?php echo $user_id; ?>">
                        <input type="hidden" name="nguoi_bi_bao_cao" value="<?php echo $cm['user_id']; ?>">
                        <input type="hidden" name="noi_dung" value="<?php echo htmlspecialchars($cm['noi_dung']); ?>">
                        <button type="submit" class="action_btn"><i class="bi bi-flag-fill"></i> Báo cáo</button>
                    </form>
                    <?php endif; ?>

                                        
                    <!-- NÚT XÓA - CHỈ HIỂN THỊ CHO CHỦ COMMENT -->
                    <?php if($user_id == $cm['user_id'] || $_SESSION['role']=='admin'): ?>
                    <button 
                        onclick="if(confirm('Bạn có chắc chắn muốn xóa bình luận này không?')){window.location.href='../../../php/ArticleCTL/delete_comment.php?id=<?php echo $cm['binh_luan_id']; ?>&khampha_id=<?php echo $khampha_id; ?>'}"
                        class="action_btn">
                        <i class="bi bi-trash3-fill"></i> Xóa
                    </button>
                    <?php endif; ?>
                </div>
            </div>
           <?php 
               endwhile;
           else:
           ?>
               <p style="text-align:center; color:#666; padding:20px;">Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
           <?php 
           endif;
           $stmt_laycm->close();
           ?>
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