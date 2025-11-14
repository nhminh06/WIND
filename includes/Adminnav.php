    <ul class="menu">
      <li><a href="../Admin/Adminacc.php"><i class="bi bi-person-circle"></i>Tài khoản quản trị</a></li>
      <li><a href="../Admin/IndexController.php"><i class="bi bi-back"></i>Quản lý trang chủ</a></li>
      <li><a href="../Admin/TourController.php"><i class="bi bi-image-alt"></i>Quản lý Tour</a></li>
      <li><a href="../Admin/ExploreController.php"><i class="bi bi-cloud-drizzle"></i>Quản lý khám phá</a></li>
      <li><a href="../Admin/ArticleController.php"><i class="bi bi-card-text"></i></i>Quản lý bài viết</a></li>
      <li><a href="../Admin/ContactController.php"><i class="bi bi-envelope"></i>Quản lý thông tin</a></li>
      <li><a href="../Admin/UserController.php"><i class="bi bi-person-workspace"></i>Quản lý người dùng</a></li>
      <li><a href="../Admin/StatisticalController.php"><i class="bi bi-bar-chart"></i>Thống kê</a></li>
      <li><a href="../../php/logout.php"><i class="bi bi-box-arrow-left"></i>Đăng xuất</a></li>
    </ul>
    <div class="thongbao" 
     <?php 
     if(isset($_SESSION['thanhcong'])) { 
         $bgColor = ($_SESSION['thanhcong'] == 1) ? '#4BB543' : '#FF3333';
         echo 'style="display: flex; background-color: ' . $bgColor . ';"'; 
     } else { 
         echo 'style="display: none;"'; 
     } 
     ?>> 
    <?php 
    if(isset($_SESSION['thanhcong'])) {
        if($_SESSION['thanhcong'] == 1) {
            echo '<i class="bi bi-bookmark-check"></i> Chỉnh sửa thành công!';
        } else {
            echo '<i class="bi bi-x-circle"></i> Chỉnh sửa thất bại!';
        }
        unset($_SESSION['thanhcong']);
    }
    ?>
</div>