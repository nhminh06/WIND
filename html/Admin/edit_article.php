<?php session_start();
include '../../db/db.php';

// Lấy ID bài viết từ URL
$bai_viet_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($bai_viet_id == 0) {
    $_SESSION['error'] = "ID bài viết không hợp lệ!";
    header("Location: ArticleController.php");
    exit();
}

// Lấy thông tin bài viết - SỬA: lấy thêm loai_id từ khampha
$sql_baiviet = "SELECT bv.*, kp.tieu_de as khampha_tieude, kp.loai_id, kp.tour_id 
                FROM bai_viet bv
                LEFT JOIN khampha kp ON bv.khampha_id = kp.khampha_id
                WHERE bv.id = ?";
$stmt = $conn->prepare($sql_baiviet);
$stmt->bind_param("i", $bai_viet_id);
$stmt->execute();
$result = $stmt->get_result();
$baiviet = $result->fetch_assoc();

if (!$baiviet) {
    $_SESSION['error'] = "Không tìm thấy bài viết!";
    header("Location: ArticleController.php");
    exit();
}

// Lấy các mục của bài viết
$sql_muc = "SELECT * FROM bai_viet_muc WHERE bai_viet_id = ? ORDER BY id";
$stmt_muc = $conn->prepare($sql_muc);
$stmt_muc->bind_param("i", $bai_viet_id);
$stmt_muc->execute();
$result_muc = $stmt_muc->get_result();
$mucs = [];
while ($muc = $result_muc->fetch_assoc()) {
    $mucs[] = $muc;
}

// Lấy danh sách tour
$sql_tour = "SELECT id, ten_tour FROM tour WHERE trang_thai = 1 ORDER BY ten_tour";
$result_tour = $conn->query($sql_tour);

// Lấy danh sách khám phá
$sql_khampha = "SELECT k.khampha_id, k.tieu_de, kl.ten_loai, k.loai_id, k.tour_id
                FROM khampha k
                JOIN khampha_loai kl ON k.loai_id = kl.loai_id
                ORDER BY kl.loai_id, k.tieu_de";
$result_khampha = $conn->query($sql_khampha);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa bài viết - Admin</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        /* CSS giữ nguyên */
    </style>
</head>
<body>
 <aside class="sidebar">
  <h2 class="logo">Sửa bài viết</h2>
  <?php include '../../includes/Adminnav.php';?>
</aside>

