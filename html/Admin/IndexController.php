<?php
session_start();
include '../../db/db.php';

// Lấy dữ liệu từ CSDL
$sql = "SELECT * FROM banner LIMIT 4";
$result = mysqli_query($conn, $sql);

// Tạo mảng 4 phần tử (trống mặc định)
$rows = array_fill(0, 4, ["id"=>"", "tieu_de" => "", "noi_dung" => "", "hinh_anh" => "", "hinh_anh2" => "", "link" => ""]);

// Đổ dữ liệu thật vào mảng
$i = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $rows[$i] = $row;
    $i++;
}

// Lấy danh sách bài viết từ CSDL
$sql_bai_viet = "SELECT 
                    k.khampha_id,
                    k.tieu_de,
                    bv.id as bai_viet_id
                 FROM khampha k
                 LEFT JOIN bai_viet bv ON k.khampha_id = bv.khampha_id
                 WHERE k.trang_thai = 1 AND bv.trang_thai = 1
                 ORDER BY k.tieu_de";
$result_bai_viet = mysqli_query($conn, $sql_bai_viet);
$bai_viets = [];
while ($row = mysqli_fetch_assoc($result_bai_viet)) {
    $bai_viets[] = $row;
}

$sql_slider = "SELECT * FROM banner_slider ORDER BY thu_tu LIMIT 4";
$result_slider = mysqli_query($conn, $sql_slider);

// Tạo mảng 4 phần tử cho slider
$slider_images = array_fill(0, 4, [
    "id" => "", 
    "tieu_de" => "", 
    "mo_ta" => "", 
    "hinh_anh" => "", 
    "thu_tu" => ""
]);

