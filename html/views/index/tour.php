<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour</title>
    <link rel="stylesheet" href="../../../css/Main5.css">
    <style>
        body{background: url('https://images.unsplash.com/photo-1750779941284-09ee2d6a619c?q=80&w=1742&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') no-repeat center center fixed;
  background-size: cover;}
    </style>
</head>
<body>
   <?php include '../../../includes/header.php';?>

       <div class="tour_container">
       <div class="tour_container1 box fade-left">
    <div class="filter-header">
        <h3>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
            Lọc Tour
        </h3>
        <button class="reset-btn">Đặt lại</button>
    </div>

    <!-- Loại Tour -->
    <div class="filter-section">
        <h4>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Loại Tour
        </h4>
        <div class="tour-type-buttons">
            <button class="tour-type-btn active">Tất cả</button>
            <button class="tour-type-btn">Trong Ngày</button>
            <button class="tour-type-btn">Dài Ngày</button>
        </div>
    </div>

    <!-- Địa điểm -->
    <div class="filter-section">
        <h4>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Địa điểm
        </h4>
        <div class="checkbox-list">
            <div class="checkbox-item">
                <input type="checkbox" id="loc-danang">
                <label for="loc-danang">Đà Nẵng</label>
                <span class="count">12</span>
            </div>
            <div class="checkbox-item">
                <input type="checkbox" id="loc-hoian">
                <label for="loc-hoian">Hội An</label>
                <span class="count">8</span>
            </div>
            <div class="checkbox-item">
                <input type="checkbox" id="loc-hue">
                <label for="loc-hue">Huế</label>
                <span class="count">10</span>
            </div>
            <div class="checkbox-item">
                <input type="checkbox" id="loc-quangbinh">
                <label for="loc-quangbinh">Quảng Bình</label>
                <span class="count">6</span>
            </div>
            <div class="checkbox-item">
                <input type="checkbox" id="loc-baclieu">
                <label for="loc-baclieu">Bạch Long Vỹ</label>
                <span class="count">4</span>
            </div>
        </div>
    </div>

    <!-- Khoảng giá -->
    <div class="filter-section">
        <h4>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Khoảng Giá
        </h4>
        <div class="price-range">
            <div class="price-inputs">
                <div class="price-input-group">
                    <label>Tối thiểu</label>
                    <input type="number" placeholder="0" value="0">
                </div>
                <div class="price-input-group">
                    <label>Tối đa</label>
                    <input type="number" placeholder="10,000,000" value="10000000">
                </div>
            </div>
            <div class="range-slider">
                <div class="range-progress"></div>
            </div>
        </div>
    </div>

    <!-- Thời gian -->
    <div class="filter-section">
        <h4>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Thời gian
        </h4>
        <div class="duration-options">
            <button class="duration-btn">1 ngày</button>
            <button class="duration-btn">2-3 ngày</button>
            <button class="duration-btn">4-5 ngày</button>
            <button class="duration-btn">6+ ngày</button>
        </div>
    </div>

    <!-- Đánh giá -->
    <div class="filter-section">
        <h4>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
            </svg>
            Đánh Giá
        </h4>
        <div class="rating-options">
            <label class="rating-item">
                <input type="radio" name="rating" value="5">
                <span class="sosao">5 sao</span>
                <span class="stars">★★★★★</span>
            </label>
            <label class="rating-item">
                <input type="radio" name="rating" value="4">
                <span class="sosao">4 sao trở lên</span>
                <span class="stars">★★★★☆</span>
            </label>
            <label class="rating-item">
                <input type="radio" name="rating" value="3">
                <span class="sosao">3 sao trở lên</span>
                <span class="stars">★★★☆☆</span>
            </label>
        </div>
    </div>

    <!-- Apply Button -->
    <button class="apply-filter-btn">Áp dụng lọc</button>
</div>
        <div class="tour_container2">
            <h1>Tour Ưu Đãi Tốt Nhất Hôm Nay</h1>
        <?php
        include '../../../db/db.php';
        $sql_httour =  "SELECT * FROM TOUR WHERE trang_thai =1";
        $sql_httour = mysqli_query($conn, $sql_httour);
        while($row_httour = mysqli_fetch_array($sql_httour)){
        ?>
            <div class="tour_box box fade-right">
                <div class="tour_box_img">
                    <img src="../../../uploads/<?php echo $row_httour['hinh_anh']; ?>" alt="">
                </div>
                <div class="tour_box_tt">
                   <div class="nameandtime">
                     <div class="name_tour"><?php echo $row_httour['ten_tour']; ?></div>
                     <div class="tour_time">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
</svg>
<p>14-09-2025</p>
                     </div>
                    
                   </div>
                    <div class="cmtandgia">
                        <div class="cmt">
                           <p> <span class="dg">9.2</span><span class="dg2">Tuyệt Vời </span>| 23 Đánh Giá</p>
                        </div>
                        <div class="gia"><?php echo number_format($row_httour['gia'], 0, ',', '.') . " VNĐ"; ?></div>
                     </div>
                    <div class="cmtandgia">
                        <div class="cmt">
                         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg> <p><?php echo $row_httour['so_ngay'] . " Ngày"; ?></p>

                        </div>
                       <button onclick="window.location.href='detailed_tour.php?id=<?php echo $row_httour['id']; ?>'" class="tour_button">Xem Chi Tiết</button>

                     </div>
                     
                </div>
            </div>

        <?php } ?>

           

        </div>
       </div>






     <?php include '../../../includes/footer.php'; ?>
    <script src="../../../js/Main5.js"></script>
</body>
</html>