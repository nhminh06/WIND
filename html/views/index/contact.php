<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên Hệ</title>
    <link rel="stylesheet" href="../../../css/Main5.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
          body{background: url('https://images.unsplash.com/photo-1750440982726-d723eab666a5?q=80&w=1740&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') no-repeat center center fixed;
  background-size: cover;}

            .type{
              color: white;
            }
            .contact_type{
              display: flex;
              justify-content: space-between;
              padding: 0 5px 10px ;
              font-weight: bold;
            }
    </style>
</head>
<body>
       <?php include '../../../includes/header.php';?>

        <div class="contact_container">
            <h2>Liên hệ với chúng tôi</h2>

            <div class="font_map">
                <div class="contact_font box fade-left">
                   <form action="../../../php/ContactCTL/process_contact.php" method="POST">
                     <div class="contact_main">
                        <h2>Thông tiên liên hệ</h2>
                       
                       <ul>
                        <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
</svg> <p>Địa chỉ: 123 WIND Street, Hà Nội</p>
</li>
                        <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
</svg> <p>Điện thoại: 0909 999 888</p>
</li>
                        <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
</svg> <p>Email: info@windtravel.vn</p>
</li>
                        <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg> 
<p>Giờ làm việc: 08:00 - 17:00 (Thứ 2 - Thứ 7)</p>
</li>
                       </ul>   
                       <input name="name" type="text" placeholder="Tên tài khoản">
                       <input name="email" type="email" placeholder="Nhập vào Email">
                      <textarea name="message" placeholder="Nội dung" id=""></textarea>
                                <div class="contact_type">
              <label  class="type"><input  type="radio" name="type" value="1"> Tôi muốn gửi góp ý</label><br>
              <label  class="type"><input type="radio" name="type" value="2"> Tôi muốn gửi khiếu nại</label>
          </div>

                      <button type="submit">Gửi liên hệ</button>
                    </div>
                   </form>
                </div>
                <div class="map box fade-right"> 
                <div style=" <?php
                        if (isset($_SESSION['error']) && $_SESSION['error'] == 1) {
                            echo "display: flex;";
                            unset($_SESSION['error']);
                        }else {
                            echo "display: none;";
                        }
                        ?>" class= "banacc">
                  <i class="bi bi-exclamation-triangle-fill"></i>
                  <?php
                        if (isset($_SESSION['text_error'])) {
                            echo "<h3>{$_SESSION['text_error']}</h3>";
                            unset($_SESSION['text_error']); // Xóa sau khi hiển thị
                        }else{
                          echo "<h3>Tài khoản của bạn đã bị khóa liên hệ với quản trị viên để biết thêm chi tiết.</h3>";
                        }
                        ?>

                </div>  
                <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.397607469205!2d106.70042357480397!3d10.778673759158203!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f3ed4e4015f%3A0x1d2f6dbf52ec2e78!2zMTIzIMSQLiBUcuG6p24gSMawbmcgxJDDoG8sIFBoxrDhu51uZyA3LCBRdeG6rW4gMSwgSOG7kyBDaMOtbmgsIFZpZXRuYW0!5e0!3m2!1svi!2s!4v1721123456789" 
        width="100%" 
        height="400" 
        style="border:0; border-radius: 15px;"
        allowfullscreen="" 
        loading="lazy" 
        referrerpolicy="no-referrer-when-downgrade">
      </iframe></div>
            </div>
        </div>



       <?php include '../../../includes/footer.php';?>
    <script src="../../../js/Main5.js"></script>
</body>
</html>