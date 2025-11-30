<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết</title>
    <link rel="stylesheet" href="../../../css/Main5.css">
    <style>
         body{background: url('https://images.unsplash.com/photo-1739219959019-dd317f76c7e8?q=80&w=1716&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') no-repeat center center fixed;
  background-size: cover;}
    </style>
</head>
<body>
   <?php include '../../../includes/header.php';?>

          <?php 
          $matour = $_GET['id'];

          if($matour == ''){
            header("Location: empty.php");
            exit();

          }
          ?>
        <div class="detailed_tour_container">
          <h1><?php
          include '../../../db/db.php';
          $tentour = "SELECT * FROM tour WHERE id= $matour";
          $rltentour = mysqli_query($conn, $tentour);
          $laytentour = mysqli_fetch_assoc($rltentour);
          echo $laytentour['ten_tour']; ?></h1>
          <div class="cmt">
                           <p> <span class="dg">9.2</span><span class="dg2">Tuyệt Vời </span>| 23 Đánh Giá</p>
                        </div>
                        <div class="main_detailed">
                          <div class="detailed_img box fade-up">
                            <div class="detailed_br">
                              <div class="review_detailed_img">
                                 <button class="bttr">
            <svg style="color: aliceblue;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
</svg>

        </button>
                                <div class="inner4">
                                  <?php
                                  include '../../../db/db.php';
                                  $hinhanh = "SELECT * FROM tour_anh WHERE tour_id= $matour";
                                  $rlhinhanh = mysqli_query($conn, $hinhanh);
                                  while($layhinhanh = mysqli_fetch_assoc($rlhinhanh)){?>
                                  <img src="<?php echo  "../../../uploads/" .$layhinhanh['hinh_anh'] ?>" alt="">
                                  <?php }?>
                                </div>
                                      <button class="btp">
            <svg style="color: aliceblue;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
</svg>

        </button>
                              </div>
                          <div class="main_slide_detailed">
                              <div class="slide_detailed">
                               <?php
                                  include '../../../db/db.php';
                                  $hinhanh = "SELECT * FROM tour_anh WHERE tour_id= $matour";
                                  $rlhinhanh = mysqli_query($conn, $hinhanh);
                                  while($layhinhanh = mysqli_fetch_assoc($rlhinhanh)){?>
                                  <img src="<?php echo  "../../../uploads/" .$layhinhanh['hinh_anh'] ?>" alt="">
                                  <?php }?>
                      
                              <div class="img_bd"></div>
                            </div>
                            </div>
                          </div>
                            <div class="detailed_dichvu">
                         <div class="top_dichvu">
                           <div class="diadiem">
                            <span><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
</svg>     <?php
                            include '../../../db/db.php';
                            $sql_tct = "SELECT * FROM tour_chi_tiet WHERE tour_id = $matour";
                            $result_tct = mysqli_query($conn, $sql_tct);
                            $tct = mysqli_fetch_assoc($result_tct);
                            echo "<p>Khởi hành từ: </p> <strong>" . $tct['diem_khoi_hanh'] . "</strong></span>"
                            ?>
                          
                          </div>
                          <div class="phuongtien">
                            <span>
                              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-airplane" viewBox="0 0 16 16">
  <path d="M6.428 1.151C6.708.591 7.213 0 8 0s1.292.592 1.572 1.151C9.861 1.73 10 2.431 10 3v3.691l5.17 2.585a1.5 1.5 0 0 1 .83 1.342V12a.5.5 0 0 1-.582.493l-5.507-.918-.375 2.253 1.318 1.318A.5.5 0 0 1 10.5 16h-5a.5.5 0 0 1-.354-.854l1.319-1.318-.376-2.253-5.507.918A.5.5 0 0 1 0 12v-1.382a1.5 1.5 0 0 1 .83-1.342L6 6.691V3c0-.568.14-1.271.428-1.849m.894.448C7.111 2.02 7 2.569 7 3v4a.5.5 0 0 1-.276.447l-5.448 2.724a.5.5 0 0 0-.276.447v.792l5.418-.903a.5.5 0 0 1 .575.41l.5 3a.5.5 0 0 1-.14.437L6.708 15h2.586l-.647-.646a.5.5 0 0 1-.14-.436l.5-3a.5.5 0 0 1 .576-.411L15 11.41v-.792a.5.5 0 0 0-.276-.447L9.276 7.447A.5.5 0 0 1 9 7V3c0-.432-.11-.979-.322-1.401C8.458 1.159 8.213 1 8 1s-.458.158-.678.599"/>
</svg>
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-car-front-fill" viewBox="0 0 16 16">
  <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM3 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2m10 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM2.906 5.189a.51.51 0 0 0 .497.731c.91-.073 3.35-.17 4.597-.17s3.688.097 4.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 11.691 3H4.309a.5.5 0 0 0-.447.276L2.906 5.19Z"/>
