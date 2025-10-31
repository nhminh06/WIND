<?php
session_start();
include '../../db/db.php';

$khampha_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($khampha_id == 0) {
    $_SESSION['error'] = 'ID không hợp lệ!';
    header('Location: ExploreController.php');
    exit();
}

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $loai_id = intval($_POST['loai_id']);
    $tieu_de = trim($_POST['tieu_de']);
    $mo_ta_ngan = trim($_POST['mo_ta_ngan']);
    $tour_id = !empty($_POST['tour_id']) ? intval($_POST['tour_id']) : NULL;
    
    // Validate
    if (empty($loai_id) || empty($tieu_de) || empty($mo_ta_ngan)) {
        $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
    } else {
        // Update bảng khampha
        $sql = "UPDATE khampha SET tour_id = ?, loai_id = ?, tieu_de = ?, mo_ta_ngan = ? WHERE khampha_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissi", $tour_id, $loai_id, $tieu_de, $mo_ta_ngan, $khampha_id);
        
        if ($stmt->execute()) {
            // Xử lý xóa ảnh cũ nếu có
            if (!empty($_POST['delete_images'])) {
                $delete_ids = $_POST['delete_images'];
                foreach ($delete_ids as $img_id) {
                    // Lấy đường dẫn ảnh để xóa file
                    $sql_get = "SELECT duong_dan_anh FROM khampha_anh WHERE anh_id = ?";
                    $stmt_get = $conn->prepare($sql_get);
                    $stmt_get->bind_param("i", $img_id);
                    $stmt_get->execute();
                    $result_get = $stmt_get->get_result();
                    if ($row = $result_get->fetch_assoc()) {
                        if (file_exists($row['duong_dan_anh'])) {
                            unlink($row['duong_dan_anh']);
                        }
                    }
                    
                    // Xóa record trong database
                    $sql_del = "DELETE FROM khampha_anh WHERE anh_id = ?";
                    $stmt_del = $conn->prepare($sql_del);
                    $stmt_del->bind_param("i", $img_id);
                    $stmt_del->execute();
                }
            }
            
            // Xử lý upload ảnh mới
            if (!empty($_FILES['hinh_anh']['name'][0])) {
                $upload_dir = '../../uploads/khampha/';
                
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $total_files = count($_FILES['hinh_anh']['name']);
                
                for ($i = 0; $i < $total_files; $i++) {
                    if ($_FILES['hinh_anh']['error'][$i] == 0) {
                        $file_name = $_FILES['hinh_anh']['name'][$i];
                        $file_tmp = $_FILES['hinh_anh']['tmp_name'][$i];
                        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                        
                        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        if (in_array($file_ext, $allowed_ext)) {
                            $new_file_name = 'khampha_' . $khampha_id . '_' . time() . '_' . $i . '.' . $file_ext;
                            $destination = $upload_dir . $new_file_name;
                            
                            if (move_uploaded_file($file_tmp, $destination)) {
                                $duong_dan = $upload_dir . $new_file_name;
                                $sql_img = "INSERT INTO khampha_anh (khampha_id, duong_dan_anh) VALUES (?, ?)";
                                $stmt_img = $conn->prepare($sql_img);
                                $stmt_img->bind_param("is", $khampha_id, $duong_dan);
                                $stmt_img->execute();
                            }
                        }
                    }
                }
            }
            
            $_SESSION['success'] = 'Cập nhật khám phá thành công!';
            header('Location: ExploreController.php');
            exit();
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $conn->error;
        }
    }
}

// Lấy thông tin khám phá
$sql = "SELECT * FROM khampha WHERE khampha_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $khampha_id);
$stmt->execute();
$result = $stmt->get_result();
$khampha = $result->fetch_assoc();

if (!$khampha) {
    $_SESSION['error'] = 'Không tìm thấy khám phá!';
    header('Location: ExploreController.php');
    exit();
}

// Lấy danh sách ảnh
$sql_img = "SELECT * FROM khampha_anh WHERE khampha_id = ?";
$stmt_img = $conn->prepare($sql_img);
$stmt_img->bind_param("i", $khampha_id);
$stmt_img->execute();
$images = $stmt_img->get_result();

