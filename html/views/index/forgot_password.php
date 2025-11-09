<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            position: relative;
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .video-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .video-background video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .video-background::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .form-wrapper {
            position: relative;
            z-index: 1;
            width: 90%;
            max-width: 450px;
               background-color: #ffffff20; 
    backdrop-filter: blur(8px); 
            border-radius: 24px;
            padding: 45px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .form-header h1 {
            color: #1a1a1a;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .form-header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #fff;
        }

        .form-group input:focus {
            outline: none;
            border-color: #64b7ebff;
            box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.1);
        }

        .form-group input::placeholder {
            color: #aaa;
        }

        .btn-send-code {
            margin-top: 10px;
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #81ecfb, #00adcc);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-send-code:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(76, 162, 175, 0.4);
        }

        .btn-send-code:disabled {
            background: linear-gradient(135deg, #ccc, #bbb);
            cursor: not-allowed;
            transform: none;
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(44, 62, 80, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .error-message {
            color: #e74c3c;
            font-size: 13px;
            margin-top: 6px;
            display: none;
            animation: shake 0.4s ease;
        }

        .success-message {
            color: #27ae60;
            font-size: 13px;
            margin-top: 6px;
            display: none;
            animation: fadeIn 0.4s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
        }

        .back-link a {
            color: #2c3e50;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: #4ca8afff;
        }

        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e0e0e0, transparent);
            margin: 25px 0;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .form-wrapper {
                padding: 35px 25px;
                width: 95%;
            }

            .form-header h1 {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="video-background">
            <video autoplay muted loop playsinline disablePictureInPicture>
                <source src="../../../Video/resgir.mp4" type="video/mp4">
            </video>
        </div>

        <div class="form-wrapper">
            <div class="form-header">
                <h1>Quên mật khẩu</h1>
                <p>Nhập thông tin để khôi phục tài khoản</p>
            </div>

            <form id="forgotPasswordForm" method="POST" action="../../../php/forgot_passwordController.php">
                <div class="form-group">
                    <label for="email">Địa chỉ Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="example@email.com" 
                        required
                    >
                    <button type="button" class="btn-send-code" id="sendCodeBtn" onclick="guiMaXacNhan()">
                        Gửi mã xác nhận
                    </button>
                    <div class="error-message" id="emailError"></div>
                    <div class="success-message" id="emailSuccess"></div>
                </div>

     
            

               

                <div class="divider"></div>

                <div class="back-link">
                    <a href="login.php">← Quay lại đăng nhập</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        let cooldownTime = 60;
        let cooldownTimer = null;

        // Hàm validate email
        function validateEmail(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        }

        // Hàm gửi mã xác nhận
    function guiMaXacNhan() {
    const email = document.getElementById('email').value.trim();
    const emailError = document.getElementById('emailError');
    const emailSuccess = document.getElementById('emailSuccess');
    const sendCodeBtn = document.getElementById('sendCodeBtn');
    
    // Reset
    emailError.style.display = 'none';
    emailSuccess.style.display = 'none';
    
    if (!email || !validateEmail(email)) {
        emailError.textContent = 'Email không hợp lệ';
        emailError.style.display = 'block';
        return;
    }
    
    sendCodeBtn.disabled = true;
    sendCodeBtn.textContent = 'Đang gửi...';
    
    fetch('/php/send_verification_code.php', {  // SỬA ĐƯỜNG DẪN TẠI ĐÂY
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: email })
    })
    .then(response => {
        if (!response.ok) throw new Error('HTTP ' + response.status);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            emailSuccess.textContent = data.message;
            emailSuccess.style.display = 'block';
            startCooldown();
        } else {
            emailError.textContent = data.message;
            emailError.style.display = 'block';
            sendCodeBtn.disabled = false;
            sendCodeBtn.textContent = 'Gửi mã xác nhận';
        }
    })
    .catch(err => {
        console.error('Lỗi:', err);
        emailError.textContent = 'Lỗi kết nối: ' + err.message;
        emailError.style.display = 'block';
        sendCodeBtn.disabled = false;
        sendCodeBtn.textContent = 'Gửi mã xác nhận';
    });
}

        // Hàm đếm ngược
        function startCooldown() {
            const sendCodeBtn = document.getElementById('sendCodeBtn');
            let timeLeft = cooldownTime;
            
            sendCodeBtn.textContent = `Gửi lại sau ${timeLeft}s`;
            
            cooldownTimer = setInterval(() => {
                timeLeft--;
                sendCodeBtn.textContent = `Gửi lại sau ${timeLeft}s`;
                
                if (timeLeft <= 0) {
                    clearInterval(cooldownTimer);
                    sendCodeBtn.textContent = 'Gửi lại mã';
                    sendCodeBtn.disabled = false;
                }
            }, 1000);
        }

        // Xử lý submit form
       // Thay thế phần xử lý submit form trong file HTML của bạn
document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Reset messages
    document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
    
    const email = document.getElementById('email').value.trim();
    const verificationCode = document.getElementById('verificationCode').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    let isValid = true;
    
    // Validate email
    if (!validateEmail(email)) {
        document.getElementById('emailError').textContent = 'Email không hợp lệ';
        document.getElementById('emailError').style.display = 'block';
        isValid = false;
    }
    
    // Validate code
    if (!verificationCode || verificationCode.length !== 6) {
        document.getElementById('codeError').textContent = 'Mã xác nhận phải có 6 ký tự';
        document.getElementById('codeError').style.display = 'block';
        isValid = false;
    }
    
    // Validate password
    if (password.length < 6) {
        document.getElementById('passwordError').textContent = 'Mật khẩu phải có ít nhất 6 ký tự';
        document.getElementById('passwordError').style.display = 'block';
        isValid = false;
    }
    
    // Validate confirm password
    if (password !== confirmPassword) {
        document.getElementById('confirmPasswordError').textContent = 'Mật khẩu xác nhận không khớp';
        document.getElementById('confirmPasswordError').style.display = 'block';
        isValid = false;
    }
    
    // Submit if valid
    if (isValid) {
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.href = 'login.php';
            } else {
                document.getElementById('codeError').textContent = data.message;
                document.getElementById('codeError').style.display = 'block';
            }
        })
        .catch(error => {
            document.getElementById('codeError').textContent = 'Đã xảy ra lỗi kết nối';
            document.getElementById('codeError').style.display = 'block';
        });
    }
});

        // Auto format verification code
        document.getElementById('verificationCode').addEventListener('input', function(e) {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        });
    </script>
</body>
</html>