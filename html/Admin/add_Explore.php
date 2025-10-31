<?php
session_start();
include '../../db/db.php';

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
        // Insert vào bảng khampha
        $sql = "INSERT INTO khampha (tour_id, loai_id, tieu_de, mo_ta_ngan) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $tour_id, $loai_id, $tieu_de, $mo_ta_ngan);
        
        if ($stmt->execute()) {
            $khampha_id = $conn->insert_id;
            
            // Xử lý upload ảnh
            if (!empty($_FILES['hinh_anh']['name'][0])) {
                $upload_dir = '../../uploads/khampha/';
                
                // Tạo thư mục nếu chưa tồn tại
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $total_files = count($_FILES['hinh_anh']['name']);
                
                for ($i = 0; $i < $total_files; $i++) {
                    if ($_FILES['hinh_anh']['error'][$i] == 0) {
                        $file_name = $_FILES['hinh_anh']['name'][$i];
                        $file_tmp = $_FILES['hinh_anh']['tmp_name'][$i];
                        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                        
                        // Validate file extension
                        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        if (in_array($file_ext, $allowed_ext)) {
                            $new_file_name = 'khampha_' . $khampha_id . '_' . time() . '_' . $i . '.' . $file_ext;
                            $destination = $upload_dir . $new_file_name;
                            
                            if (move_uploaded_file($file_tmp, $destination)) {
                                // Lưu đường dẫn vào database
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
            
            $_SESSION['success'] = 'Thêm khám phá thành công!';
            header('Location: ExploreController.php');
            exit();
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $conn->error;
        }
    }
}

// Lấy danh sách tour
$tours = $conn->query("SELECT id, ten_tour FROM tour WHERE trang_thai = 1");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Khám phá mới - Admin</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 800px;
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
        
        .form-group input[type="file"] {
            padding: 10px;
        }
        
        .image-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        
        .image-preview-item {
            position: relative;
            width: 100px;
            height: 100px;
        }
        
        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
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
    </style>
</head>
<body>
  <aside class="sidebar">
    <h2 class="logo">WIND Admin</h2>
    <?php include '../../includes/Adminnav.php';?>
  </aside>

  <div class="main">
    <header class="header">
      <h1>Thêm Khám phá mới</h1>
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
              <option value="1">Làng nghề truyền thống</option>
              <option value="2">Đặc sản địa phương</option>
              <option value="3">Văn hóa - Phong tục</option>
            </select>
          </div>

          <div class="form-group">
            <label>Tour liên quan (tùy chọn)</label>
            <select name="tour_id">
              <option value="">-- Không liên kết tour --</option>
              <?php while($tour = $tours->fetch_assoc()): ?>
                <option value="<?php echo $tour['id']; ?>">
                  <?php echo htmlspecialchars($tour['ten_tour']); ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Tiêu đề <span class="required">*</span></label>
            <input type="text" name="tieu_de" required placeholder="Nhập tiêu đề khám phá...">
          </div>

          <div class="form-group">
            <label>Mô tả ngắn <span class="required">*</span></label>
            <textarea name="mo_ta_ngan" required placeholder="Nhập mô tả ngắn về khám phá này..."></textarea>
          </div>

          <div class="form-group">
            <label>Hình ảnh (có thể chọn nhiều ảnh)</label>
            <input type="file" name="hinh_anh[]" multiple accept="image/*" onchange="previewImages(event)">
            <small style="color: #666; display: block; margin-top: 5px;">
              Chấp nhận: JPG, JPEG, PNG, GIF, WEBP
            </small>
            <div class="image-preview" id="imagePreview"></div>
          </div>

          <div class="btn-group">
            <button type="button" class="btn btn-secondary" onclick="window.location.href='ExploreController.php'">
              <i class="bi bi-x-circle"></i> Hủy
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save"></i> Lưu khám phá
            </button>
          </div>
        </form>
      </div>
    </section>
  </div>

  <script>
    function previewImages(event) {
      const preview = document.getElementById('imagePreview');
      preview.innerHTML = '';
      
      const files = event.target.files;
      
      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const reader = new FileReader();
        
        reader.onload = function(e) {
          const div = document.createElement('div');
          div.className = 'image-preview-item';
          div.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
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