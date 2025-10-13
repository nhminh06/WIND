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
            <h3>Tour HOT Nước Ngoài</h3>
            <ul>
               <li>Nhật Bản</li>
                <li>Hoa Kỳ</li>
                <li>Pháp</li>
                <li>Trung Quốc</li>
                <li>Hàn Quốc</li>
                <li>Đức</li>
                <li>Ý</li>
                <li>Canada</li>
                <li>Anh</li>
                <li>Úc</li>

            </ul>
            <h3>Tour HOT Trong Nước</h3>
            <ul>
              <li>Quảng Ninh</li>
                <li>Quảng Nam</li>
                <li>Đà Nẵng</li>
                <li>Lâm Đồng</li>
                <li>Lào Cai</li>
                <li>Thừa Thiên Huế</li>
                <li>TP Hồ Chí Minh</li>
                <li>Hà Nội</li>
                <li>Khánh Hòa</li>
                <li>Bình Thuận</li>


            </ul>
            <h3>Loại Tuor </h3>
            <ul>
              <li>Tour Trọn Gió</li>
                <li>Tour Trong Ngày </li>
                <li>Tour Siêu Du Thuyền</li>

              
               


            </ul>
                            <h3>Tour Theo Chủ Đề</h3><ul>
                <li>Tour Ưu Đãi Tốt Nhất Hôm Nay</li>
                <li>Tour Du Lịch Đông Tây Bắc</li>
                <li>Tour Du Lịch Trải Nghiệm</li>
                <li>Tour Du Lịch Độc Đáo</li>
                <li>Tour Du Lịch Nước Ngoài Cao Cấp</li>
                <li>Tour Du Lịch Nội Địa</li>
                <li>Tour Du Lịch Miền Trung</li>
                <li>Tour Du Lịch Siêu Du Thuyền 5 Sao</li>
                <li>Tour Du Lịch Bằng Xe Lửa</li>
                <li>Tour Du Lịch Nhật Bản - Hàn Quốc</li>
                </ul>

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
                     <div class="name_tour"><?php echo $row_httour['ten_tour'] . " " . $row_httour['so_ngay'] ?></div>
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
</svg> <p><?php echo $row_httour['so_ngay']; ?></p>

                        </div>
                        <button onclick="window.location.href = 'detailed_tour.php'" class="tour_button">Xem Chi Tiết</button>
                     </div>
                     
                </div>
            </div>
<<<<<<< HEAD
          <div class="tour_box box fade-right">
    <div class="tour_box_img">
        <img src="../../../img/phongcanh2.png" alt="">
    </div>
    <div class="tour_box_tt">
        <div class="nameandtime">
            <div class="name_tour">HÀ NỘI – HỒ HOÀN KIẾM – 36 PHỐ PHƯỜNG (1 ngày)</div>
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
            <div class="gia">2.200.000 VNĐ</div>
        </div>
        <div class="cmtandgia">
            <div class="cmt">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <p>1 ngày</p>
            </div>
            <button class="tour_button">Xem Chi Tiết</button>
        </div>
    </div>
</div>

            <div class="tour_box box fade-right">
                <div class="tour_box_img">
                    <img src="../../../img/phongcanh3.png" alt="">
                </div>
                <div class="tour_box_tt">
                   <div class="nameandtime">
                     <div class="name_tour">SAPA – BẢN CÁT CÁT, NÚI HÀM RỒNG</div>
                     <div class="tour_time">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
</svg>
<p>14-08-2025</p>
                     </div>
                    
                   </div>
                    <div class="cmtandgia">
                        <div class="cmt">
                           <p> <span class="dg">8.2</span><span class="dg2">Tuyệt Vời </span>| 23 Đánh Giá</p>
                        </div>
                        <div class="gia">2.800.000 VNĐ</div>
                     </div>
                    <div class="cmtandgia">
                        <div class="cmt">
                         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg> <p>2 ngày</p>

                        </div>
                        <button class="tour_button">Xem Chi Tiết</button>
                     </div>
                     
                </div>
            </div>
            <div class="tour_box box fade-right">
                <div class="tour_box_img">
                    <img src="../../../img/phongcanh4.png" alt="">
                </div>
                <div class="tour_box_tt">
                   <div class="nameandtime">
                     <div class="name_tour">ĐÀ NẴNG – Bà Nà Hills, Cầu Vàng, Biển Mỹ Khê</div>
                     <div class="tour_time">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
</svg>
<p>10-09-2025</p>
                     </div>
                    
                   </div>
                    <div class="cmtandgia">
                        <div class="cmt">
                           <p> <span class="dg">9.5</span><span class="dg2">Tuyệt Vời </span>| 23 Đánh Giá</p>
                        </div>
                        <div class="gia">2.800.000 VNĐ</div>
                     </div>
                    <div class="cmtandgia">
                        <div class="cmt">
                         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg> <p>2 ngày</p>

                        </div>
                        <button class="tour_button">Xem Chi Tiết</button>
                     </div>
                     
                </div>
            </div>
            <div class="tour_box box fade-right">
                <div class="tour_box_img">
                    <img src="../../../img/phongcanh5.png" alt="">
                </div>
                <div class="tour_box_tt">
                   <div class="nameandtime">
                     <div class="name_tour">ĐÀ LẠT – Thung Lũng Tình Yêu, Đồi Chè Cầu Đất, Que Garden</div>
                     <div class="tour_time">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