</svg>
                            </span>
                          </div>
                          <div class="matour">
                            <?php
                            include '../../../db/db.php';
                            $sql_tct = "SELECT * FROM tour_chi_tiet WHERE tour_id = $matour";
                            $result_tct = mysqli_query($conn, $sql_tct);
                            $tct = mysqli_fetch_assoc($result_tct);
                            echo "<span>Mã Tour: <strong>" . $tct['ma_tour'] . "</strong></span>"
                            ?>
                          </div>
                         </div>
                          <hr>
                          <h2>Tour Trọn Gói bao gồm</h2>
                   <ul>
<?php
    $sql_dv = "SELECT ten_dich_vu FROM dich_vu WHERE tour_id = '$matour'";
    $result_dv = mysqli_query($conn, $sql_dv);
    while ($dv = mysqli_fetch_assoc($result_dv)) {
        echo "<li>" . htmlspecialchars($dv['ten_dich_vu']) . "</li>";
    }
?>
</ul>

                        </div>
                        <div class="detailed_dichvu_s2">
                          
                        <h2>Trải nghiệm thú vị trong tour</h2>
<ul>
<?php
    $sql_tn = "SELECT noi_dung FROM trai_nghiem WHERE tour_id = '$matour'";
    $result_tn = mysqli_query($conn, $sql_tn);
    while ($tn = mysqli_fetch_assoc($result_tn)) {
        echo "<li>" . htmlspecialchars($tn['noi_dung']) . "</li>";
    }
?>
</ul>

                        </div>  
                        <div class="detailed_dichvu_s2">
                            <h2>Chương trình tour</h2>
                            <div class="detailed_item_list">
                            
                            
                         <?php
                         include '../../../db/db.php';
                          $sql_lt = "SELECT * FROM lich_trinh WHERE tour_id= $matour";
                          $result_lt = mysqli_query($conn, $sql_lt);
                          while($row_lt = mysqli_fetch_assoc($result_lt)){?>
                            <div class="detailed_item">
                                <div class="detailed_item_img"><img src="<?php echo "../../../uploads/" . $row_lt['hinh_anh']; ?>" alt="">
                               
                                </div>
                                <div class="detailed_item_tt">
                                   <p><?php echo "Ngày: " . $row_lt['ngay']; ?></p>
                                <p><?php echo  $row_lt['noi_dung']; ?></p>
                                </div>
                              </div> 
                          <?php } ?>
                         
                            
                              
                            </div>
                            
                        </div>
                       <div class="detailed_dichvu_s2">
                              <h2>Lưu ý chi tiết</h2>
                              <h3>Di chuyển</h3>
                                        <ul>
                                          <li>Vé máy bay khứ hồi thường bao gồm 7kg hành lý xách tay và 10kg hành lý ký gửi. Vui lòng kiểm tra kỹ thông tin vé trước khi khởi hành.</li>
                                          <li>Phương tiện di chuyển trong tour sẽ được bố trí theo chương trình, bao gồm xe đưa đón và tàu du lịch nếu có.</li>
                                          <li>Chi phí cầu đường, bến bãi và bảo hiểm vận chuyển đã được tính trong giá tour.</li>
                                        </ul>

                                        <h3>Lưu trú</h3>
                                        <ul>
                                          <li>Tour trong ngày không bao gồm lưu trú qua đêm. Nếu có nhu cầu nghỉ lại, vui lòng liên hệ để được tư vấn thêm.</li>
                                          <li>Điểm nghỉ ngơi có thể là tàu du lịch chất lượng hoặc nhà hàng nổi, tùy theo lịch trình cụ thể.</li>
                                        </ul>

                                        <h3>Khác</h3>
                                        <ul>
                                          <li>Bữa ăn chính thường là hải sản, phục vụ trên tàu hoặc tại nhà hàng địa phương. Vui lòng thông báo trước nếu có yêu cầu đặc biệt về chế độ ăn.</li>
                                          <li>Vé tham quan các điểm du lịch nổi bật đã bao gồm trong giá tour (ví dụ: Vịnh Hạ Long, Hang Sửng Sốt, Đảo Titop).</li>
                                          <li>Hướng dẫn viên chuyên nghiệp sẽ đồng hành cùng đoàn để hỗ trợ và cung cấp thông tin trong suốt hành trình.</li>
                                          <li>Quà tặng du lịch như nón và bao hộ chiếu sẽ được phát cho khách tham gia tour.</li>
                                          <li>Bảo hiểm du lịch được áp dụng cho toàn bộ hành trình, với mức đền bù tối đa 10.000 USD/trường hợp.</li>
                                        </ul>
                            </div>
                              <div class="detailed_dichvu_s2">
                                <h3>Đánh giá của khác hàng</h3>
                                <div class="cmt_main">
                                  
                                 <?php
                                 include '../../../db/db.php';
                                 $danhgia ="SELECT * FROM danh_gia WHERE tour_id= 1";
                                  $resultdanhgia = mysqli_query($conn, $danhgia);
                                  while($rowdanhgia = mysqli_fetch_assoc($resultdanhgia)){?>
                                  
                                  <div class="cmt_item">
                                    <div class="cmt_item_avata">
                                      <img src="<?php echo $rowdanhgia['hinh_anh']; ?>" alt="">
                                      <div class="cmt_item_name">
                                        <p><?php echo $rowdanhgia['ten_khach_hang']; ?></p>
                                      </div>
                                    </div>
                                    <div class="cmt_item_main">
                                       <p> <span class="dg"><?php echo $rowdanhgia['diem']; ?></span><span class="dg2">Tuyệt Vời </span></p>
                                    </div>
                                    <div class="cmt_item_text">
                                     <p><?php echo $rowdanhgia['nhan_xet']; ?></p>
                                    </div>
                                  </div>
                                  <?php }?>
                                 
                                </div>
                              </div>
                          </div>
                          
                          <div class="font_detailed">
                            <div class="tour-price-box box fade-right">
  <h2>Lịch Trình và Giá Tour</h2>
  <p>Chọn Lịch Trình và Xem Giá:</p>

  <div class="date-buttons">
    <button class="active">09/08</button>
    <button>21/08</button>
    <button>21/09</button>
    <button><div><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar3" viewBox="0 0 16 16">
  <path d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857z"/>
  <path d="M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