// Lấy danh sách tour
$tours = $conn->query("SELECT id, ten_tour FROM tour WHERE trang_thai = 1");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa Khám phá - Admin</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 900px;
            margin: 0 auto;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group label .required {
            color: red;
        }
        
        .form-group input[type="text"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .existing-images {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }
        
        .existing-image-item {
            position: relative;
            border: 3px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .existing-image-item.marked-delete {
            border-color: #dc3545;
            opacity: 0.5;
        }
        
        .existing-image-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            display: block;
        }
        
        .delete-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            text-align: center;
            padding: 8px;
            font-size: 13px;
            font-weight: 600;
            display: none;
        }
        
        .existing-image-item.marked-delete .delete-overlay {
            display: block;
        }
        
        .delete-checkbox-label {
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            font-size: 12px;
            font-weight: 600;
            z-index: 10;
        }
        
        .delete-checkbox-label input {
            margin-right: 5px;
        }
        
        .image-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        
        .image-preview-item {
            width: 100%;
            height: 120px;
        }
        
        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #28a745;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 30px;
        }
        
        .btn {
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
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #667eea;
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
      <h1>Chỉnh sửa Khám phá</h1>
      <div class="admin-info">
        <?php echo "<p>Xin chào " . (isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin') . "</p>"; ?>
        <button onclick="window.location.href='../views/user/users.php'" class="logout">Đăng xuất</button>
      </div>
    </header>

    <section class="content">
      <?php if(isset($_SESSION['error'])): ?>
      <div class="alert alert-error">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <?php 
          echo $_SESSION['error']; 
          unset($_SESSION['error']);
          ?>
      </div>
      <?php endif; ?>

      <div class="form-container">
        <form method="POST" action="" enctype="multipart/form-data">
          <div class="form-group">
            <label>Loại khám phá <span class="required">*</span></label>
            <select name="loai_id" required>
              <option value="">-- Chọn loại --</option>
              <option value="1" <?php echo ($khampha['loai_id'] == 1) ? 'selected' : ''; ?>>Làng nghề truyền thống</option>
              <option value="2" <?php echo ($khampha['loai_id'] == 2) ? 'selected' : ''; ?>>Đặc sản địa phương</option>
              <option value="3" <?php echo ($khampha['loai_id'] == 3) ? 'selected' : ''; ?>>Văn hóa - Phong tục</option>
            </select>
          </div>

          <div class="form-group">
            <label>Tour liên quan (tùy chọn)</label>
            <select name="tour_id">
              <option value="">-- Không liên kết tour --</option>
              <?php while($tour = $tours->fetch_assoc()): ?>
                <option value="<?php echo $tour['id']; ?>" <?php echo ($khampha['tour_id'] == $tour['id']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($tour['ten_tour']); ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Tiêu đề <span class="required">*</span></label>
            <input type="text" name="tieu_de" required value="<?php echo htmlspecialchars($khampha['tieu_de']); ?>">
          </div>

          <div class="form-group">
            <label>Mô tả ngắn <span class="required">*</span></label>
            <textarea name="mo_ta_ngan" required><?php echo htmlspecialchars($khampha['mo_ta_ngan']); ?></textarea>
          </div>

          <?php if($images->num_rows > 0): ?>
          <div class="form-group">
            <label class="section-title">
              <i class="bi bi-images"></i> Hình ảnh hiện tại (<?php echo $images->num_rows; ?> ảnh)
            </label>
            <div class="existing-images">
              <?php 
              $images->data_seek(0); // Reset pointer
              while($img = $images->fetch_assoc()): 
              ?>
              <div class="existing-image-item" id="img-<?php echo $img['anh_id']; ?>">
                <label class="delete-checkbox-label">
                  <input type="checkbox" 
                         name="delete_images[]" 
                         value="<?php echo $img['anh_id']; ?>"
                         onchange="toggleDeleteMark(<?php echo $img['anh_id']; ?>)">
                  Xóa
                </label>
                <img src="<?php echo htmlspecialchars($img['duong_dan_anh']); ?>" alt="">
                <div class="delete-overlay">
                  <i class="bi bi-trash"></i> Sẽ xóa ảnh này
                </div>
              </div>
              <?php endwhile; ?>
            </div>
            <small style="color: #666; display: block; margin-top: 8px;">
              <i class="bi bi-info-circle"></i> Chọn checkbox "Xóa" trên ảnh để đánh dấu xóa
            </small>
          </div>
          <?php endif; ?>

          <div class="form-group">
            <label class="section-title">
              <i class="bi bi-cloud-upload"></i> Thêm hình ảnh mới (tùy chọn)
            </label>
            <input type="file" name="hinh_anh[]" multiple accept="image/*" onchange="previewImages(event)">
            <small style="color: #666; display: block; margin-top: 5px;">
              Chấp nhận: JPG, JPEG, PNG, GIF, WEBP. Có thể chọn nhiều ảnh cùng lúc.
            </small>
            <div class="image-preview" id="imagePreview"></div>
          </div>

          <div class="btn-group">
            <button type="button" class="btn btn-secondary" onclick="window.location.href='ExploreController.php'">
              <i class="bi bi-x-circle"></i> Hủy
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save"></i> Cập nhật
            </button>
          </div>
        </form>
      </div>
    </section>
  </div>

  <script>
    function toggleDeleteMark(imgId) {
      const imgItem = document.getElementById('img-' + imgId);
      imgItem.classList.toggle('marked-delete');
    }
    
    function previewImages(event) {
      const preview = document.getElementById('imagePreview');
      preview.innerHTML = '';
      
      const files = event.target.files;
      
      if (files.length > 0) {
        preview.innerHTML = '<p style="color: #28a745; font-weight: 600; margin-bottom: 10px;"><i class="bi bi-check-circle"></i> ' + files.length + ' ảnh mới sẽ được thêm:</p>';
      }
      
      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const reader = new FileReader();
        
        reader.onload = function(e) {
          const div = document.createElement('div');
          div.className = 'image-preview-item';
          div.innerHTML = `<img src="${e.target.result}" alt="Preview ${i+1}">`;
          preview.appendChild(div);
        }
        
        reader.readAsDataURL(file);
      }
    }
  </script>
</body>
</html>
<?php
$conn->close();
?>