$i = 0;
while ($row = mysqli_fetch_assoc($result_slider)) {
    $slider_images[$i] = $row;
    $i++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin</title>
  <link rel="stylesheet" href="../../css/Admin.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    /* Modal sửa */
    .modal {
      display: none;
      position: fixed;
      top:0; left:0;
      width:100%; height:100%;
      background: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }
    .modal-content {
      background:#15325f;
      padding:20px;
      border-radius:8px;
      width:500px;
      max-height: 90vh;
      overflow-y: auto;
    }
    .modal-content h3 {
      color: #fff;
      margin-bottom: 15px;
    }
    .modal-content label {
      color: #fff;
      display: block;
      margin-top: 10px;
      margin-bottom: 5px;
    }
    .modal-content-button {
      margin-top:10px;
      padding:8px 12px;
      border:none;
      border-radius:4px;
      cursor:pointer;
    }
    .modal-content input, .modal-content textarea, .modal-content select {
      width:100%;
      margin:5px 0;
      padding:8px;
      resize: none;
      box-sizing: border-box;
    }
    .slider-note {
      background: #e3f2fd;
      border-left: 4px solid #2196F3;
      padding: 12px 15px;
      margin-bottom: 20px;
      border-radius: 4px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .slider-note i {
      color: #2196F3;
      font-size: 20px;
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
    <button class="menu-toggle">
        <span></span>
        <span></span>
        <span></span>
    </button>
    <h1>Bảng điều khiển</h1>
    <div class="admin-info">
      <?php echo "<p>Xin chào " . $_SESSION['username'] . "</p>"; ?>
      <button onclick="window.location.href='../views/index/webindex.php'" class="logout">Trở lại</button>
    </div>
  </header>
<section class="content">
    <div class="slider-note">
      <i class="bi bi-info-circle"></i> Quản lý 4 ảnh banner chính hiển thị ở đầu trang web
    </div>
    
    <h2>Danh sách Banner Slider</h2>
    <table>
      <thead>
        <tr>
          <th style="width: 10%;">Thứ tự</th>
          <th style="width: 20%;">Tiêu đề</th>
          <th style="width: 25%;">Mô tả</th>
          <th style="width: 30%;">Hình ảnh</th>
          <th style="width: 15%;">Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($slider_images as $index => $slide): ?>
        <tr data-id="<?= $slide['id'] ?>">
          <td>Slide <?= $index + 1 ?></td>
          <td><?= htmlspecialchars($slide['tieu_de']) ?></td>
          <td><textarea rows="2" readonly><?= htmlspecialchars($slide['mo_ta']) ?></textarea></td>
          <td>
            <?php if ($slide['hinh_anh']): ?>
              <img src="<?php echo "../../" . htmlspecialchars($slide['hinh_anh']) ?>" width="200" style="border-radius: 5px;">
            <?php else: ?>
              <span style="color: #999;">Chưa có ảnh</span>
            <?php endif; ?>
          </td>
          <td>
            <button class="edit" onclick="openEditSlider(this, <?= $index ?>)">
              <i class="bi bi-pen-fill"></i> Sửa
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <section class="content">
    <h2>Địa điểm nổi bật</h2>
    <table>
      <thead>
        <tr>
          <th style="width: 20%;">Tên địa điểm</th>
          <th style="width: 30%;">Mô tả ngắn</th>
          <th style="width: 20%;">Bài viết liên kết</th>
          <th style="width: 20%;">Hình ảnh</th>
          <th style="width: 10%;">Hành động</th>
        </tr>
      </thead>
      <tbody id="tourTable">
        <?php foreach ($rows as $r): ?>
        <tr data-id="<?= $r['id'] ?>" data-link="<?= htmlspecialchars($r['link']) ?>">
          <td><?= htmlspecialchars($r['tieu_de']) ?></td>
          <td><textarea rows="3" readonly><?= htmlspecialchars($r['noi_dung']) ?></textarea></td>
          <td>
            <?php 
            if (!empty($r['link'])) {
                // Tìm tên bài viết từ link
                $bai_viet_hien_tai = '';
                foreach ($bai_viets as $bv) {
                    if (strpos($r['link'], 'id=' . $bv['khampha_id']) !== false) {
                        $bai_viet_hien_tai = $bv['tieu_de'];
                        break;
                    }
                }
                echo htmlspecialchars($bai_viet_hien_tai ?: 'Bài viết không tồn tại');
            } else {
                echo 'Chưa chọn bài viết';
            }
            ?>
          </td>
          <td>
            <div class="img2ad">
              <?php if ($r['hinh_anh']): ?>
                <img src="<?= htmlspecialchars($r['hinh_anh']) ?>" width="100">
              <?php endif; ?>
              <?php if ($r['hinh_anh2']): ?>
                <img src="<?= htmlspecialchars($r['hinh_anh2']) ?>" width="100">
              <?php endif; ?>
            </div>
          </td>
          <td>
            <button class="edit" onclick="openEditForm(this)"><i class="bi bi-pen-fill"></i></button>
            <?php if ($r['id']): ?>
              <button class="delete" onclick="confirmXoa(<?= $r['id'] ?>)"><i class="bi bi-trash3-fill"></i></button>
            <?php else: ?>
              <button class="delete" disabled>Xóa</button>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</div>

<!-- Modal sửa địa điểm -->
<div id="modalSua" class="modal">
  <div class="modal-content">
    <h3>Sửa địa điểm</h3>
    <form method="POST" action="../../php/IndexBannerCTL/UDBanner.php" enctype="multipart/form-data">
      <input type="hidden" name="id" id="idSua">

      <label>Tiêu đề</label>
      <input type="text" name="ten" id="tenSua" required>

      <label>Mô tả</label>
      <textarea name="mota" id="motaSua" rows="3" required></textarea>

      <label>Chọn bài viết liên kết</label>
      <select name="bai_viet" id="baiVietSua">
        <option value="">-- Chọn bài viết --</option>
        <?php foreach ($bai_viets as $bv): ?>
          <option value="<?= $bv['khampha_id'] ?>"><?= htmlspecialchars($bv['tieu_de']) ?></option>
        <?php endforeach; ?>
      </select>
      <small style="color: #ccc; font-size: 12px;">Bài viết được chọn sẽ hiển thị khi click vào banner</small>

      <label>Ảnh 1</label>
      <input type="file" name="anh1" id="anh1Sua" accept="image/*" onchange="previewImage(this, 'preview1')">
      <img id="preview1" src="" width="100" style="margin-top:5px;display:block">
      <p id="link1" style="font-size:12px;color:#ccc;"></p>

      <label>Ảnh 2</label>
      <input type="file" name="anh2" id="anh2Sua" accept="image/*" onchange="previewImage(this, 'preview2')">
      <img id="preview2" src="" width="100" style="margin-top:5px;display:block">
      <p id="link2" style="font-size:12px;color:#ccc;"></p>

      <button class="modal-content-button" type="submit">Lưu</button>
      <button class="modal-content-button" type="button" onclick="closeEditForm()">Hủy</button>
    </form>
  </div>
</div>

<!-- Modal sửa slider -->
<div id="modalSlider" class="modal">
  <div class="modal-content">
    <h3>Sửa Banner Slider</h3>
    <form method="POST" action="../../php/IndexBannerCTL/UpdateSlider.php" enctype="multipart/form-data">
      <input type="hidden" name="id" id="sliderId">
      <input type="hidden" name="thu_tu" id="sliderThuTu">

      <label>Tiêu đề</label>
      <input type="text" name="tieu_de" id="sliderTieuDe" required>

      <label>Mô tả</label>
      <textarea name="mo_ta" id="sliderMoTa" rows="3" required></textarea>

      <label>Hình ảnh</label>
      <input type="file" name="hinh_anh" id="sliderHinhAnh" accept="image/*" onchange="previewSliderImage(this)">
      <img id="sliderPreview" src="" width="200" style="margin-top:10px;display:none;border-radius:5px;">
      <p id="sliderCurrentImage" style="font-size:12px;color:#ccc;margin-top:5px;"></p>

      <button class="modal-content-button" type="submit">Lưu</button>
      <button class="modal-content-button" type="button" onclick="closeSliderModal()">Hủy</button>
    </form>
  </div>
</div>

<div class="sidebar-overlay"></div>

<script src="../../js/Main5.js"></script>
<script>
// Sửa địa điểm
function openEditForm(btn) {
  let row = btn.closest("tr");
  document.getElementById("idSua").value = row.getAttribute("data-id");
  document.getElementById("tenSua").value = row.cells[0].innerText;
  document.getElementById("motaSua").value = row.cells[1].querySelector("textarea").value;

  // Lấy link từ data attribute
  let currentLink = row.getAttribute("data-link");
  if (currentLink) {
    let match = currentLink.match(/id=(\d+)/);
    if (match) {
      document.getElementById("baiVietSua").value = match[1];
    }
  }

  let imgs = row.cells[3].querySelectorAll("img");

  // Ảnh 1
  if (imgs[0]) {
    document.getElementById("preview1").src = imgs[0].src;
    document.getElementById("preview1").style.display = "block";
    document.getElementById("link1").innerText = imgs[0].src;
  } else {
    document.getElementById("preview1").style.display = "none";
    document.getElementById("link1").innerText = "";
  }

  // Ảnh 2
  if (imgs[1]) {
    document.getElementById("preview2").src = imgs[1].src;
    document.getElementById("preview2").style.display = "block";
    document.getElementById("link2").innerText = imgs[1].src;
  } else {
    document.getElementById("preview2").style.display = "none";
    document.getElementById("link2").innerText = "";
  }

  document.getElementById("modalSua").style.display = "flex";
}

function closeEditForm() {
  document.getElementById("modalSua").style.display = "none";
}

function confirmXoa(id) {
  if (confirm("⚠️ Bạn có chắc chắn muốn xóa địa điểm này không?")) {
    window.location.href = '../../php/IndexBannerCTL/DLTBanner.php?id=' + id;
  }
}

function previewImage(input, previewId) {
  const file = input.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      let preview = document.getElementById(previewId);
      preview.src = e.target.result;
      preview.style.display = "block";
    }
    reader.readAsDataURL(file);
  }
}

// Sửa slider
function openEditSlider(btn, index) {
  let row = btn.closest("tr");
  let id = row.getAttribute("data-id");
  
  document.getElementById("sliderId").value = id;
  document.getElementById("sliderThuTu").value = index + 1;
  document.getElementById("sliderTieuDe").value = row.cells[1].innerText;
  document.getElementById("sliderMoTa").value = row.cells[2].querySelector("textarea").value;
  
  // Hiển thị ảnh hiện tại
  let img = row.cells[3].querySelector("img");
  if (img) {
    document.getElementById("sliderPreview").src = img.src;
    document.getElementById("sliderPreview").style.display = "block";
    document.getElementById("sliderCurrentImage").innerText = "Ảnh hiện tại: " + img.src;
  } else {
    document.getElementById("sliderPreview").style.display = "none";
    document.getElementById("sliderCurrentImage").innerText = "Chưa có ảnh";
  }
  
  document.getElementById("modalSlider").style.display = "flex";
}

function closeSliderModal() {
  document.getElementById("modalSlider").style.display = "none";
}

function previewSliderImage(input) {
  const file = input.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById("sliderPreview").src = e.target.result;
      document.getElementById("sliderPreview").style.display = "block";
      document.getElementById("sliderCurrentImage").innerText = "Ảnh mới đã chọn: " + file.name;
    }
    reader.readAsDataURL(file);
  }
}

// Đóng modal khi click bên ngoài
window.onclick = function(event) {
  let modalSua = document.getElementById("modalSua");
  let modalSlider = document.getElementById("modalSlider");
  
  if (event.target == modalSua) {
    closeEditForm();
  }
  if (event.target == modalSlider) {
    closeSliderModal();
  }
}
</script>
</body>
</html>