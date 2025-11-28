<?php
include '../../../db/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin nhân viên
$sql = "SELECT * FROM user WHERE id = $id";
$rs = $conn->query($sql);
$staff = $rs->fetch_assoc();

if (!$staff) {
    echo "Không tìm thấy nhân viên.";
    exit;
}

// Lấy kỹ năng
$skill_q = $conn->query("SELECT skill_name FROM staff_skill WHERE staff_id = $id");

// Lấy kinh nghiệm
$exp_q = $conn->query("SELECT * FROM staff_experience WHERE staff_id = $id ORDER BY year_start DESC");
?>

<div class="profile_container">

    <!-- Ảnh + info cơ bản -->
    <div class="profile_header">
        <?php
        $avt = $staff['avatar'] ?? "img/avatamacdinh.png";
        ?>
        <img class="avatar-pf" src="<?php echo '../../../' . $avt; ?>">
        
        <div class="info">
            <h1><?php echo $staff['ho_ten']; ?></h1>
            <p class="position"><?php echo $staff['position']; ?></p>

            <p><b>Email:</b> <?php echo $staff['email']; ?></p>
            <p><b>SĐT:</b> <?php echo $staff['sdt']; ?></p>
            <p><b>Địa chỉ:</b> <?php echo $staff['dia_chi']; ?></p>
            <p><b>Giới tính:</b> <?php echo $staff['gioi_tinh']; ?></p>
            <p><b>Ngày sinh:</b> <?php echo $staff['ngay_sinh']; ?></p>
        </div>
    </div>

    <hr>

    <!-- Giới thiệu -->
    <div class="section-pf">
        <h2>Giới thiệu</h2>
        <p><?php echo nl2br($staff['about']); ?></p>
    </div>

    <!-- Kỹ năng -->
    <div class="section-pf">
        <h2>Kỹ năng chuyên môn</h2>
        <ul class="skill_list">
            <?php while ($sk = $skill_q->fetch_assoc()) { ?>
                <li><?php echo $sk['skill_name']; ?></li>
            <?php } ?>
        </ul>
    </div>

    <!-- Kinh nghiệm -->
    <div class="section-pf">
        <h2>Kinh nghiệm làm việc</h2>
        <div class="timeline">
            <?php while ($ex = $exp_q->fetch_assoc()) { ?>
                <div class="timeline_item">
                    <div class="time">
                        <?php echo $ex['year_start']; ?> - <?php echo $ex['year_end'] ?: "Hiện tại"; ?>
                    </div>
                    <div class="content">
                        <h3><?php echo $ex['title']; ?></h3>
                        <p><?php echo nl2br($ex['description']); ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Mạng xã hội -->
    <div class="section-pf">
        <h2>Kết nối</h2>
        <div class="social_links">
            <a style="background: gray;" href="#"><img src="../../../img/facebook.png"></a>
            <a style="background: gray;" href="#"><img src="../../../img/Instagram.png"></a>
            <a style="background: gray;" href="#"><img src="../../../img/youtube.png"></a>
        </div>
    </div>

</div>