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
    z-index: 9999; /* thêm dòng này */
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
    <?php
session_start();
include '../db/db.php';
$staff_id = $_SESSION['id'];

// Lấy thông tin cơ bản
$sql = "SELECT * FROM user WHERE id = $staff_id";
$res = mysqli_query($conn, $sql);
$staff = mysqli_fetch_assoc($res);

// Lấy kỹ năng
$sqlSkill = "SELECT * FROM staff_skill WHERE staff_id = $staff_id";
$skills = mysqli_query($conn, $sqlSkill);

// Lấy kinh nghiệm
$sqlExp = "SELECT * FROM staff_experience WHERE staff_id = $staff_id";
$exps = mysqli_query($conn, $sqlExp);
?>

    <!-- Sidebar -->
    <?php include('menu.php'); ?>

    <!-- Main content -->

    <div class="main-content">
        <button class="edit-btn-float" onclick="openEditModal()">🔧 Sửa hồ sơ</button>
        
        <!-- Header -->
        <div class="header-section">
            <img src="<?= $staff['photo'] ?>" alt="Profile" class="profile-photo" id="profilePhoto">

            <h1 id="staffName"><?= $staff['full_name'] ?></h1>
            <h2 id="staffPosition"><?= $staff['position'] ?></h2>

        </div>

        <!-- Decorative elements -->
        <svg class="plane-icon" viewBox="0 0 100 100" fill="white">
            <path d="M10,50 L90,30 L80,50 L90,70 Z"/>
        </svg>
        <div class="birds">🦅 🦅 🦅</div>

        <!-- Content -->
        <div class="content-wrapper">
            <!-- Về tôi -->
            <div class="section">
                <h3>Về tôi</h3>
                <p id="aboutText">
    <?= nl2br($staff['about']) ?>
</p>

            </div>

            <!-- Kỹ năng -->
            <div class="section">
                <h3>Kỹ năng</h3>
                <div class="skills-grid" id="skillsList">
    <?php while($row = mysqli_fetch_assoc($skills)) : ?>
        <div class="skill-card">
            <?= $row['skill_name'] ?>
        </div>
    <?php endwhile; ?>
</div>

            </div>

            <!-- Kinh nghiệm -->
            <div class="section">
                <h3>Kinh nghiệm làm việc</h3>
                <div id="experienceList">
    <?php while($row = mysqli_fetch_assoc($exps)) : ?>
        <div class="experience-card">
            <h4>
                <?= $row['year_start'] ?> - 
                <?= $row['year_end'] ? $row['year_end'] : "Hiện tại" ?> :
                <?= $row['title'] ?>
            </h4>
            <p><?= $row['description'] ?></p>
        </div>
    <?php endwhile; ?>
