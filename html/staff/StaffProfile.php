<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/Staff.css">
    <title>Hồ sơ nhân viên du lịch</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #1e1e2f;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        
        /* Main content */
        .main-content {
            margin-left: 250px;
            flex: 1;
            background: linear-gradient(180deg, #040715ff 0%, #401c64ff 30%, #115069ff 60%, #0a2834ff 100%);
            min-height: 100vh;
            position: relative;
        }

        /* Header section */
        .header-section {
            text-align: center;
            padding: 40px 20px;
            color: white;
            position: relative;
        }

        .edit-badge {
            display: inline-block;
            padding: 8px 20px;
            background: rgba(255, 255, 255, 0.3);
            border: 2px solid white;
            border-radius: 20px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .edit-badge:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        .profile-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            margin: 0 auto 20px;
            display: block;
            object-fit: cover;
        }

        .header-section h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .header-section h2 {
            font-size: 20px;
            font-weight: 400;
            opacity: 0.95;
        }

        /* Edit button floating */
        .edit-btn-float {
            position: absolute;
            top: 20px;
            right: 30px;
            padding: 12px 30px;
            background: white;
            color: #667eea;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
        }

        .edit-btn-float:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        /* Content sections */
        .content-wrapper {
            padding: 40px;
        }

        .section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .section h3 {
            color: #667eea;
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }

        .section p {
            line-height: 1.8;
            color: #555;
            font-size: 16px;
            text-align: justify;
        }

        /* Skills grid */
        .skills-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 15px;
        }

        .skill-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border: 2px solid #667eea;
            color: #555;
            font-size: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Experience cards */
        .experience-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 5px solid #764ba2;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .experience-card h4 {
            color: #764ba2;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .experience-card p {
            color: #666;
            margin: 0;
        }

        /* Decorative elements */
        .plane-icon {
            position: absolute;
            width: 150px;
            opacity: 0.3;
        }

        .plane-1 {
            left: 10%;
            bottom: 30%;
            transform: rotate(-15deg);
        }

        .birds {
            position: absolute;
            right: 15%;
            top: 40%;
            font-size: 24px;
            opacity: 0.4;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 2000;
            overflow-y: auto;
            padding: 20px;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 700px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .modal-header h2 {
            color: #667eea;
            font-size: 28px;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 30px;
            color: #999;
            cursor: pointer;
            transition: all 0.3s;
        }

        .close-btn:hover {
            color: #667eea;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 15px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #666;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
        }

        .photo-upload {
            text-align: center;
            margin-bottom: 25px;
        }

        .photo-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #667eea;
            margin-bottom: 15px;
        }

        .upload-btn {
            display: inline-block;
            padding: 8px 20px;
            background: #667eea;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .upload-btn:hover {
            background: #764ba2;
        }

        #photoInput {
            display: none;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            
            .main-content {
                margin-left: 200px;
            }
            
            .skills-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include('menu.php'); ?>

    <!-- Main content -->
    <div class="main-content">
        <button class="edit-btn-float" onclick="openEditModal()">🔧 Sửa hồ sơ</button>
        
        <!-- Header -->
        <div class="header-section">
            
            <img src="https://via.placeholder.com/120" alt="Profile" class="profile-photo" id="profilePhoto">
            <h1 id="staffName">Nguyễn Văn A</h1>
            <h2 id="staffPosition">Chuyên viên tư vấn du lịch</h2>
        </div>

        <!-- Decorative elements -->
        <svg class="plane-icon plane-1" viewBox="0 0 100 100" fill="white">
            <path d="M10,50 L90,30 L80,50 L90,70 Z"/>
        </svg>
        <div class="birds">🦅 🦅 🦅</div>

        <!-- Content -->
        <div class="content-wrapper">
            <!-- Về tôi -->
            <div class="section">
                <h3>Về tôi</h3>
                <p id="aboutText">Với hơn 5 năm kinh nghiệm trong lĩnh vực du lịch, tôi đã dẫn dắt nhiều đoàn khách khám phá các địa điểm nổi tiếng như châu Âu, Đông Nam Á và các tour mạo hiểm. Tôi đam mê khám phá văn hóa mới và mang đến trải nghiệm tuyệt vời cho khách hàng.</p>
            </div>

            <!-- Kỹ năng -->
            <div class="section">
                <h3>Kỹ năng</h3>
                <div class="skills-grid" id="skillsList">
                    <div class="skill-card">Thành thạo tiếng Anh và tiếng Pháp</div>
                    <div class="skill-card">Kiến thức tour châu Âu và châu Á</div>
                    <div class="skill-card">Tổ chức sự kiện và quản lý nhóm</div>
                    <div class="skill-card">Chứng chỉ hướng dẫn viên IATA</div>
                </div>
            </div>

            <!-- Kinh nghiệm -->
            <div class="section">
                <h3>Kinh nghiệm làm việc</h3>
                <div id="experienceList">
                    <div class="experience-card">
                        <h4>2020 - Hiện tại: Công ty Du lịch ABC</h4>
                        <p>Chuyên viên tư vấn và hướng dẫn viên du lịch quốc tế</p>
                    </div>
                    <div class="experience-card">
                        <h4>2018 - 2020: Hướng dẫn viên địa phương</h4>
                        <p>Đà Nẵng - Đã dẫn dắt hơn 50 tour khám phá châu Âu và tour sinh thái</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>