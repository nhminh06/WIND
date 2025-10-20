<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khám phá</title>
    <link rel="stylesheet" href="../../../css/Main5.css">
    <link rel="stylesheet" href="../../../css/Main5_1.css">
      <link href="https://fonts.googleapis.com/css2?family=Sarina&display=swap" rel="stylesheet">
    <style>
      body{background: url('https://images.pexels.com/photos/8892/pexels-photo.jpg?_gl=1*h8o504*_ga*MTY1MzgzNDc3Ni4xNzUyMTU3Nzk0*_ga_8JE65Q40S6*czE3NjA2NzE4MDQkbzUkZzEkdDE3NjA2NzIwNTkkajYwJGwwJGgw') no-repeat center center fixed;
  background-size: cover;}

    </style>
</head>
<body>
    <?php
    include '../../../includes/header.php';
    ?>
    <div class="about_container">
       <div class="about_top">
        <h4>Khám Phá Miền Trung</h4>
        <h5>Hành trình tìm về phong tục, văn hóa, ẩm thực và phong cảnh</h5>
        <img src="../../../img/logo.png" alt="">
       </div> 
    </div>
    <div class="ex_container">
        <h2>Làng nghề truyền thống</h2>
        
        <div class="ex_content">
         <?php 
         include '../../../db/db.php';
         $sql = "SELECT * FROM khampha WHERE loai_id= 1";
         $laylangnghe = mysqli_query($conn, $sql);
         $i = 0;
          while($laylangnghe2 = mysqli_fetch_assoc($laylangnghe )){
             $khampha_id = $laylangnghe2['khampha_id'];
               $i++;
  if($i%2!=0){
         ?>
            <div class="ex_item">
               <div class="ex_all_img">
               <?php
                 include '../../../db/db.php';
         $sql = "SELECT * FROM khampha_anh WHERE khampha_id= $khampha_id";
         $layanhlangnghe = mysqli_query($conn, $sql);
          while($layanhlangnghe2 = mysqli_fetch_assoc($layanhlangnghe )){
               ?> <div class="ex_img box fade-up"><img src="<?php echo $layanhlangnghe2['duong_dan_anh'] ?>" alt=""></div> <?php } ?>
          

               </div>
               <div class="ex_tt box fade-right">
                <h2><?php echo $laylangnghe2['tieu_de'] ?></h2>
                <p><?php echo $laylangnghe2['mo_ta_ngan'] ?></p>
<button class="xemthem">Xem thêm</button>
</div>

               
    </div><?php }else{ ?>
        <div class="ex_item">
             
               <div class="ex_tt box fade-left">
                <h2><?php echo $laylangnghe2['tieu_de'] ?></h2>
                <p><?php echo $laylangnghe2['mo_ta_ngan'] ?></p>
<button class="xemthem">Xem thêm</button>
</div>
  <div class="ex_all_img">
               <?php
                 include '../../../db/db.php';
         $sql = "SELECT * FROM khampha_anh WHERE khampha_id= $khampha_id";
         $layanhlangnghe = mysqli_query($conn, $sql);
          while($layanhlangnghe2 = mysqli_fetch_assoc($layanhlangnghe )){
               ?> <div class="ex_img box fade-up"><img src="<?php echo $layanhlangnghe2['duong_dan_anh'] ?>" alt=""></div> <?php } ?>
          

               </div>
               
    </div>
      <?php } ?>
         <?php } ?>
            </div>
<div class="ex_container2">
  <h2>Đặc sản địa phương</h2>
    <div class="ex_content">
       
       <?php
