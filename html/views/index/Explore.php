<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khám phá</title>
    <link rel="stylesheet" href="../../../css/Main5.css">
    <link rel="stylesheet" href="../../../css/Main5_1.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarina&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body{
            background: url('https://i.pinimg.com/1200x/96/12/29/9612295f6129ce93bef203b6c66381df.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }
        
       
    </style>
</head>
<body>
    <?php include '../../../includes/header.php'; ?>
    
    <div class="about_container">
        <div class="about_top">
            <h4>Khám Phá Miền Trung</h4>
            <h5>Hành trình tìm về phong tục, văn hóa, ẩm thực và phong cảnh</h5>
            <img src="../../../img/logo.png" alt="">
        </div> 
    </div>

    <?php 
    include '../../../db/db.php';
    
    // Số mục mỗi trang
    $items_per_page = 3;
    
    // Lấy trang hiện tại từ URL
    $page_langnghe = isset($_GET['page_langnghe']) ? (int)$_GET['page_langnghe'] : 1;
    $page_dacsan = isset($_GET['page_dacsan']) ? (int)$_GET['page_dacsan'] : 1;
    $page_vanhoa = isset($_GET['page_vanhoa']) ? (int)$_GET['page_vanhoa'] : 1;
    
    // Tính offset
    $offset_langnghe = ($page_langnghe - 1) * $items_per_page;
    $offset_dacsan = ($page_dacsan - 1) * $items_per_page;
    $offset_vanhoa = ($page_vanhoa - 1) * $items_per_page;
    ?>

    <!-- Làng nghề truyền thống -->
    <div class="ex_container">
        <h2>Làng nghề truyền thống</h2>
        <div class="ex_content">
            <?php 
            // Đếm tổng số mục
            $count_sql = "SELECT COUNT(*) as total FROM khampha WHERE loai_id = 1 AND trang_thai = 1";
            $count_result = mysqli_query($conn, $count_sql);
            $total_items = mysqli_fetch_assoc($count_result)['total'];
            $total_pages = ceil($total_items / $items_per_page);
            
            // Lấy dữ liệu với phân trang
            $sql = "SELECT k.*, bv.id as bai_viet_id 
                    FROM khampha k 
                    LEFT JOIN bai_viet bv ON k.khampha_id = bv.khampha_id 
                    WHERE k.loai_id = 1 AND k.trang_thai = 1
                    LIMIT $items_per_page OFFSET $offset_langnghe";
            $laylangnghe = mysqli_query($conn, $sql);
            $i = 0;
            while($laylangnghe2 = mysqli_fetch_assoc($laylangnghe)){
                $khampha_id = $laylangnghe2['khampha_id'];
                $bai_viet_id = $laylangnghe2['bai_viet_id'];
                $i++;
                if($i % 2 != 0){
            ?>
            <div class="ex_item">
                <div class="ex_all_img">
                    <?php
                    $sql_anh = "SELECT * FROM khampha_anh WHERE khampha_id = $khampha_id";
                    $layanhlangnghe = mysqli_query($conn, $sql_anh);
                    while($layanhlangnghe2 = mysqli_fetch_assoc($layanhlangnghe)){
                    ?>
                    <div class="ex_img box fade-up"><img src="<?php echo "../" . $layanhlangnghe2['duong_dan_anh'] ?>" alt=""></div>
                    <?php } ?>
                </div>
                <div class="ex_tt box fade-right">
                    <h2><?php echo $laylangnghe2['tieu_de'] ?></h2>
                   <p><?php echo nl2br($laylangnghe2['mo_ta_ngan']); ?></p>

                    <?php if($bai_viet_id): ?>
                    <button class="xemthem" onclick="window.location.href='detailed_explore.php?id=<?php echo $khampha_id; ?>'">Xem thêm</button>
                    <?php else: ?>
                    <button class="xemthem" disabled>Chưa có bài viết</button>
                    <?php endif; ?>
                </div>
            </div>
            <?php } else { ?>
            <div class="ex_item">
                <div class="ex_tt box fade-left">
                    <h2><?php echo $laylangnghe2['tieu_de'] ?></h2>
                 <p><?php echo nl2br($laylangnghe2['mo_ta_ngan']); ?></p>

                    <?php if($bai_viet_id): ?>
                    <button class="xemthem" onclick="window.location.href='detailed_explore.php?id=<?php echo $khampha_id; ?>'">Xem thêm</button>
                    <?php else: ?>
                    <button class="xemthem" disabled>Chưa có bài viết</button>
                    <?php endif; ?>
                </div>
                <div class="ex_all_img">
                    <?php
                    $sql_anh = "SELECT * FROM khampha_anh WHERE khampha_id = $khampha_id";
                    $layanhlangnghe = mysqli_query($conn, $sql_anh);
                    while($layanhlangnghe2 = mysqli_fetch_assoc($layanhlangnghe)){
                    ?>
                    <div class="ex_img box fade-up"><img src="<?php echo "../" . $layanhlangnghe2['duong_dan_anh'] ?>" alt=""></div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
            <?php } ?>
        </div>
        
        <!-- Phân trang Làng nghề -->
         <?php if($total_pages > 1): ?>
        <div class="change-container">
            <button class="change-btn" onclick="window.location.href='?page_langnghe=<?php echo max(1, $page_langnghe - 1); ?>&page_dacsan=<?php echo $page_dacsan; ?>&page_vanhoa=<?php echo $page_vanhoa; ?>#langnghe'" 
                    <?php echo $page_langnghe <= 1 ? 'disabled' : ''; ?>>
                <i class="bi bi-caret-left-fill"></i>
            </button>
            
            <span class="page-info">Trang <?php echo $page_langnghe; ?> / <?php echo $total_pages; ?></span>
            
            <button class="change-btn" onclick="window.location.href='?page_langnghe=<?php echo min($total_pages, $page_langnghe + 1); ?>&page_dacsan=<?php echo $page_dacsan; ?>&page_vanhoa=<?php echo $page_vanhoa; ?>#langnghe'" 
                    <?php echo $page_langnghe >= $total_pages ? 'disabled' : ''; ?>>
                <i class="bi bi-caret-right-fill"></i>
            </button>
        </div>
        <?php endif; ?>
    </div>

    <!-- Đặc sản địa phương -->
    <div class="ex_container2" id="dacsan">
        <h2>Đặc sản địa phương</h2>
        <div class="ex_content">
            <?php
            // Đếm tổng số mục
            $count_sql = "SELECT COUNT(*) as total FROM khampha WHERE loai_id = 2 AND trang_thai = 1";
            $count_result = mysqli_query($conn, $count_sql);
            $total_items_dacsan = mysqli_fetch_assoc($count_result)['total'];
            $total_pages_dacsan = ceil($total_items_dacsan / $items_per_page);
            
            $sql = "SELECT k.*, bv.id as bai_viet_id 
                    FROM khampha k 
                    LEFT JOIN bai_viet bv ON k.khampha_id = bv.khampha_id 
                    WHERE k.loai_id = 2 AND k.trang_thai = 1
                    LIMIT $items_per_page OFFSET $offset_dacsan";
            $layamthuc = mysqli_query($conn, $sql);
            $h = 0;
            while ($layamthuc2 = mysqli_fetch_assoc($layamthuc)) {
                $khampha_id = $layamthuc2['khampha_id'];
                $bai_viet_id = $layamthuc2['bai_viet_id'];
                $h++;
                if($h % 2 != 0){
            ?>
            <div class="ex_item">
                <div class="ex_all_img">
                    <?php
                    $sql_anh = "SELECT * FROM khampha_anh WHERE khampha_id = $khampha_id";
                    $layanhamthuc = mysqli_query($conn, $sql_anh);
                    while ($layanhamthuc2 = mysqli_fetch_assoc($layanhamthuc)) {
                    ?>
                    <div class="box rolling-ball">
                        <img src="<?php echo "../" . $layanhamthuc2['duong_dan_anh']; ?>" alt="">
                    </div>
                    <?php } ?>
                </div>
                <div class="ex_tt box fade-left">
                    <h2><?php echo $layamthuc2['tieu_de']; ?></h2>
                    <p><?php echo nl2br($layamthuc2['mo_ta_ngan']); ?></p>
                    <?php if($bai_viet_id): ?>
                    <button class="xemthem" onclick="window.location.href='detailed_explore.php?id=<?php echo $khampha_id; ?>'">Xem thêm</button>
                    <?php else: ?>
                    <button class="xemthem" disabled>Chưa có bài viết</button>
                    <?php endif; ?>
                </div>
            </div>
            <?php } else { ?>
            <div class="ex_item">
                <div class="ex_tt box fade-right">
                    <h2><?php echo $layamthuc2['tieu_de']; ?></h2>
                    <p><?php echo nl2br($layamthuc2['mo_ta_ngan']); ?></p>
                    <?php if($bai_viet_id): ?>
                    <button class="xemthem" onclick="window.location.href='detailed_explore.php?id=<?php echo $khampha_id; ?>'">Xem thêm</button>
                    <?php else: ?>
                    <button class="xemthem" disabled>Chưa có bài viết</button>
                    <?php endif; ?>
                </div>
                <div class="ex_all_img">
                    <?php
                    $sql_anh = "SELECT * FROM khampha_anh WHERE khampha_id = $khampha_id";
                    $layanhamthuc = mysqli_query($conn, $sql_anh);
                    while ($layanhamthuc2 = mysqli_fetch_assoc($layanhamthuc)) {
                    ?>
                    <div class="box rolling-ball1">
                        <img src="<?php echo "../" . $layanhamthuc2['duong_dan_anh']; ?>" alt="">
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
            <?php } ?>
        </div>
        
        <!-- Phân trang Đặc sản -->
        <?php if($total_pages_dacsan > 1): ?>
        <div class="change-container">
            <button class="change-btn" onclick="window.location.href='?page_langnghe=<?php echo $page_langnghe; ?>&page_dacsan=<?php echo max(1, $page_dacsan - 1); ?>&page_vanhoa=<?php echo $page_vanhoa; ?>#dacsan'" 
                    <?php echo $page_dacsan <= 1 ? 'disabled' : ''; ?>>
                <i class="bi bi-caret-left-fill"></i>
            </button>
            
            <span class="page-info">Trang <?php echo $page_dacsan; ?> / <?php echo $total_pages_dacsan; ?></span>
            
            <button class="change-btn" onclick="window.location.href='?page_langnghe=<?php echo $page_langnghe; ?>&page_dacsan=<?php echo min($total_pages_dacsan, $page_dacsan + 1); ?>&page_vanhoa=<?php echo $page_vanhoa; ?>#dacsan'" 
                    <?php echo $page_dacsan >= $total_pages_dacsan ? 'disabled' : ''; ?>>
                <i class="bi bi-caret-right-fill"></i>
            </button>
        </div>
        <?php endif; ?>
    </div>

    <!-- Văn hóa - Phong tục -->
    <div class="ex_container2" id="vanhoa">
        <h2>Văn hóa - Phong tục</h2>
        <div class="ex_content">
            <?php
            // Đếm tổng số mục
            $count_sql = "SELECT COUNT(*) as total FROM khampha WHERE loai_id = 3 AND trang_thai = 1";
            $count_result = mysqli_query($conn, $count_sql);
            $total_items_vanhoa = mysqli_fetch_assoc($count_result)['total'];
            $total_pages_vanhoa = ceil($total_items_vanhoa / $items_per_page);
            
            $sql = "SELECT k.*, bv.id as bai_viet_id 
                    FROM khampha k 
                    LEFT JOIN bai_viet bv ON k.khampha_id = bv.khampha_id 
                    WHERE k.loai_id = 3 AND k.trang_thai = 1
                    LIMIT $items_per_page OFFSET $offset_vanhoa";
            $layvanhoa = mysqli_query($conn, $sql);
            $k = 0;
            while ($layvanhoa2 = mysqli_fetch_assoc($layvanhoa)) {
                $khampha_id = $layvanhoa2['khampha_id'];
                $bai_viet_id = $layvanhoa2['bai_viet_id'];
                $k++;
                if($k % 2 != 0){
            ?>
            <div class="ex_item">
                <div class="ex_all_img1">
                    <?php
                    $sql_anh = "SELECT * FROM khampha_anh WHERE khampha_id = $khampha_id";
                    $layanhvanhoa = mysqli_query($conn, $sql_anh);
                    while ($layanhvanhoa2 = mysqli_fetch_assoc($layanhvanhoa)) {
                    ?>
                    <div class="ex_all_img2 box fade-left"><img src="<?php echo "../" .$layanhvanhoa2['duong_dan_anh'] ?>" alt=""></div>
                    <?php } ?>
                </div>
                <div class="ex_tt box fade-right">
                    <h2><?php echo $layvanhoa2['tieu_de'] ?></h2>
                    <p><?php echo nl2br($layvanhoa2['mo_ta_ngan']) ?></p>
                    <?php if($bai_viet_id): ?>
                        <button class="xemthem" onclick="window.location.href='detailed_explore.php?id=<?php echo $khampha_id; ?>'">Xem thêm</button>
                    <?php else: ?>
                    <button class="xemthem" disabled>Chưa có bài viết</button>
                    <?php endif; ?>
                </div>
            </div>
            <?php } else { ?>
            <div class="ex_item">
                <div class="ex_tt box fade-left">
                    <h2><?php echo $layvanhoa2['tieu_de'] ?></h2>
                    <p><?php echo nl2br($layvanhoa2['mo_ta_ngan']) ?></p>
                    <?php if($bai_viet_id): ?>
                    <button class="xemthem" onclick="window.location.href='detailed_explore.php?id=<?php echo $khampha_id; ?>'">Xem thêm</button>
                    <?php else: ?>
                    <button class="xemthem" disabled>Chưa có bài viết</button>
                    <?php endif; ?>
                </div>
                <div class="ex_all_img1">
                    <?php
                    $sql_anh = "SELECT * FROM khampha_anh WHERE khampha_id = $khampha_id";
                    $layanhvanhoa = mysqli_query($conn, $sql_anh);
                    while ($layanhvanhoa2 = mysqli_fetch_assoc($layanhvanhoa)) {
                    ?>
                    <div class="ex_all_img2 box fade-right"><img src="<?php echo "../" .$layanhvanhoa2['duong_dan_anh'] ?>" alt=""></div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
            <?php } ?>
        </div>
        
        <!-- Phân trang Văn hóa -->
          <?php if($total_pages_vanhoa > 1): ?>
        <div class="change-container">
            <button class="change-btn" onclick="window.location.href='?page_langnghe=<?php echo $page_langnghe; ?>&page_dacsan=<?php echo $page_dacsan; ?>&page_vanhoa=<?php echo max(1, $page_vanhoa - 1); ?>#vanhoa'" 
                    <?php echo $page_vanhoa <= 1 ? 'disabled' : ''; ?>>
                <i class="bi bi-caret-left-fill"></i>
            </button>
            
            <span class="page-info">Trang <?php echo $page_vanhoa; ?> / <?php echo $total_pages_vanhoa; ?></span>
            
            <button class="change-btn" onclick="window.location.href='?page_langnghe=<?php echo $page_langnghe; ?>&page_dacsan=<?php echo $page_dacsan; ?>&page_vanhoa=<?php echo min($total_pages_vanhoa, $page_vanhoa + 1); ?>#vanhoa'" 
                    <?php echo $page_vanhoa >= $total_pages_vanhoa ? 'disabled' : ''; ?>>
                <i class="bi bi-caret-right-fill"></i>
            </button>
        </div>
        <?php endif; ?>
    </div>

    <?php include '../../../includes/footer.php'; ?>
</body>
<script src="../../../js/Main5.js"></script>
</html>