</svg></div></button>
    <button>Tất cả</button>
  </div>

<?php
include '../../../db/db.php';
$sql_gia = "SELECT * FROM lich_khoi_hanh WHERE tour_id = $matour";
$result_gia = mysqli_query($conn, $sql_gia);
if (mysqli_num_rows($result_gia) > 0) {
   $row_gia = mysqli_fetch_assoc($result_gia);
?> 

  <div class="quantity-row" data-gia="<?php echo $row_gia['gia_nguoi_lon']; ?>">
    <div class="label">
      <strong>Người lớn</strong><br><small>&gt; 9 tuổi</small>
    </div>
    <div class="price"></div>
    <div class="counter">
      <button class="tru">-</button>
      <span>0</span>
      <button class="cong">+</button>
    </div>
  </div>

  <div class="quantity-row" data-gia="<?php echo $row_gia['gia_tre_em']; ?>">
    <div class="label">
      <strong>Trẻ em</strong><br><small>2 - 9 tuổi</small>
    </div>
    <div class="price"></div>
    <div class="counter">
      <button class="tru">-</button>
      <span>0</span>
      <button class="cong">+</button>
    </div>
  </div>

  <div class="quantity-row" data-gia="<?php echo $row_gia['gia_tre_nho']; ?>">
    <div class="label">
      <strong>Trẻ nhỏ</strong><br><small>&lt; 2 tuổi</small>
    </div>
    <div class="price"></div>
    <div class="counter">
      <button class="tru">-</button>
      <span>0</span>
      <button class="cong">+</button>
    </div>
  </div>

<?php } ?>

  <div class="note">
     <div class="isvg"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-lg" viewBox="0 0 16 16">
  <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
</svg></div> Liên hệ để xác nhận chỗ
  </div>

  <div class="price-summary">
    <div class="original">Giá gốc <span class="strike">0 VND</span></div>
    <div class="total">Tổng Giá Tour:  <span class="highlight">0 VND</span></div>
  </div>

  <button class="submit-button">Yêu cầu đặt</button>
</div>

                          </div>
                        </div>
<h2>Các tour gợi ý</h2>
                      <div class="proposal box fade-up">
                          
                         <?php
    include '../../../db/db.php';
    $sql2 = "SELECT * FROM tour WHERE trang_thai = 1 AND loai_banner = 1";
    $result2 = mysqli_query($conn, $sql2);
    $h = 0;
    while ($row2 = mysqli_fetch_assoc($result2)) {
      $h++; if ($h > 3 ) break;
      ?>
  
          <div class="tour_item">
        <img src="<?php echo "../../../uploads/" . $row2['hinh_anh']; ?>" alt="">
        <div class="thongtin">
             <h2><?php echo $row2['ten_tour']; ?></h2>
        <p><?php echo $row2['so_ngay']; ?> | <?php echo number_format($row2['gia'], 0, ',', '.'); ?> VNĐ</p>
            <button class="chitiet">Xem chi tiết</button>
        </div>
       </div>


    <?php } ?>           
                      </div>


        </div>




        <?php include '../../../includes/footer.php';?>
    <script src="../../../js/Main5.js"></script>
</body>
</html>