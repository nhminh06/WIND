<?php
session_start();
include '../../db/db.php';

// Lấy danh sách tour
$sql_tour = "SELECT id, ten_tour FROM tour WHERE trang_thai = 1 ORDER BY ten_tour";
$result_tour = $conn->query($sql_tour);
$sql_khampha = "SELECT k.khampha_id, k.tieu_de, kl.ten_loai 
                FROM khampha k
                JOIN khampha_loai kl ON k.loai_id = kl.loai_id
                ORDER BY kl.loai_id, k.tieu_de";
$result_khampha = $conn->query($sql_khampha);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm bài viết mới - Admin</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
 <aside class="sidebar">
  <h2 class="logo">Thêm bài viết</h2>
  <?php include '../../includes/Adminnav.php';?>
</aside>

<div class="main">
  <header class="header">
    <h1>Bảng điều khiển</h1>
    <div class="admin-info">
      <?php echo "<p>Xin chào " . $_SESSION['username'] . "</p>"; ?>
      <button onclick="window.location.href='../views/user/users.php'" class="logout">Đăng xuất</button>
    </div>
  </header>

    <section class="content">
      <div class="form-container">
        <div class="form-header">
          <h2><i class="bi bi-file-earmark-text"></i> Thông tin bài viết</h2>
          <a href="ArticleController.php" class="btn-back">
            <i class="bi bi-arrow-left"></i>
            Quay lại
          </a>
        </div>

        <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i>
            <?php 
            echo $_SESSION['success']; 
            unset($_SESSION['success']);
            ?>
        </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
            ?>
        </div>
        <?php endif; ?>

        <form id="addArticleForm" action="../../php/ArticleCTL/add_article_process.php" method="POST" enctype="multipart/form-data">
          
          <!-- Thông tin cơ bản -->
          <div class="form-row">
            <div class="form-group">
              <label>Tour liên quan</label>
              <select name="tour_id" class="form-control">
                <option value="">-- Không liên kết tour --</option>
                <?php 
                if($result_tour && $result_tour->num_rows > 0) {
                    while($tour = $result_tour->fetch_assoc()) {
                        echo '<option value="'.$tour['id'].'">'.$tour['ten_tour'].'</option>';
                    }
                }
                ?>
              </select>
            </div>

            <div class="form-group">
              <label class="required">Loại bài viết</label>
              <select name="loai_id" class="form-control" required>
                <option value="">-- Chọn loại --</option>
                <option value="1">Làng nghề</option>
                <option value="2">Ẩm thực</option>
                <option value="3">Văn hóa</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="required">Tiêu đề bài viết</label>
            <input type="text" name="tieu_de" class="form-control" placeholder="Nhập tiêu đề bài viết" required>
          </div>

          <div class="form-group">
            <label class="required">Kết nối với mục Khám phá</label>
            <select name="khampha_id" class="form-control" required>
              <option value="">-- Chọn mục khám phá --</option>
              <?php 
              if($result_khampha && $result_khampha->num_rows > 0) {
                  $current_loai = '';
                  while($kp = $result_khampha->fetch_assoc()) {
                      if($current_loai != $kp['ten_loai']) {
                          if($current_loai != '') echo '</optgroup>';
                          echo '<optgroup label="'.$kp['ten_loai'].'">';
                          $current_loai = $kp['ten_loai'];
                      }
                      echo '<option value="'.$kp['khampha_id'].'">'.$kp['tieu_de'].'</option>';
                  }
                  if($current_loai != '') echo '</optgroup>';
              }
              ?>
            </select>
          </div>

          <hr class="section-divider">

          <!-- Nội dung chi tiết -->
          <h3 class="section-title"><i class="bi bi-list-ol"></i> Nội dung chi tiết</h3>
          
          <div id="mucContainer">
            <!-- Mục 1 mặc định -->
            <div class="muc-section" data-muc="1">
              <div class="muc-header">
                <h4><i class="bi bi-card-text"></i> Mục 1</h4>
                <button type="button" class="btn-remove-muc" onclick="removeMuc(this)">
                  <i class="bi bi-trash"></i> Xóa
                </button>
              </div>
              
              <div class="form-group">
                <label>Tiêu đề mục</label>
                <input type="text" name="tieu_de_muc[]" class="form-control" placeholder="Nhập tiêu đề hoặc để trống">
                <span class="help-text">Để trống nếu không muốn hiển thị tiêu đề cho mục này</span>
              </div>

              <div class="form-group">
                <label class="required">Nội dung</label>
                <textarea name="noi_dung_muc[]" class="form-control" placeholder="Nhập nội dung chi tiết cho mục này" required></textarea>
              </div>

              <div class="form-group">
                <label>Hình ảnh mục</label>
                <input type="file" name="hinh_anh_muc[]" class="form-control" accept="image/*">
                <span class="help-text">Định dạng: JPG, PNG, GIF, WEBP. Kích thước tối đa: 5MB</span>
              </div>
            </div>
          </div>
          
          <button type="button" class="btn-add-muc" onclick="addMuc()">
            <i class="bi bi-plus-circle"></i>
            Thêm mục mới
          </button>
          
          <!-- Action Buttons -->
          <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="confirmCancel()">
              <i class="bi bi-x-circle"></i> Hủy bỏ
            </button>
            <button type="submit" class="btn-submit">
              <i class="bi bi-check-circle"></i> Lưu bài viết
            </button>
          </div>
        </form>
      </div>
    </section>
  </div>

  <script>
    let mucCount = 1;

    // Thêm mục mới
    function addMuc() {
      mucCount++;
      const mucContainer = document.getElementById('mucContainer');
      const newMuc = `
        <div class="muc-section" data-muc="${mucCount}">
          <div class="muc-header">
            <h4><i class="bi bi-card-text"></i> Mục ${mucCount}</h4>
            <button type="button" class="btn-remove-muc" onclick="removeMuc(this)">
              <i class="bi bi-trash"></i> Xóa
            </button>
          </div>
          
          <div class="form-group">
            <label>Tiêu đề mục</label>
            <input type="text" name="tieu_de_muc[]" class="form-control" placeholder="Nhập tiêu đề hoặc để trống">
            <span class="help-text">Để trống nếu không muốn hiển thị tiêu đề cho mục này</span>
          </div>

          <div class="form-group">
            <label class="required">Nội dung</label>
            <textarea name="noi_dung_muc[]" class="form-control" placeholder="Nhập nội dung chi tiết cho mục này" required></textarea>
          </div>

          <div class="form-group">
            <label>Hình ảnh mục</label>
            <input type="file" name="hinh_anh_muc[]" class="form-control" accept="image/*">
            <span class="help-text">Định dạng: JPG, PNG, GIF, WEBP. Kích thước tối đa: 5MB</span>
          </div>
        </div>
      `;
      mucContainer.insertAdjacentHTML('beforeend', newMuc);
    }

    // Xóa mục
    function removeMuc(button) {
      const mucSection = button.closest('.muc-section');
      const allMuc = document.querySelectorAll('.muc-section');
      
      if (allMuc.length > 1) {
        if(confirm('Bạn có chắc muốn xóa mục này?')) {
          mucSection.remove();
          updateMucNumbers();
        }
      } else {
        alert('Phải có ít nhất 1 mục nội dung!');
      }
    }

    // Cập nhật số thứ tự mục
    function updateMucNumbers() {
      const mucSections = document.querySelectorAll('.muc-section');
      mucSections.forEach((section, index) => {
        const h4 = section.querySelector('h4');
        h4.innerHTML = `<i class="bi bi-card-text"></i> Mục ${index + 1}`;
        section.setAttribute('data-muc', index + 1);
      });
      mucCount = mucSections.length;
    }

    // Xác nhận hủy
    function confirmCancel() {
      if (confirm('Bạn có chắc muốn hủy? Mọi thay đổi sẽ không được lưu.')) {
        window.location.href = 'ArticleController.php';
      }
    }

    // Validate form trước khi submit
    document.getElementById('addArticleForm').addEventListener('submit', function(e) {
      const allFields = this.querySelectorAll('input, select, textarea');
      let isValid = true;
      const blankChar = ' '; // Khoảng trống đơn giản

      allFields.forEach(field => {
        const name = field.getAttribute('name');
        const isRequired = field.hasAttribute('required');

        // Xử lý riêng cho tiêu đề mục - tự động gán khoảng trống nếu để trống
        if (name && name.includes('tieu_de_muc')) {
          if (!field.value.trim()) {
            field.value = blankChar; // Gán khoảng trống
          }
          field.style.borderColor = '#ddd';
        } 
        // Xử lý các trường required khác
        else if (isRequired && !field.value.trim()) {
          isValid = false;
          field.style.borderColor = '#e53935';
        } else if (field.value.trim()) {
          field.style.borderColor = '#ddd';
        }
      });

      if (!isValid) {
        e.preventDefault();
        alert('Vui lòng điền đầy đủ các trường bắt buộc!');
        return false;
      }

      return confirm('Bạn có chắc muốn lưu bài viết này?');
    });
  </script>
</body>
</html>
<?php
$conn->close();
?>