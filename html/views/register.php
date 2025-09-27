<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="../../css/Main5.css">
</head>
<body>
    <div class="bannervd">
        <video autoplay muted loop playsinline disablePictureInPicture>
            <source src="../../Video/resgir.mp4">
        </video>
       <form action="../../php/RegisterController.php" method="POST" onsubmit="return xacnhan();">
         <div class="register_font">
            <h1>Đăng ký</h1>
            <p id="textten">Tên đăng nhập:</p>
            <div class="nhaplieu">
                <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập">
            </div>
            <p id="textemail">Nhập email:</p>
            <div class="nhaplieu">
                <input type="email" id="email" name="email" placeholder="Nhập email">
            </div>
            <p id="textpw">Mật khẩu:</p>
            <div class="nhaplieu">
                <input type="password" id="password" name="password" placeholder="Nhập mật khẩu">
            </div>
            <p id="textpw2">Xác nhận mật khẩu:</p>
            <div class="nhaplieu">
                <input type="password" id="confirmPassword" placeholder="Xác nhận mật khẩu">
            </div>
            <div class="nhaplieu">
                <button onclick="xacnhan()" id="registerButton">Đăng ký</button>
            </div>
          
        </div>
       </form>
    <script src="../../js/Main5.js"></script>
</body>
</html>