<div class="main">
  <header class="header">
    <h1>Bảng điều khiển</h1>
    <div class="admin-info">
      <?php echo "<p>Xin chào " . $_SESSION['username'] . "</p>"; ?>
      <button onclick="window.location.href='../views/index/webindex.php'" class="logout">Trở lại</button>
    </div>
  </header>

    <section class="content">
      <div class="form-container">
        <div class="form-header">
          <h2><i class="bi bi-pencil-square"></i> Chỉnh sửa bài viết</h2>
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

        <form id="editArticleForm" action="../../php/ArticleCTL/edit_article_process.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="bai_viet_id" value="<?php echo $bai_viet_id; ?>">
          
          <!-- Thông tin cơ bản -->
          <div class="form-row">
            <div class="form-group">
              <label>Tour liên quan</label>
              <select name="tour_id" class="form-control">
                <option value="">-- Không liên kết tour --</option>
                <?php 
                if($result_tour && $result_tour->num_rows > 0) {
                    while($tour = $result_tour->fetch_assoc()) {
                        $selected = (isset($baiviet['tour_id']) && $tour['id'] == $baiviet['tour_id']) ? 'selected' : '';
                        echo '<option value="'.$tour['id'].'" '.$selected.'>'.$tour['ten_tour'].'</option>';
                    }
                }
                ?>
              </select>
            </div>

            <div class="form-group">
              <label class="required">Loại bài viết</label>
              <select name="loai_id" class="form-control" required>
                <option value="">-- Chọn loại --</option>
                <option value="1" <?php echo (isset($baiviet['loai_id']) && $baiviet['loai_id'] == 1) ? 'selected' : ''; ?>>Làng nghề</option>
                <option value="2" <?php echo (isset($baiviet['loai_id']) && $baiviet['loai_id'] == 2) ? 'selected' : ''; ?>>Ẩm thực</option>
                <option value="3" <?php echo (isset($baiviet['loai_id']) && $baiviet['loai_id'] == 3) ? 'selected' : ''; ?>>Văn hóa</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="required">Tiêu đề bài viết</label>
            <input type="text" name="tieu_de" class="form-control" value="<?php echo htmlspecialchars($baiviet['tieu_de']); ?>" required>
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
                      $selected = ($kp['khampha_id'] == $baiviet['khampha_id']) ? 'selected' : '';
                      echo '<option value="'.$kp['khampha_id'].'" '.$selected.' data-loai-id="'.$kp['loai_id'].'" data-tour-id="'.$kp['tour_id'].'">'.$kp['tieu_de'].'</option>';
                  }
                  if($current_loai != '') echo '</optgroup>';
              }
              ?>
            </select>
            <span class="help-text">Khi chọn mục khám phá, loại bài viết và tour liên quan sẽ tự động cập nhật</span>
          </div>

          <hr class="section-divider">

          <!-- Nội dung chi tiết -->
          <h3 class="section-title"><i class="bi bi-list-ol"></i> Nội dung chi tiết</h3>
          
          <div id="mucContainer">
            <?php 
            $muc_count = 0;
            foreach($mucs as $index => $muc): 
                $muc_count++;
            ?>
            <div class="muc-section" data-muc="<?php echo $muc_count; ?>" data-muc-id="<?php echo $muc['id']; ?>">
              <input type="hidden" name="muc_id[]" value="<?php echo $muc['id']; ?>">
              
              <div class="muc-header">
                <h4><i class="bi bi-card-text"></i> Mục <?php echo $muc_count; ?></h4>
                <button type="button" class="btn-remove-muc" onclick="removeMuc(this)">
                  <i class="bi bi-trash"></i> Xóa
                </button>
              </div>
              
              <div class="form-group">
                <label>Tiêu đề mục</label>
                <input type="text" name="tieu_de_muc[]" class="form-control" value="<?php echo htmlspecialchars($muc['tieu_de_muc']); ?>" placeholder="Nhập tiêu đề hoặc để trống">
                <span class="help-text">Để trống nếu không muốn hiển thị tiêu đề cho mục này</span>
              </div>

              <div class="form-group">
                <label class="required">Nội dung</label>
                <textarea name="noi_dung_muc[]" class="form-control" required><?php echo htmlspecialchars($muc['noi_dung']); ?></textarea>
              </div>

              <div class="form-group">
                <label>Hình ảnh mục</label>
                <?php if(!empty($muc['hinh_anh'])): ?>
                <div class="image-info">
                  <i class="bi bi-image"></i>
                  <span>Ảnh hiện tại: <?php echo basename($muc['hinh_anh']); ?></span>
                </div>
                <div class="current-image">
                  <img src="../../<?php echo $muc['hinh_anh']; ?>" alt="Ảnh hiện tại">
                </div>
                <input type="hidden" name="old_hinh_anh[]" value="<?php echo $muc['hinh_anh']; ?>">
                <?php else: ?>
                <input type="hidden" name="old_hinh_anh[]" value="">
                <?php endif; ?>
                <input type="file" name="hinh_anh_muc[]" class="form-control" accept="image/*">
                <span class="help-text">Để trống nếu không muốn thay đổi ảnh. Định dạng: JPG, PNG, GIF, WEBP. Max: 5MB</span>
              </div>
            </div>
            <?php endforeach; ?>
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
              <i class="bi bi-check-circle"></i> Cập nhật bài viết
            </button>
          </div>
        </form>
      </div>
    </section>
  </div>

  <script>
    let mucCount = <?php echo count($mucs); ?>;

    // Thêm mục mới
    function addMuc() {
      mucCount++;
      const mucContainer = document.getElementById('mucContainer');
      const newMuc = `
        <div class="muc-section" data-muc="${mucCount}" data-muc-id="0">
          <input type="hidden" name="muc_id[]" value="0">
          
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
            <textarea name="noi_dung_muc[]" class="form-control" placeholder="Nhập nội dung chi tiết" required></textarea>
          </div>

          <div class="form-group">
            <label>Hình ảnh mục</label>
            <input type="hidden" name="old_hinh_anh[]" value="">
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

    // Tự động cập nhật loại bài viết và tour khi chọn mục khám phá
    document.querySelector('select[name="khampha_id"]').addEventListener('change', function() {
      const selectedOption = this.options[this.selectedIndex];
      if (selectedOption.value) {
        const loaiId = selectedOption.getAttribute('data-loai-id');
        const tourId = selectedOption.getAttribute('data-tour-id');
        
        // Cập nhật loại bài viết
        if (loaiId) {
          document.querySelector('select[name="loai_id"]').value = loaiId;
        }
        
        // Cập nhật tour liên quan
        if (tourId) {
          document.querySelector('select[name="tour_id"]').value = tourId;
        } else {
          document.querySelector('select[name="tour_id"]').value = '';
        }
      }
    });

    // Validate form trước khi submit
    document.getElementById('editArticleForm').addEventListener('submit', function(e) {
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

      return confirm('Bạn có chắc muốn cập nhật bài viết này?');
    });
  </script>
</body>
</html>
<?php
$conn->close();
?>