<?php
session_start();
include '../../db/db.php';

// Lấy dữ liệu từ CSDL
$sql = "SELECT * FROM banner LIMIT 4";
$result = mysqli_query($conn, $sql);

// Tạo mảng 4 phần tử (trống mặc định)
$rows = array_fill(0, 4, ["id"=>"", "tieu_de" => "", "noi_dung" => "", "hinh_anh" => "", "hinh_anh2" => ""]);

// Đổ dữ liệu thật vào mảng
$i = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $rows[$i] = $row;
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
    }
    .modal-content {
      background:#15325f;
      padding:20px;
      border-radius:8px;
      width:400px;
    }
    .modal-content-button {
      margin-top:10px;
      padding:8px 12px;
      border:none;
      border-radius:4px;
      cursor:pointer;
    }
    .modal-content input, .modal-content textarea {
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
      <button onclick="window.location.href='../views/user/users.php'" class="logout">Đăng xuất</button>
    </div>
  </header>

  <section class="content">
    <h2>Địa điểm nổi bật</h2>
    <table>
      <thead>
        <tr>
          <th style="width: 25%;">Tên địa điểm</th>
          <th>Mô tả ngắn</th>
          <th>Hình ảnh</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody id="tourTable">
        <?php foreach ($rows as $r): ?>
        <tr data-id="<?= $r['id'] ?>">
          <td><?= htmlspecialchars($r['tieu_de']) ?></td>
          <td><textarea rows="3" readonly><?= htmlspecialchars($r['noi_dung']) ?></textarea></td>
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

  let imgs = row.cells[2].querySelectorAll("img");

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
</body>
</html>
