<?php
session_start();
require_once __DIR__ . '/db.php';
global $conn;

$token = $_GET['token'] ?? '';
$error = $success = '';
$validToken = false;

// === KIỂM TRA TOKEN HỢP LỆ ===
if (!$token) {
    $error = 'Thiếu token xác thực';
} else {
    // Xóa token đã hết hạn
    $conn->query("DELETE FROM password_reset_tokens WHERE expires_at < NOW()");
    
    // Kiểm tra token
    $sql = "SELECT * FROM password_reset_tokens WHERE token = ? AND used = 0 AND expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $reset = $result->fetch_assoc();
    
    if (!$reset) {
        $error = 'Liên kết không hợp lệ hoặc đã hết hạn';
    } else {
        $validToken = true;
    }
}

// === XỬ LÝ FORM SUBMIT ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirmPassword'] ?? '';

    if (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự';
    } elseif ($password !== $confirm) {
        $error = 'Mật khẩu xác nhận không khớp';
    } else {
        // Kiểm tra lại token (phòng trường hợp đã dùng)
        $sql = "SELECT * FROM password_reset_tokens WHERE token = ? AND used = 0 AND expires_at > NOW()";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $reset = $result->fetch_assoc();

        if (!$reset) {
            $error = 'Token đã được sử dụng hoặc hết hạn';
        } else {
            // Cập nhật mật khẩu (KHÔNG MÃ HÓA - chỉ để test)
            $sql = "UPDATE user SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $password, $reset['user_id']);
            
            if ($stmt->execute()) {
                // Đánh dấu token đã dùng
                $sql = "UPDATE password_reset_tokens SET used = 1 WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $reset['id']);
                $stmt->execute();

                $success = 'Đặt lại mật khẩu thành công! Đang chuyển về trang đăng nhập...';
                $validToken = false; // Ẩn form
                
                // Redirect sau 3 giây
                header("Refresh: 3; url=../html/views/index/login.php");
            } else {
                $error = 'Lỗi cập nhật mật khẩu. Vui lòng thử lại!';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background:url('https://i.pinimg.com/1200x/a6/87/e9/a687e98ee2f422a4967fe56fd9f0e9ba.jpg') no-repeat center center fixed;
             background-size: cover; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
        }
        .card { 
              background-color: #ffffff20; 
    backdrop-filter: blur(8px); 
            padding: 40px; 
            border-radius: 16px; 
            box-shadow: 0 20px 60px rgba(0,0,0,0.3); 
            width: 90%; 
            max-width: 420px; 
            text-align: center;
            animation: slideUp 0.5s ease;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h1 { 
            color: #ffffffff; 
            margin-bottom: 10px; 
            font-size: 28px; 
        }
        i{
            color: #ddd;
        }
        p { 
            color: #ffffffff; 
            margin-bottom: 25px; 
            font-size: 14px; 
        }
        input { 
            width: 100%; 
            padding: 14px; 
            margin: 10px 0; 
            border: 2px solid #ddd; 
            border-radius: 8px; 
            font-size: 15px;
            transition: border-color 0.3s;
        }
        input:focus { 
            outline: none; 
            border-color: #667eea; 
        }
        button { 
            width: 100%; 
            padding: 14px; 
            background: linear-gradient(135deg, #66eac9ff 0%, #3be897ff 100%);
            color: white; 
            border: none; 
            border-radius: 8px; 
            font-weight: bold; 
            cursor: pointer; 
            margin-top: 10px;
            transition: transform 0.2s;
        }
        button:hover { 
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        button:active {
            transform: translateY(0);
        }
        .error { 
            background: rgba(255, 255, 255, 1); 
            color: #c33; 
            padding: 12px; 
            border-radius: 8px; 
            margin: 15px 0;
        }
        .success { 
            background: #ffffffff; 
            color: #2fa94bff; 
            padding: 12px; 
            border-radius: 8px; 
            margin: 15px 0;
        }
        .back { 
            margin-top: 20px; 
        }
        .back a { 
            color: #ffffffff; 
            text-decoration: none; 
            font-size: 14px;
            transition: color 0.3s;
        }
        .back a:hover {
            color: #e42b2bff;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .password-strength {
            height: 4px;
            background: #ddd;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }
        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s, background 0.3s;
        }
    </style>
</head>
<body>
    <div class="card">
        <?php if ($error): ?>
            <div class="icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <h1>Có lỗi xảy ra</h1>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="icon"><i class="bi bi-check-lg"></i></div>
            <h1>Thành công!</h1>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php elseif ($validToken): ?>
            <div class="icon"><i class="bi bi-shield-lock-fill"></i></div>
            <h1>Đặt lại mật khẩu</h1>
            <p>Nhập mật khẩu mới cho tài khoản của bạn</p>
            
            <form method="POST" id="resetForm">
                <input 
                    type="password" 
                    name="password" 
                    id="password"
                    placeholder="Mật khẩu mới (tối thiểu 6 ký tự)" 
                    required
                    minlength="6"
                >
                <div class="password-strength">
                    <div class="password-strength-bar" id="strengthBar"></div>
                </div>
                
                <input 
                    type="password" 
                    name="confirmPassword" 
                    id="confirmPassword"
                    placeholder="Xác nhận mật khẩu" 
                    required
                >
                <button type="submit">Cập nhật mật khẩu</button>
            </form>
        <?php endif; ?>

        <div class="back">
            <a href="../html/views/index/login.php">← Quay lại đăng nhập</a>
        </div>
    </div>

    <script>
        const password = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');
        
        if (password) {
            password.addEventListener('input', function() {
                const val = this.value;
                let strength = 0;
                
                if (val.length >= 6) strength += 25;
                if (val.length >= 8) strength += 25;
                if (/[A-Z]/.test(val)) strength += 25;
                if (/[0-9]/.test(val)) strength += 25;
                
                strengthBar.style.width = strength + '%';
                
                if (strength <= 25) {
                    strengthBar.style.background = '#e74c3c';
                } else if (strength <= 50) {
                    strengthBar.style.background = '#f39c12';
                } else if (strength <= 75) {
                    strengthBar.style.background = '#3498db';
                } else {
                    strengthBar.style.background = '#27ae60';
                }
            });
        }
        
        // Validate trước khi submit
        const form = document.getElementById('resetForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const pwd = document.getElementById('password').value;
                const confirm = document.getElementById('confirmPassword').value;
                
                if (pwd !== confirm) {
                    e.preventDefault();
                    alert('Mật khẩu xác nhận không khớp!');
                    return false;
                }
                
                if (pwd.length < 6) {
                    e.preventDefault();
                    alert('Mật khẩu phải có ít nhất 6 ký tự!');
                    return false;
                }
            });
        }
    </script>
</body>
</html>