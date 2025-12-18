<?php
session_start();
include('../../db/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$staff_id = $_SESSION['user_id'];

// Lấy thông tin cơ bản
$sql = "SELECT * FROM user WHERE id = ".$staff_id;
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0){
    $row = mysqli_fetch_assoc($result);
    
    // ✅ Xử lý ngày sinh
    $ngay_sinh = $row['ngay_sinh'] ?? '2000-01-01';
    list($nam, $thang, $ngay) = explode('-', $ngay_sinh);
    $thang = (int)$thang;
    $ngay = (int)$ngay;
} else {
    die("Không tìm thấy thông tin người dùng!");
}

// Lấy kỹ năng
$sqlSkill = "SELECT * FROM staff_skill WHERE staff_id = $staff_id";
$skills_result = mysqli_query($conn, $sqlSkill);
$skills = [];
if($skills_result) {
    while($skill = mysqli_fetch_assoc($skills_result)) {
        $skills[] = $skill;
    }
}

// Lấy kinh nghiệm
$sqlExp = "SELECT * FROM staff_experience WHERE staff_id = $staff_id ORDER BY year_start DESC";
$exps_result = mysqli_query($conn, $sqlExp);
$exps = [];
if($exps_result) {
    while($exp = mysqli_fetch_assoc($exps_result)) {
        $exps[] = $exp;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/Staff.css">
    <link rel="stylesheet" href="../../css/staff2-0.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
     <link rel="stylesheet" href="../../css/rpstaff.css">
    <title>Cài đặt tài khoản</title>
    
</head>
<body>
    <?php include('../../includes/Staffnav.php'); ?>

    <div class="main-content">
        <div class="settings-wrapper">
            <div class="settings-container">

                <!-- Tabs -->
                <div class="settings-header">
                    <div class="settings-tabs">
                        <div class="settings-tab active-tab">Thông tin tài khoản</div>
                        <div class="settings-tab">Mật khẩu & Bảo mật</div>
                    </div>
                </div>

                <div class="settings-content">

                    <!-- Tab Thông tin tài khoản -->
                    <div class="account-section">

                        <!-- Form dữ liệu cá nhân -->
                        <form id="basicInfoForm">
                            <div class="settings-section">
                                <h2>Dữ liệu cá nhân</h2>
                                
                                <div class="staff-card">
                                   <div class="avatar">
                                    <img id="avatarImg" src="<?php echo "../../../" . (!empty($_SESSION['avatar']) ? $_SESSION['avatar'] : 'img/avatamacdinh.png'); ?>" alt="Ảnh đại diện" style="cursor: pointer;">
                                    <input type="file" id="avatarInput" accept="image/*" style="display: none;">
                                    </div>
                                    <div class="rank-staff">
                                        <h4><?php echo htmlspecialchars($row['ho_ten']); ?></h4>
                                        <p>
                                            <?php
                                            $role = $row['role'] ?? 'user';
                                            if($role == 'staff') {
                                                echo '<i class="bi bi-backpack4-fill"></i> Nhân viên';
                                            } 
                                            ?>
                                            • <i class="bi bi-star-fill"></i> Rank <?php echo $row['rank'] ?? 0; ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="settings-form-group">
                                    <label>Họ và tên đầy đủ</label>
                                    <input type="text" name="ho_ten" value="<?php echo htmlspecialchars($row['ho_ten']); ?>" class="settings-input" disabled>
                                </div>

                                <div class="settings-form-group">
                                    <label>Giới tính</label>
                                    <select name="gioi_tinh" class="settings-select" disabled>
                                        <option value="">Chọn giới tính</option>
                                        <option value="Nam" <?php echo ($row['gioi_tinh'] ?? '') == 'Nam' ? 'selected' : ''; ?>>Nam giới</option>
                                        <option value="Nữ" <?php echo ($row['gioi_tinh'] ?? '') == 'Nữ' ? 'selected' : ''; ?>>Nữ giới</option>
                                        <option value="Khác" <?php echo ($row['gioi_tinh'] ?? '') == 'Khác' ? 'selected' : ''; ?>>Khác</option>
                                    </select>
                                </div>

                                <div class="settings-form-group">
                                    <label>Ngày sinh</label>
                                    <div class="settings-date-group">
                                        <input class="settings-input" type="number" name="ngay" value="<?php echo $ngay; ?>" min="1" max="31" placeholder="Ngày" disabled>
                                        <select name="thang" class="settings-select" disabled>
                                            <option value="">Chọn tháng</option>
                                            <?php for($i=1;$i<=12;$i++): ?>
                                                <option value="<?php echo $i; ?>" <?php echo $thang==$i?'selected':''; ?>>Tháng <?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                        <input class="settings-input" type="number" name="nam" value="<?php echo $nam; ?>" min="1900" max="<?php echo date('Y'); ?>" placeholder="Năm" disabled>
                                    </div>
                                </div>

                                <div class="settings-form-group">
                                    <label>Địa chỉ</label>
                                    <input type="text" name="dia_chi" value="<?php echo htmlspecialchars($row['dia_chi'] ?? ''); ?>" class="settings-input" disabled>
                                </div>

                                <div class="settings-button-group">
                                    <button type="button" class="settings-btn settings-btn-secondary btn-edit">Chỉnh sửa</button>
                                    <button type="button" onclick="saveBasicInfo()" class="settings-btn settings-btn-primary">Lưu thay đổi</button>
                                </div>
                            </div>
                        </form>

                        <!-- Email -->
                        <div class="settings-section">
                            <h2>E-mail</h2>
                            <input style="margin-bottom: 15px;" type="email" 
                                id="emailInput"
                                value="<?php echo htmlspecialchars($row['email']); ?>" 
                                class="settings-input" 
                                disabled>
                            <button type="button" class="settings-btn settings-link-btn edit-email-btn">
                                + Chỉnh sửa Email
                            </button>
                            <button type="button" onclick="saveEmail()" class="settings-btn settings-btn-primary" style="display:none; margin-top:10px;" id="saveEmailBtn">
                                Lưu Email
                            </button>
                        </div>

                        <!-- SĐT -->
                        <div class="settings-section">
                            <h2>Số điện thoại di động</h2>
                            <input style="margin-bottom: 15px;" type="text" 
                                id="phoneInput"
                                value="<?php echo htmlspecialchars($row['sdt'] ?? ''); ?>" 
                                class="settings-input" 
                                disabled>
                            <button type="button" class="settings-btn settings-link-btn edit-phone-btn">
                                + Chỉnh sửa số điện thoại
                            </button>
                            <button type="button" onclick="savePhone()" class="settings-btn settings-btn-primary" style="display:none; margin-top:10px;" id="savePhoneBtn">
                                Lưu số điện thoại
                            </button>
                        </div>

                        <!-- Giới thiệu bản thân -->
                        <div class="settings-section">
                            <h2><i class="bi bi-file-earmark-check-fill"></i> Giới thiệu bản thân</h2>
                            <?php if(!empty($row['about'])): ?>
                                <div class="about-content">
                                    <?php echo nl2br(htmlspecialchars($row['about'])); ?>
                                </div>
                                <button type="button" class="add-info-btn edit-info-btn" onclick="editAbout()">
                                    <i class="bi bi-pencil-square"></i> Chỉnh sửa giới thiệu
                                </button>
                            <?php else: ?>
                                <div class="empty-state">
                                    Bạn chưa có giới thiệu bản thân
                                    <br>
                                    <button type="button" class="add-info-btn" onclick="editAbout()">
                                        + Thêm giới thiệu
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Kỹ năng -->
                        <div class="settings-section">
                            <h2><i class="bi bi-clipboard-check-fill"></i> Kỹ năng</h2>
                            <?php if(!empty($skills)): ?>
                                <div class="skills-grid">
                                    <?php foreach($skills as $skill): ?>
                                        <div class="skill-tag">
                                            <?php echo htmlspecialchars($skill['skill_name']); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="add-info-btn edit-info-btn" onclick="manageSkills()">
                                    <i class="bi bi-pencil-square"></i> Quản lý kỹ năng
                                </button>
                            <?php else: ?>
                                <div class="empty-state">
                                    Bạn chưa thêm kỹ năng nào
                                    <br>
                                    <button type="button" class="add-info-btn" onclick="manageSkills()">
                                        + Thêm kỹ năng
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Kinh nghiệm làm việc -->
                        <div class="settings-section">
                            <h2><i class="bi bi-briefcase-fill"></i> Kinh nghiệm làm việc</h2>
                            <?php if(!empty($exps)): ?>
                                <div class="experience-list">
                                    <?php foreach($exps as $exp): ?>
                                        <div class="experience-item">
                                            <div class="experience-header">
                                                <?php echo htmlspecialchars($exp['title']); ?>
                                            </div>
                                            <div class="experience-period">
                                                <?php echo htmlspecialchars($exp['year_start']); ?> - 
                                                <?php echo !empty($exp['year_end']) ? htmlspecialchars($exp['year_end']) : 'Hiện tại'; ?>
                                            </div>
                                            <?php if(!empty($exp['description'])): ?>
                                                <div class="experience-description">
                                                    <?php echo nl2br(htmlspecialchars($exp['description'])); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="add-info-btn edit-info-btn" onclick="manageExperience()">
                                    <i class="bi bi-pencil-square"></i> Quản lý kinh nghiệm
                                </button>
                            <?php else: ?>
                                <div class="empty-state">
                                    Bạn chưa thêm kinh nghiệm làm việc nào
                                    <br>
                                    <button type="button" class="add-info-btn" onclick="manageExperience()">
                                        + Thêm kinh nghiệm
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>

                    <!-- Tab Mật khẩu & Bảo mật -->
                    <form id="changePasswordForm" class="security-section">
                        <div class="settings-section">
                            <h2>Mật khẩu & Bảo mật</h2>

                            <div class="settings-form-group">
                                <label>Mật khẩu hiện tại</label>
                                <input type="password" id="current_password" name="current_password" class="settings-input" required>
                                <small class="form-hint">Nhập mật khẩu hiện tại của bạn</small>
                            </div>

                            <div class="settings-form-group">
                                <label>Mật khẩu mới</label>
                                <input type="password" id="new_password" name="new_password" class="settings-input" required minlength="6">
                                <small class="form-hint">Mật khẩu phải có ít nhất 6 ký tự</small>
                            </div>

                            <div class="settings-form-group">
                                <label>Xác nhận mật khẩu mới</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="settings-input" required minlength="6">
                                <small class="form-hint">Nhập lại mật khẩu mới để xác nhận</small>
                            </div>

                            <div class="settings-button-group">
                                <button type="button" onclick="resetPasswordForm()" class="settings-btn settings-btn-secondary">Hủy</button>
                                <button type="button" onclick="changePassword()" class="settings-btn settings-btn-primary">Đổi mật khẩu</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Giới thiệu -->
    <div id="aboutModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="bi bi-file-earmark-check-fill"></i> Giới thiệu bản thân</h3>
                <button class="close-modal" onclick="closeAboutModal()">&times;</button>
            </div>
            <div class="modal-form-group">
                <label>Nội dung giới thiệu</label>
                <textarea id="aboutTextarea" class="modal-textarea" placeholder="Viết vài dòng giới thiệu về bản thân..."><?php echo htmlspecialchars($row['about'] ?? ''); ?></textarea>
            </div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeAboutModal()">Hủy</button>
                <button class="btn-save" onclick="saveAbout()">Lưu</button>
            </div>
        </div>
    </div>

    <!-- Modal Kỹ năng -->
    <div id="skillsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="bi bi-clipboard-check-fill"></i> Quản lý kỹ năng</h3>
                <button class="close-modal" onclick="closeSkillsModal()">&times;</button>
            </div>
            <div class="skill-list" id="skillsList"></div>
            <div class="modal-form-group">
                <label>Thêm kỹ năng mới</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="newSkillInput" class="modal-input" placeholder="Nhập tên kỹ năng...">
                    <button class="btn-add" onclick="addSkill()">Thêm</button>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeSkillsModal()">Hủy</button>
                <button class="btn-save" onclick="saveSkills()">Lưu thay đổi</button>
            </div>
        </div>
    </div>

    <!-- Modal Kinh nghiệm -->
    <div id="experienceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="bi bi-briefcase-fill"></i> Quản lý kinh nghiệm làm việc</h3>
                <button class="close-modal" onclick="closeExperienceModal()">&times;</button>
            </div>
            <div id="experiencesList"></div>
            <button class="btn-add" onclick="addExperience()" style="margin-bottom: 20px;">+ Thêm kinh nghiệm</button>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeExperienceModal()">Hủy</button>
                <button class="btn-save" onclick="saveExperiences()">Lưu thay đổi</button>
            </div>
        </div>
    </div>

    <script>
        // Chuyển tab
        const tabs = document.querySelectorAll('.settings-tab');
        const accountSection = document.querySelector('.account-section');
        const securitySection = document.querySelector('.security-section');

        tabs.forEach((tab, index) => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active-tab'));
                tab.classList.add('active-tab');

                if(index === 0){
                    accountSection.style.display = 'block';
                    securitySection.style.display = 'none';
                }else{
                    accountSection.style.display = 'none';
                    securitySection.style.display = 'block';
                }
            });
        });

      const avatarImg = document.getElementById("avatarImg");
  const avatarInput = document.getElementById("avatarInput");

  // Khi nhấn vào ảnh, bật chọn file
  avatarImg.addEventListener("click", () => avatarInput.click());

  // Khi chọn file mới
  avatarInput.addEventListener("change", () => {
    const file = avatarInput.files[0];
    if (file) {
      // Hiển thị xem trước ảnh
      const reader = new FileReader();
      reader.onload = e => avatarImg.src = e.target.result;
      reader.readAsDataURL(file);

      // Gửi ảnh lên server để lưu vào CSDL
      const formData = new FormData();
      formData.append('avatar', file);

      fetch('../../php/UsersController/upload_avatar.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          $_SESSION['thanhcong'] = 1;
        } else {
          $_SESSION['thanhcong'] = 0;
        }
      })
      .catch(err => $_SESSION['thanhcong'] = 0);
    }
  });

        // ✅ LƯU THÔNG TIN CƠ BẢN
        function saveBasicInfo() {
            const form = document.getElementById('basicInfoForm');
            const formData = new FormData(form);
            formData.append('action', 'update_basic_info'); // ✅ Thêm action
            
            fetch('../../php/StaffCTL/UpdateStaffInfo.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ Lỗi: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('❌ Có lỗi xảy ra!');
            });
        }

        // ✅ LƯU EMAIL
        function saveEmail() {
            const email = document.getElementById('emailInput').value;
            const formData = new FormData();
            formData.append('action', 'update_email'); // ✅ Action cho email
            formData.append('email', email);
            
            fetch('../../php/StaffCTL/UpdateStaffInfo.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ Lỗi: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('❌ Có lỗi xảy ra!');
            });
        }

        // ✅ LƯU SỐ ĐIỆN THOẠI
        function savePhone() {
            const sdt = document.getElementById('phoneInput').value;
            const formData = new FormData();
            formData.append('action', 'update_phone'); // ✅ Action cho phone
            formData.append('sdt', sdt);
            
            fetch('../../php/StaffCTL/UpdateStaffInfo.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ Lỗi: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('❌ Có lỗi xảy ra!');
            });
        }

        // Chỉnh sửa thông tin cá nhân
        document.querySelector(".btn-edit").addEventListener("click", function () {
            const inputs = document.querySelectorAll("#basicInfoForm input, #basicInfoForm select");

            inputs.forEach(el => {
                el.disabled = !el.disabled;
            });

            this.innerText = this.innerText === "Chỉnh sửa" ? "Hủy" : "Chỉnh sửa";
        });

        // Bật tắt Email
        document.querySelector(".edit-email-btn").addEventListener("click", function () {
            let emailInput = document.getElementById('emailInput');
            let saveBtn = document.getElementById('saveEmailBtn');
            
            emailInput.disabled = !emailInput.disabled;
            
            if(emailInput.disabled) {
                this.innerText = "+ Chỉnh sửa Email";
                saveBtn.style.display = 'none';
            } else {
                this.innerText = "Hủy chỉnh sửa Email";
                saveBtn.style.display = 'inline-block';
            }
        });

        // Bật tắt SĐT
        document.querySelector(".edit-phone-btn").addEventListener("click", function () {
            let phoneInput = document.getElementById('phoneInput');
            let saveBtn = document.getElementById('savePhoneBtn');
            
            phoneInput.disabled = !phoneInput.disabled;
            
            if(phoneInput.disabled) {
                this.innerText = "+ Chỉnh sửa số điện thoại";
                saveBtn.style.display = 'none';
            } else {
                this.innerText = "Hủy chỉnh sửa số điện thoại";
                saveBtn.style.display = 'inline-block';
            }
        });

        // Quản lý Giới thiệu
        function editAbout() {
            document.getElementById('aboutModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeAboutModal() {
            document.getElementById('aboutModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function saveAbout() {
            const about = document.getElementById('aboutTextarea').value;
            
            const formData = new FormData();
            formData.append('action', 'update_about');
            formData.append('about', about);
            
            fetch('../../php/StaffCTL/UpdateStaffInfo.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ Lỗi: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('❌ Có lỗi xảy ra!');
            });
        }

        // Quản lý kỹ năng
        let currentSkills = <?php echo json_encode(array_column($skills, 'skill_name')); ?>;

        function manageSkills() {
            document.getElementById('skillsModal').classList.add('active');
            document.body.style.overflow = 'hidden';
            renderSkills();
        }

        function closeSkillsModal() {
            document.getElementById('skillsModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function renderSkills() {
            const container = document.getElementById('skillsList');
            container.innerHTML = '';
            
            currentSkills.forEach((skill, index) => {
                const div = document.createElement('div');
                div.className = 'skill-item-edit';
                div.innerHTML = `
                    <span>${skill}</span>
                    <button class="btn-remove" onclick="removeSkill(${index})">Xóa</button>
                `;
                container.appendChild(div);
            });
        }

        function addSkill() {
            const input = document.getElementById('newSkillInput');
            const skill = input.value.trim();
            
            if(skill) {
                currentSkills.push(skill);
                input.value = '';
                renderSkills();
            } else {
                alert('Vui lòng nhập tên kỹ năng!');
            }
        }

        function removeSkill(index) {
            if(confirm('Bạn có chắc muốn xóa kỹ năng này?')) {
                currentSkills.splice(index, 1);
                renderSkills();
            }
        }

        function saveSkills() {
            const formData = new FormData();
            formData.append('action', 'update_skills');
            formData.append('skills', JSON.stringify(currentSkills));
            
            fetch('../../php/StaffCTL/UpdateStaffInfo.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ Lỗi: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('❌ Có lỗi xảy ra!');
            });
        }

        // Quản lý kinh nghiệm
        let currentExperiences = <?php echo json_encode($exps); ?>;

        function manageExperience() {
            document.getElementById('experienceModal').classList.add('active');
            document.body.style.overflow = 'hidden';
            renderExperiences();
        }

        function closeExperienceModal() {
            document.getElementById('experienceModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function renderExperiences() {
            const container = document.getElementById('experiencesList');
            container.innerHTML = '';
            
            currentExperiences.forEach((exp, index) => {
                const div = document.createElement('div');
                div.className = 'exp-item-edit';
                div.innerHTML = `
                    <div class="exp-item-header">
                        <strong>Kinh nghiệm ${index + 1}</strong>
                        <button class="btn-remove" onclick="removeExperience(${index})">Xóa</button>
                    </div>
                    <div class="modal-form-group">
                        <label>Chức danh</label>
                        <input type="text" class="modal-input exp-title" value="${exp.title || ''}" placeholder="Ví dụ: Hướng dẫn viên du lịch">
                    </div>
                    <div class="modal-row">
                        <div class="modal-form-group">
                            <label>Năm bắt đầu</label>
                            <input type="number" class="modal-input exp-year-start" value="${exp.year_start || new Date().getFullYear()}" min="1900" max="${new Date().getFullYear()}">
                        </div>
                        <div class="modal-form-group">
                            <label>Năm kết thúc (để trống nếu hiện tại)</label>
                            <input type="number" class="modal-input exp-year-end" value="${exp.year_end || ''}" min="1900" max="${new Date().getFullYear()}">
                        </div>
                    </div>
                    <div class="modal-form-group">
                        <label>Mô tả</label>
                        <textarea class="modal-textarea exp-description" placeholder="Mô tả công việc và thành tựu...">${exp.description || ''}</textarea>
                    </div>
                `;
                container.appendChild(div);
            });
        }

        function addExperience() {
            currentExperiences.push({
                title: '',
                year_start: new Date().getFullYear(),
                year_end: null,
                description: ''
            });
            renderExperiences();
        }

        function removeExperience(index) {
            if(confirm('Bạn có chắc muốn xóa kinh nghiệm này?')) {
                currentExperiences.splice(index, 1);
                renderExperiences();
            }
        }

        function saveExperiences() {
            const expDivs = document.querySelectorAll('.exp-item-edit');
            currentExperiences = [];
            
            expDivs.forEach(div => {
                const title = div.querySelector('.exp-title').value.trim();
                const yearStart = div.querySelector('.exp-year-start').value;
                const yearEnd = div.querySelector('.exp-year-end').value;
                const description = div.querySelector('.exp-description').value.trim();
                
                if(title && yearStart) {
                    currentExperiences.push({
                        title: title,
                        year_start: yearStart,
                        year_end: yearEnd || null,
                        description: description
                    });
                }
            });
            
            const formData = new FormData();
            formData.append('action', 'update_experiences');
            formData.append('experiences', JSON.stringify(currentExperiences));
            
            fetch('../../php/StaffCTL/UpdateStaffInfo.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ Lỗi: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('❌ Có lỗi xảy ra!');
            });
        }

        // ✅ ĐỔI MẬT KHẨU
        function changePassword() {
            const currentPassword = document.getElementById('current_password').value.trim();
            const newPassword = document.getElementById('new_password').value.trim();
            const confirmPassword = document.getElementById('confirm_password').value.trim();

            // Validate phía client
            if (!currentPassword) {
                alert('❌ Vui lòng nhập mật khẩu hiện tại!');
                return;
            }

            if (!newPassword) {
                alert('❌ Vui lòng nhập mật khẩu mới!');
                return;
            }

            if (newPassword.length < 6) {
                alert('❌ Mật khẩu mới phải có ít nhất 6 ký tự!');
                return;
            }

            if (!confirmPassword) {
                alert('❌ Vui lòng xác nhận mật khẩu mới!');
                return;
            }

            if (newPassword !== confirmPassword) {
                alert('❌ Mật khẩu mới và xác nhận mật khẩu không khớp!');
                return;
            }

            if (currentPassword === newPassword) {
                alert('❌ Mật khẩu mới không được trùng với mật khẩu hiện tại!');
                return;
            }

            // Xác nhận trước khi đổi
            if (!confirm('Bạn có chắc chắn muốn đổi mật khẩu?')) {
                return;
            }

            const formData = new FormData();
            formData.append('current_password', currentPassword);
            formData.append('new_password', newPassword);
            formData.append('confirm_password', confirmPassword);

            fetch('../../php/StaffCTL/ChangePassword.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('✅ ' + data.message);
                    resetPasswordForm();
                    // Tùy chọn: Chuyển về trang đăng nhập
                    // window.location.href = '../../login.php';
                } else {
                    alert('❌ Lỗi: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('❌ Có lỗi xảy ra khi đổi mật khẩu!');
            });
        }

        // Reset form đổi mật khẩu
        function resetPasswordForm() {
            document.getElementById('current_password').value = '';
            document.getElementById('new_password').value = '';
            document.getElementById('confirm_password').value = '';
        }

        // Close modal khi click bên ngoài
        window.onclick = function(event) {
            if(event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        }
 

    </script>
</body>
</html>
<?php
$conn->close();
?>