</div>

            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>✏️ Chỉnh sửa hồ sơ</h2>
                <button class="close-btn" onclick="closeEditModal()">&times;</button>
            </div>

            <form id="editForm" onsubmit="saveProfile(event)">
                <!-- Photo Upload -->
                <div class="photo-upload">
                    <img src="https://via.placeholder.com/120" alt="Preview" class="photo-preview" id="photoPreview">
                    <label for="photoInput" class="upload-btn">📷 Thay đổi ảnh đại diện</label>
                    <input type="file" id="photoInput" accept="image/*" onchange="previewPhoto(event)">
                </div>

                <!-- Basic Info -->
                <div class="form-section">
                    <div class="form-section-title">👤 Thông tin cơ bản</div>
                    <div class="form-group">
                        <label for="editName">Họ và tên *</label>
                        <input type="text" id="editName" required placeholder="Nhập họ và tên">
                    </div>
                    <div class="form-group">
                        <label for="editPosition">Vị trí công việc *</label>
                        <input type="text" id="editPosition" required placeholder="Nhập vị trí công việc">
                    </div>
                </div>

                <!-- About -->
                <div class="form-section">
                    <div class="form-section-title">📝 Về tôi</div>
                    <div class="form-group">
                        <label for="editAbout">Giới thiệu bản thân *</label>
                        <textarea id="editAbout" required placeholder="Mô tả về bản thân, kinh nghiệm và đam mê..."></textarea>
                    </div>
                </div>

                <!-- Skills -->
                <div class="form-section">
                    <div class="form-section-title">⭐ Kỹ năng</div>
                    <div class="skill-input-group">
                        <input type="text" id="newSkillInput" placeholder="Nhập kỹ năng mới">
                        <button type="button" class="add-skill-btn" onclick="addSkill()">+ Thêm</button>
                    </div>
                    <div id="skillsEditList"></div>
                </div>

                <!-- Experience -->
                <div class="form-section">
                    <div class="form-section-title">💼 Kinh nghiệm làm việc</div>
                    <div class="form-group">
                        <label for="editExperience">Kinh nghiệm *</label>
                        <textarea id="editExperience" required placeholder="Mô tả kinh nghiệm làm việc (mỗi kinh nghiệm trên một dòng, định dạng: Năm: Công ty/Vị trí - Mô tả)"></textarea>
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">
                        ❌ Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        💾 Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentSkills = [
            'Thành thạo tiếng Anh và tiếng Pháp',
            'Kiến thức tour châu Âu và châu Á',
            'Tổ chức sự kiện và quản lý nhóm',
            'Chứng chỉ hướng dẫn viên IATA'
        ];

        function openEditModal() {
            const modal = document.getElementById('editModal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            // Load current data
            document.getElementById('editName').value = document.getElementById('staffName').textContent;
            document.getElementById('editPosition').value = document.getElementById('staffPosition').textContent;
            document.getElementById('editAbout').value = document.getElementById('aboutText').textContent;
            document.getElementById('photoPreview').src = document.getElementById('profilePhoto').src;
            
            // Load experiences
            const experiences = document.querySelectorAll('.experience-card');
            let expText = '';
            experiences.forEach(exp => {
                const title = exp.querySelector('h4').textContent;
                const desc = exp.querySelector('p').textContent;
                expText += `${title}\n${desc}\n\n`;
            });
            document.getElementById('editExperience').value = expText.trim();
            
            renderSkillsList();
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function previewPhoto(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photoPreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        function renderSkillsList() {
            const container = document.getElementById('skillsEditList');
            container.innerHTML = '';
            currentSkills.forEach((skill, index) => {
                const skillItem = document.createElement('div');
                skillItem.className = 'skill-item';
                skillItem.innerHTML = `
                    <span>${skill}</span>
                    <button type="button" class="remove-skill-btn" onclick="removeSkill(${index})">Xóa</button>
                `;
                container.appendChild(skillItem);
            });
        }

        function addSkill() {
            const input = document.getElementById('newSkillInput');
            const skill = input.value.trim();
            if (skill) {
                currentSkills.push(skill);
                input.value = '';
                renderSkillsList();
            }
        }

        function removeSkill(index) {
            currentSkills.splice(index, 1);
            renderSkillsList();
        }

        function saveProfile(event) {
            event.preventDefault();
            
            // Update name and position
            document.getElementById('staffName').textContent = document.getElementById('editName').value;
            document.getElementById('staffPosition').textContent = document.getElementById('editPosition').value;
            
            // Update about
            document.getElementById('aboutText').textContent = document.getElementById('editAbout').value;
            
            // Update photo
            document.getElementById('profilePhoto').src = document.getElementById('photoPreview').src;
            
            // Update skills
            const skillsContainer = document.getElementById('skillsList');
            skillsContainer.innerHTML = '';
            currentSkills.forEach(skill => {
                const skillCard = document.createElement('div');
                skillCard.className = 'skill-card';
                skillCard.textContent = skill;
                skillsContainer.appendChild(skillCard);
            });
            
            // Update experience
            const experienceText = document.getElementById('editExperience').value;
            const experiences = experienceText.split('\n\n').filter(exp => exp.trim());
            const experienceContainer = document.getElementById('experienceList');
            experienceContainer.innerHTML = '';
            
            experiences.forEach(exp => {
                const lines = exp.split('\n').filter(line => line.trim());
                if (lines.length >= 1) {
                    const card = document.createElement('div');
                    card.className = 'experience-card';
                    card.innerHTML = `
                        <h4>${lines[0]}</h4>
                        <p>${lines.slice(1).join(' ')}</p>
                    `;
                    experienceContainer.appendChild(card);
                }
            });
            
            closeEditModal();
            
            // Show success message
            alert('✅ Hồ sơ đã được cập nhật thành công!');
        }

        // Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
</body>
</html>