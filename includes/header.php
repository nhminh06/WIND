   <?php session_start() ?>
   <?php
     if(!empty($_SESSION['username'])){
    
    echo "<style>.hd_lg{display: none;}</style>";
  }else{
    echo "<style>.hd_lg{display: block;}</style>";}
   ?>
  <div class="main_search">
     <div
         class="menusearch">
          <button class="menu-toggle" id="menuToggle">
    <span></span>
    <span></span>
    <span></span>
</button> 

            <div onclick="window.location.href = '../../views/index/WebIndex.php'" class="logo">
                <img src="../../../img/logo.png" alt="">
                <!-- Nút Hamburger Menu -->

            </div>
          
            <ul>
                <li><a href="../../views/index/WebIndex.php">Trang chủ</a></li>
                <li><a href="../../views/index/about.php">Giới thiệu</a></li>
                <li><a href="../../views/index/Explore.php">Khám phá</a></li>
                <li><a href="../../views/index/tour.php">Tour</a></li>
                <li><a href="../../views/index/contact.php">Liên hệ</a></li>
                <li class="hd_lg"><a href="../../views/index/login.php">Đăng nhập</a></li>
            </ul>
            <div class="search">
                <input type="text" placeholder="Tìm kiếm...">
                <button type="submit"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
</svg>
</button>
 <button class="sangtoi" id="sangtoi">
        <svg class="sun" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
</svg>
<svg  class="moon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
</svg>

      </button>
        <div onclick="window.location.href = '../user/users.php'" class="users_avata">
        <img src="https://i.pinimg.com/1200x/ce/5f/d3/ce5fd3590095d2aabe3ad6f6203dfe70.jpg" alt="">
      </div>
            </div>
          
        </div>
        <div class="rbc_menu" id="rbc_menu">
      <ul>
                <li><a href="../../views/index/WebIndex.php">Trang chủ</a></li>
                <li><a href="../../views/index/about.php">Giới thiệu</a></li>
                <li><a href="../../views/index/Explore.php">Khám phá</a></li>
                <li><a href="../../views/index/tour.php">Tour</a></li>
                <li><a href="../../views/index/contact.php">Liên hệ</a></li>
                <li class="hd_lg"><a href="../../views/index/login.php">Đăng nhập</a></li>
            </ul>
            <div class="rpc_search">
              <input type="text">
              <button>Tìm kiếm</button>
              <div class="rcp_avt"><img src="https://i.pinimg.com/1200x/ce/5f/d3/ce5fd3590095d2aabe3ad6f6203dfe70.jpg" alt=""></div>
            </div>
</div>
  </div>