</svg>
<p>14-10-2025</p>
                     </div>
                    
                   </div>
                    <div class="cmtandgia">
                        <div class="cmt">
                           <p> <span class="dg">9.2</span><span class="dg2">Tuyệt Vời </span>| 23 Đánh Giá</p>
                        </div>
                        <div class="gia">2.800.000 VNĐ</div>
                     </div>
                    <div class="cmtandgia">
                        <div class="cmt">
                         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg> <p>3 ngày</p>

                        </div>
                        <button class="tour_button">Xem Chi Tiết</button>
                     </div>
                     
                </div>
            </div>
            <div class="tour_box box fade-right">
                <div class="tour_box_img">
                    <img src="../../../img/phongcanh6.png" alt="">
                </div>
                <div class="tour_box_tt">
                   <div class="nameandtime">
                     <div class="name_tour">NHA TRANG – Đảo Yến, VinWonders, Tháp Bà Ponagar</div>
                     <div class="tour_time">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
</svg>
<p>07-09-2025</p>
                     </div>
                    
                   </div>
                    <div class="cmtandgia">
                        <div class="cmt">
                           <p> <span class="dg">8.0</span><span class="dg2">Tuyệt Vời </span>| 23 Đánh Giá</p>
                        </div>
                        <div class="gia">2.900.000 VNĐ</div>
                     </div>
                    <div class="cmtandgia">
                        <div class="cmt">
                         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg> <p>3 ngày</p>

                        </div>
                     </div>
                     
                </div>
            </div>
            <div class="tour_box box fade-right">
                <div class="tour_box_img">
                    <img src="../../../img/phongcanh7.png" alt="">
                </div>
                <div class="tour_box_tt">
                   <div class="nameandtime">
                     <div class="name_tour">QUY NHƠN – Kỳ Co, Eo Gió, Hòn Khô</div>
                     <div class="tour_time">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
</svg>
<p>14-08-2025</p>
                     </div>
                    
                   </div>
                    <div class="cmtandgia">
                        <div class="cmt">
                           <p> <span class="dg">9.2</span><span class="dg2">Tuyệt Vời </span>| 23 Đánh Giá</p>
                        </div>
                        <div class="gia">2.950.000 VNĐ</div>
                     </div>
                    <div class="cmtandgia">
                        <div class="cmt">
                         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg> <p>3 ngày</p>

                        </div>
                        <button class="tour_button">Xem Chi Tiết</button>
                     </div>
                     
                </div>
            </div>
            <div class="tour_box box fade-right">
                <div class="tour_box_img">
                    <img src="../../../img/phongcanh8.png" alt="">
                </div>
                <div class="tour_box_tt">
                   <div class="nameandtime">
                     <div class="name_tour">TP. HỒ CHÍ MINH – Dinh Độc Lập, Nhà Thờ Đức Bà, Bến Nhà Rồng</div>
                     <div class="tour_time">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
</svg>
<p>11-09-2025</p>
                     </div>
                    
                   </div>
                    <div class="cmtandgia">
                        <div class="cmt">
                           <p> <span class="dg">9.3</span><span class="dg2">Tuyệt Vời </span>| 23 Đánh Giá</p>
                        </div>
                        <div class="gia">1.200.000 VNĐ</div>
                     </div>
                    <div class="cmtandgia">
                        <div class="cmt">
                         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg> <p>1 ngày</p>

                        </div>
                        <button class="tour_button">Xem Chi Tiết</button>
                     </div>
                     
                </div>
            </div>
            <div class="tour_box box fade-right">
                <div class="tour_box_img">
                    <img src="../../../img/phongcanh9.png" alt="">
                </div>
                <div class="tour_box_tt">
                   <div class="nameandtime">
                     <div class="name_tour">HUẾ – Đại Nội, Chùa Thiên Mụ, Lăng Khải Định</div>
                     <div class="tour_time">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
</svg>
<p>14-08-2025</p>
                     </div>
                    
                   </div>
                    <div class="cmtandgia">
                        <div class="cmt">
                           <p> <span class="dg">9.2</span><span class="dg2">Tuyệt Vời </span>| 23 Đánh Giá</p>
                        </div>
                        <div class="gia">2.400.000 VNĐ</div>
                     </div>
                    <div class="cmtandgia">
                        <div class="cmt">
                         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg> <p>2 ngày</p>

                        </div>
                        <button class="tour_button">Xem Chi Tiết</button>
                     </div>
                     
                </div>
            </div>
=======
        <?php } ?>
                 
>>>>>>> d704c10f6c1a989e1e701e36b799e8d2a9000a9b
           

        </div>
       </div>






     <?php include '../../../includes/footer.php'; ?>
    <script src="../../../js/Main5.js"></script>
</body>
</html>