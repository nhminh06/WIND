<?php
session_start();
include('../../db/db.php');

if (!isset($_SESSION['id'])) {
    header("Location: ../../login.php");
    exit();
}

$staff_id = $_SESSION['id'];

// Lấy thông tin cơ bản
$sql = "SELECT * FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$staff = $stmt->get_result()->fetch_assoc();

// Lấy kỹ năng
$sqlSkill = "SELECT * FROM staff_skill WHERE staff_id = ?";
$stmt_skill = $conn->prepare($sqlSkill);
$stmt_skill->bind_param("i", $staff_id);
$stmt_skill->execute();
$skills = $stmt_skill->get_result();

// Lấy kinh nghiệm
$sqlExp = "SELECT * FROM staff_experience WHERE staff_id = ? ORDER BY year_start DESC";
$stmt_exp = $conn->prepare($sqlExp);
$stmt_exp->bind_param("i", $staff_id);
$stmt_exp->execute();
$exps = $stmt_exp->get_result();
?>
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

        .main-content {
            margin-left: 250px;
            flex: 1;
            background: linear-gradient(180deg, #040715ff 0%, #401c64ff 30%, #115069ff 60%, #0a2834ff 100%);
            min-height: 100vh;
            position: relative;
        }

        .header-section {
            text-align: center;
            padding: 40px 20px;
            color: white;
            position: relative;
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
            z-index: 9999;
        }

        .edit-btn-float:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

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

        .skill-input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .skill-input-group input {
            flex: 1;
        }

        .add-skill-btn {
            padding: 10px 20px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .skill-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            background: #f0f0f0;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .remove-skill-btn {
            padding: 5px 15px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .exp-input-group {
            border: 2px solid #e0e0e0;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .exp-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .remove-exp-btn {
            padding: 5px 15px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .add-exp-btn {
            padding: 10px 20px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
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
    <?php include('menu.php'); ?>

    <div class="main-content">
        <button class="edit-btn-float" onclick="openEditModal()">🔧 Sửa hồ sơ</button>
        
        <div class="header-section">
            <img src="<?= htmlspecialchars($staff['photo']) ?>" alt="Profile" class="profile-photo" id="profilePhoto">
            <h1 id="staffName"><?= htmlspecialchars($staff['full_name']) ?></h1>
            <h2 id="staffPosition"><?= htmlspecialchars($staff['position']) ?></h2>
        </div>

        <div class="content-wrapper">
            <div class="section">
                <h3>Về tôi</h3>
                <p id="aboutText"><?= nl2br(htmlspecialchars($staff['about'])) ?></p>
            </div>

            <div class="section">
                <h3>Kỹ năng</h3>
                <div class="skills-grid" id="skillsList">
                    <?php while($row = $skills->fetch_assoc()): ?>
                        <div class="skill-card"><?= htmlspecialchars($row['skill_name']) ?></div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="section">
                <h3>Kinh nghiệm làm việc</h3>
                <div id="experienceList">
                    <?php while($row = $exps->fetch_assoc()): ?>
                        <div class="experience-card">
                            <h4>
                                <?= $row['year_start'] ?> - 
                                <?= $row['year_end'] ? $row['year_end'] : "Hiện tại" ?> :
                                <?= htmlspecialchars($row['title']) ?>
                            </h4>
                            <p><?= htmlspecialchars($row['description']) ?></p>
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
                <div class="photo-upload">
                    <img src="<?= htmlspecialchars($staff['photo']) ?>" alt="Preview" class="photo-preview" id="photoPreview">
                    <label for="photoInput" class="upload-btn">📷 Thay đổi ảnh đại diện</label>
                    <input type="file" id="photoInput" name="photo" accept="image/*" onchange="previewPhoto(event)">
                </div>

                <div class="form-group">
                    <label for="editName">Họ và tên *</label>
                    <input type="text" id="editName" name="full_name" required value="<?= htmlspecialchars($staff['full_name']) ?>">
                </div>

                <div class="form-group">
                    <label for="editPosition">Vị trí công việc *</label>
                    <input type="text" id="editPosition" name="position" required value="<?= htmlspecialchars($staff['position']) ?>">
                </div>

                <div class="form-group">
                    <label for="editAbout">Giới thiệu bản thân *</label>
                    <textarea id="editAbout" name="about" required><?= htmlspecialchars($staff['about']) ?></textarea>
                </div>

                <div class="form-group">
                    <label>Kỹ năng</label>
                    <div class="skill-input-group">
                        <input type="text" id="newSkillInput" placeholder="Nhập kỹ năng mới">
                        <button type="button" class="add-skill-btn" onclick="addSkill()">+ Thêm</button>
                    </div>
                    <div id="skillsEditList"></div>
                </div>

                <div class="form-group">
                    <label>Kinh nghiệm làm việc</label>
                    <button type="button" class="add-exp-btn" onclick="addExperience()">+ Thêm kinh nghiệm</button>
                    <div id="experiencesEditList"></div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">✖ Hủy</button>
                    <button type="submit" class="btn btn-primary">💾 Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentSkills = <?= json_encode(array_column($skills->fetch_all(MYSQLI_ASSOC), 'skill_name')) ?>;
        let currentExperiences = <?= json_encode($exps->fetch_all(MYSQLI_ASSOC)) ?>;

        function openEditModal() {
            document.getElementById('editModal').classList.add('active');
            document.body.style.overflow = 'hidden';
            renderSkillsList();
            renderExperiencesList();
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
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

        function renderExperiencesList() {
            const container = document.getElementById('experiencesEditList');
            container.innerHTML = '';
            currentExperiences.forEach((exp, index) => {
                const expDiv = document.createElement('div');
                expDiv.className = 'exp-input-group';
                expDiv.innerHTML = `
                    <div class="exp-header">
                        <strong>Kinh nghiệm #${index + 1}</strong>
                        <button type="button" class="remove-exp-btn" onclick="removeExperience(${index})">Xóa</button>
                    </div>
                    <div class="form-group">
                        <label>Tiêu đề:</label>
                        <input type="text" class="exp-title" value="${exp.title || ''}" required>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label>Năm bắt đầu:</label>
                            <input type="number" class="exp-year-start" value="${exp.year_start || ''}" required>
                        </div>
                        <div class="col-6 form-group">
                            <label>Năm kết thúc:</label>
                            <input type="number" class="exp-year-end" value="${exp.year_end || ''}" placeholder="Để trống nếu hiện tại">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Mô tả:</label>
                        <textarea class="exp-description">${exp.description || ''}</textarea>
                    </div>
                `;
                container.appendChild(expDiv);
            });
        }

        function addExperience() {
            currentExperiences.push({
                title: '',
                year_start: new Date().getFullYear(),
                year_end: null,
                description: ''
            });
            renderExperiencesList();
        }

        function removeExperience(index) {
            currentExperiences.splice(index, 1);
            renderExperiencesList();
        }

        function saveProfile(event) {
            event.preventDefault();
            
            // Lấy dữ liệu kinh nghiệm
            const expDivs = document.querySelectorAll('.exp-input-group');
            currentExperiences = [];
            expDivs.forEach(div => {
                currentExperiences.push({
                    title: div.querySelector('.exp-title').value,
                    year_start: div.querySelector('.exp-year-start').value,
                    year_end: div.querySelector('.exp-year-end').value || null,
                    description: div.querySelector('.exp-description').value
                });
            });
            
            const formData = new FormData(document.getElementById('editForm'));
            formData.append('skills', JSON.stringify(currentSkills));
            formData.append('experiences', JSON.stringify(currentExperiences));
            
            fetch('UpdateProfile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(error => {
                alert('❌ Có lỗi xảy ra: ' + error);
            });
        }

        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>