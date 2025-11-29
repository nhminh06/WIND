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
    }
    .modal-content {
      background:#15325f;
      padding:20px;
      border-radius:8px;
      width:500px;
      max-height: 90vh;
      overflow-y: auto;
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
    <h1>Bảng điều khiển</h1>
    <div class="admin-info">
      <?php echo "<p>Xin chào " . $_SESSION['username'] . "</p>"; ?>
      <button onclick="window.location.href='../views/index/webindex.php'" class="logout">Trở lại</button>
    </div>
  </header>

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
        <tr data-id="<?= $r['id'] ?>">
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

<!-- Modal sửa -->
<div id="modalSua" class="modal">
  <div class="modal-content">
    <h3>Sửa địa điểm</h3>
    <!-- nhớ thêm enctype -->
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
          <option  value="<?= $bv['khampha_id'] ?>"><?= htmlspecialchars($bv['tieu_de']) ?></option>
        <?php endforeach; ?>
      </select>
      <small style="color: #ccc; font-size: 12px;">Bài viết được chọn sẽ hiển thị khi click vào banner</small>

      <label>Ảnh 1</label>
      <input type="file" name="anh1" id="anh1Sua" accept="image/*" onchange="previewImage(this, 'preview1')">
      <!-- hiển thị ảnh cũ -->
      <img id="preview1" src="" width="100" style="margin-top:5px;display:block">
      <p id="link1" style="font-size:12px;color:#ccc;"></p>

      <label>Ảnh 2</label>
      <input type="file" name="anh2" id="anh2Sua" accept="image/*" onchange="previewImage(this, 'preview2')">
      <!-- hiển thị ảnh cũ -->
      <img id="preview2" src="" width="100" style="margin-top:5px;display:block">
      <p id="link2" style="font-size:12px;color:#ccc;"></p>

      <button class="modal-content-button" type="submit">Lưu</button>
      <button class="modal-content-button" type="button" onclick="closeEditForm()">Hủy</button>
    </form>
  </div>
</div>

<script>
function openEditForm(btn) {
  let row = btn.closest("tr");
  document.getElementById("idSua").value = row.getAttribute("data-id");
  document.getElementById("tenSua").value = row.cells[0].innerText;
  document.getElementById("motaSua").value = row.cells[1].querySelector("textarea").value;

  // Lấy link hiện tại và chọn bài viết tương ứng
  let currentLink = '<?= $r["link"] ?>'; // Cần điều chỉnh để lấy link từ dòng hiện tại
  if (currentLink) {
    // Trích xuất khampha_id từ link (ví dụ: detailed_explore.php?id=123)
    let match = currentLink.match(/id=(\d+)/);
    if (match) {
      document.getElementById("baiVietSua").value = match[1];
    }
  }

  let imgs = row.cells[3].querySelectorAll("img"); // Đã đổi từ cells[2] thành cells[3]

  // Ảnh 1
  document.getElementById("preview1").src = imgs[0] ? imgs[0].src : "";
  document.getElementById("link1").innerText = imgs[0] ? imgs[0].src : "";

  // Ảnh 2
  document.getElementById("preview2").src = imgs[1] ? imgs[1].src : "";
  document.getElementById("link2").innerText = imgs[1] ? imgs[1].src : "";

  document.getElementById("modalSua").style.display = "flex";
}

function confirmXoa(id) {
  if (confirm("⚠️ Bạn có chắc chắn muốn xóa địa điểm này không?")) {
    window.location.href = '../../php/IndexBannerCTL/DLTBanner.php?id=' + id;
  }
}

function closeEditForm() {
  document.getElementById("modalSua").style.display = "none";
}

function previewImage(input, previewId) {
  const file = input.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = e => document.getElementById(previewId).src = e.target.result;
    reader.readAsDataURL(file);
  }
}
</script>
<script src="../../js/Main5.js"></script>
</body>
</html>