include '../../../db/db.php';
$sql = "SELECT * FROM khampha WHERE loai_id = 2";
$layamthuc = mysqli_query($conn, $sql);
$h = 0;
while ($layamthuc2 = mysqli_fetch_assoc($layamthuc)) {
    $khampha_id = $layamthuc2['khampha_id'];
     $h++;
  if($h%2!=0){
?>
    <div class="ex_item">
        <div class="ex_all_img">
            <?php
            $sql_anh = "SELECT * FROM khampha_anh WHERE khampha_id = $khampha_id";
            $layanhamthuc = mysqli_query($conn, $sql_anh);

            while ($layanhamthuc2 = mysqli_fetch_assoc($layanhamthuc)) {
            ?>
                <div class="box rolling-ball">
                    <img src="<?php echo $layanhamthuc2['duong_dan_anh']; ?>" alt="">
                </div>
            <?php } ?>
        </div>

        <div class="ex_tt box fade-left">
            <h2><?php echo $layamthuc2['tieu_de']; ?></h2>
            <p><?php echo $layamthuc2['mo_ta_ngan']; ?></p>
            <button class="xemthem">Xem thêm</button>
        </div>
    </div>
<?php
}else{
?>

 <div class="ex_item">
       

        <div class="ex_tt box fade-right">
            <h2><?php echo $layamthuc2['tieu_de']; ?></h2>
            <p><?php echo $layamthuc2['mo_ta_ngan']; ?></p>
            <button class="xemthem">Xem thêm</button>
        </div>
         <div class="ex_all_img">
            <?php
            $sql_anh = "SELECT * FROM khampha_anh WHERE khampha_id = $khampha_id";
            $layanhamthuc = mysqli_query($conn, $sql_anh);

            while ($layanhamthuc2 = mysqli_fetch_assoc($layanhamthuc)) {
            ?>
                <div class="box rolling-ball1">
                    <img src="<?php echo $layanhamthuc2['duong_dan_anh']; ?>" alt="">
                </div>
            <?php } ?>
        </div>
    </div>

<?php } ?><?php } ?>
               
    </div>
</div>
<div class="ex_container2">
  <h2>Văn hóa - Phong tục</h2>
    <div class="ex_content">
       <?php
include '../../../db/db.php';
$sql = "SELECT * FROM khampha WHERE loai_id = 3";
$layvanhoa = mysqli_query($conn, $sql);
$k = 0;
while ($layvanhoa2 = mysqli_fetch_assoc($layvanhoa)) {
    $khampha_id = $layvanhoa2['khampha_id'];
     $k++;
  if($k%2!=0){
?>
<div class="ex_item">
               <div class="ex_all_img1">
                  <?php
            $sql_anh = "SELECT * FROM khampha_anh WHERE khampha_id = $khampha_id";
            $layanhvanhoa = mysqli_query($conn, $sql_anh);

            while ($layanhvanhoa2 = mysqli_fetch_assoc($layanhvanhoa)) {
            ?>
                 <div class="ex_all_img2 box fade-left"><img src="<?php echo $layanhvanhoa2['duong_dan_anh'] ?>" alt=""></div>
            <?php } ?>
          

               </div>
               <div class="ex_tt box fade-right">
                <h2><?php echo $layvanhoa2['tieu_de'] ?></h2>
              <p><?php echo $layvanhoa2['mo_ta_ngan'] ?></p>

<button class="xemthem">Xem thêm</button>
</div>

               
    </div>
<?php }else{ ?>
  <div class="ex_item">
              
               <div class="ex_tt box fade-left">
                <h2><?php echo $layvanhoa2['tieu_de'] ?></h2>
              <p><?php echo $layvanhoa2['mo_ta_ngan'] ?></p>

<button class="xemthem">Xem thêm</button>
</div>
 <div class="ex_all_img1">
                  <?php
            $sql_anh = "SELECT * FROM khampha_anh WHERE khampha_id = $khampha_id";
            $layanhvanhoa = mysqli_query($conn, $sql_anh);

            while ($layanhvanhoa2 = mysqli_fetch_assoc($layanhvanhoa)) {
            ?>
                 <div class="ex_all_img2 box fade-right"><img src="<?php echo $layanhvanhoa2['duong_dan_anh'] ?>" alt=""></div>
            <?php } ?>
          

               </div>
               
    </div><?php } ?><?php } ?>
    </div>
</div>
  
  

            
    <?php
    include '../../../includes/footer.php';
    ?>
    
</body>
<script src="../../../js/Main5.js"></script>
</html>