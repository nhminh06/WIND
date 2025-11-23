<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Người dùng - Admin</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .add-user-form-wrapper {
            background: #26263b;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 100%;
          
        }

        .user-form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .user-form-header h2 {
            margin: 0;
            color: #ffffffff;
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-form-header h2 i {
            color: #667eea;
        }

        .notification-alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .notification-alert.error-type {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .user-input-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .user-field-group {
            display: flex;
            flex-direction: column;
        }

        .user-field-group.wide-field {
            grid-column: span 2;
        }

        .user-field-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #ffffffff;
            font-size: 14px;
        }

        .user-field-group label .star-required {
            color: red;
        }

        .user-field-group input[type="text"],
        .user-field-group input[type="email"],
        .user-field-group input[type="tel"],
        .user-field-group input[type="password"],
        .user-field-group input[type="date"],
        .user-field-group textarea,
        .user-field-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .user-field-group input:focus,
        .user-field-group textarea:focus,
        .user-field-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .user-field-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .user-field-group small {
            color: #ffffffff;
            display: block;
            margin-top: 5px;
            font-size: 13px;
        }

        .status-checkbox-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .status-checkbox-wrap input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .status-checkbox-wrap label {
            margin: 0;
            cursor: pointer;
            font-weight: 500;
        }

        .role-preview-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-top: 8px;
        }

        .role-preview-badge.admin-style { 
            background: #dc3545; 
            color: white; 
        }
        
        .role-preview-badge.staff-style { 
            background: #28a745; 
            color: white; 
        }
        
        .role-preview-badge.user-style { 
            background: #007bff; 
            color: white; 
        }

        .action-btn-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .action-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .action-btn.primary-style {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .action-btn.primary-style:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .action-btn.secondary-style {
            background: #6c757d;
            color: white;
        }

        .action-btn.secondary-style:hover {
            background: #5a6268;
        }

        @media (max-width: 768px) {
            .user-input-grid {
                grid-template-columns: 1fr;
            }
            
            .user-field-group.wide-field {
                grid-column: span 1;
            }

            .action-btn-group {
                flex-direction: column;
            }

            .action-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <h2 class="logo">WIND Admin</h2>
        <?php include '../../includes/Adminnav.php';?>
    </aside>

    <div class="main">
        <header class="header">
            <h1>Thêm Người dùng mới</h1>
            <div class="admin-info">
                <?php echo "<p>Xin chào " . (isset($_SESSION['ho_ten']) ? $_SESSION['ho_ten'] : 'Admin') . "</p>"; ?>
                <button onclick="window.location.href='../views/index/webindex.php'" class="logout">Trở lại</button>
            </div>
        </header>

        <section class="content">
            <!-- Thông báo lỗi -->
            <?php if(isset($_SESSION['error'])): ?>
            <div class="notification-alert error-type">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
                ?>
            </div>
            <?php endif; ?>

            <div class="add-user-form-wrapper">
                <div class="user-form-header">
                    <h2>
                       <i style="color: #fff ;" class="bi bi-person-add"></i></i>
                        Thông tin người dùng
                    </h2>
                </div>

                <form method="POST" action="../../php/UsersController/process_add_user.php" id="addUserForm">
                    <div class="user-input-grid">
                        <!-- Họ tên -->
                        <div class="user-field-group">
                            <label>Họ và tên <span class="star-required">*</span></label>
                            <input type="text" name="ho_ten" required placeholder="Nhập họ và tên đầy đủ">
                            <small>
                                <i class="bi bi-info-circle"></i>
                                Ví dụ: Nguyễn Văn A
                            </small>
                        </div>

                        <!-- Email -->
                        <div class="user-field-group">
                            <label>Email <span class="star-required">*</span></label>
                            <input type="email" name="email" required placeholder="example@email.com">
                            <small>
                                <i class="bi bi-info-circle"></i>
                                Email phải là duy nhất
                            </small>
                        </div>

                        <!-- Số điện thoại -->
                        <div class="user-field-group">
                            <label>Số điện thoại</label>
                            <input type="tel" name="sdt" placeholder="0xxxxxxxxx">
                            <small>
                                <i class="bi bi-info-circle"></i>
                                10 số, bắt đầu bằng 0
                            </small>
                        </div>

                        <!-- Mật khẩu -->
                        <div class="user-field-group">
                            <label>Mật khẩu <span class="star-required">*</span></label>
                            <input type="password" name="password" required placeholder="Nhập mật khẩu">
                            <small>
                                <i class="bi bi-info-circle"></i>
                                Tối thiểu 6 ký tự
                            </small>
                        </div>

                        <!-- Vai trò -->
                        <div class="user-field-group">
                            <label>Vai trò <span class="star-required">*</span></label>
                            <select name="role" id="roleSelect" required>
                                <option value="">-- Chọn vai trò --</option>
                                <option value="admin">Admin - Quản trị viên</option>
                                <option value="staff">Staff - Nhân viên</option>
                                <option value="user">User - Người dùng</option>
                            </select>
                            <span id="roleBadge"></span>
                        </div>

                        <!-- Giới tính -->
                        <div class="user-field-group">
                            <label>Giới tính</label>
                            <select name="gioi_tinh">
                                <option value="">-- Chọn giới tính --</option>
                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>

                        <!-- Ngày sinh -->
                        <div class="user-field-group">
                            <label>Ngày sinh</label>
                            <input type="date" name="ngay_sinh">
                        </div>

                        <!-- Địa chỉ -->
                        <div class="user-field-group wide-field">
                            <label>Địa chỉ</label>
                            <textarea name="dia_chi" placeholder="Nhập địa chỉ chi tiết..."></textarea>
                        </div>

                        <!-- Trạng thái -->
                        <div class="user-field-group wide-field">
                            <div class="status-checkbox-wrap">
                                <input type="checkbox" name="trang_thai" id="statusCheckbox" checked>
                                <label style="color: black;" for="statusCheckbox">
                                    <i  style="color: black;" class="bi bi-check-circle-fill"></i>
                                    Kích hoạt tài khoản ngay
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="action-btn-group">
                        <button type="button" class="action-btn secondary-style" onclick="window.location.href='UserController.php'">
                            <i class="bi bi-x-circle"></i> Hủy
                        </button>
                        <button type="submit" class="action-btn primary-style">
                            <i class="bi bi-save"></i> Lưu người dùng
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <script>
        // Role badge preview
        document.getElementById('roleSelect').addEventListener('change', function() {
            const badge = document.getElementById('roleBadge');
            const value = this.value;
            
            if (value === 'admin') {
                badge.innerHTML = '<span class="role-preview-badge admin-style"><i class="bi bi-shield-check"></i> ADMIN</span>';
            } else if (value === 'staff') {
                badge.innerHTML = '<span class="role-preview-badge staff-style"><i class="bi bi-person-badge"></i> STAFF</span>';
            } else if (value === 'user') {
                badge.innerHTML = '<span class="role-preview-badge user-style"><i class="bi bi-person"></i> USER</span>';
            } else {
                badge.innerHTML = '';
            }
        });

        // Form validation
        document.getElementById('addUserForm').addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]').value;
            if (password.length < 6) {
                e.preventDefault();
                alert('Mật khẩu phải có ít nhất 6 ký tự!');
                return false;
            }
        });
    </script>
</body>
</html>