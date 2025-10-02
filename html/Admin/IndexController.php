<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
  <?php session_start(); ?>
      <aside class="sidebar">
    <h2 class="logo">WIND Admin</h2>
   <?php include '../../includes/Adminnav.php';?>
  </aside>

  <!-- Main -->
  <div class="main">
    <!-- Header -->
    <header class="header">
      <h1>Bảng điều khiển</h1>
      <div class="admin-info">
       <?php 
       echo "<p>Xin chào  " . $_SESSION['username'] . "</p>";
       ?>
        <button onclick="window.location.href='../views/users.php'" class="logout">Đăng xuất</button>
      </div>
    </header>

    <!-- Content -->
   <section class="content">
  <h2>Địa điểm nổi bật</h2>
  <button class="add" onclick="hienFormthem(this)">+ Thêm địa điểm</button>
  <table>
    <thead>
      <tr>
        <th>Tên địa điểm</th>
        <th>Mô tả ngắn</th>
        <th>Hình ảnh</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody id="tourTable">
      <tr>
        <td>Vịnh Hạ Long</td>
        <td><textarea id="mota" rows="5" required>Kỳ quan thiên nhiên thế giới</textarea></td>
        <td style="width: 300px;"><div class="img2ad">
          <img src="../../img/vinhhalong1.png" width="100">
          <img src="../../img/vinhhalong2.png" width="100">
        </div></td>
        <td>
          <button class="edit" onclick="hienFormSua(this)">Sửa</button>
          <button class="delete" onclick="deleteTour(this)">Xóa</button>
        </td>
      </tr>
      <tr>
        <td>Nha Trang</td>
        <td><textarea name="mota" id="mota" rows="5" required>Viên ngọc biển Đông Việt Nam</textarea></td>
        <td><div class="img2ad"></div>
          <img src="../../img/nhatrang1.png" width="100">
          <img src="../../img/nhatrang2.png" width="100">
        <td>
          <button class="edit" onclick="hienFormSua(this)">Sửa</button>
          <button class="delete" onclick="deleteTour(this)">Xóa</button>
        </td>
      </tr>
    </tbody>
  </table>
</section>
<form action="../../php/IndexBannerCTL/UDBanner.php" method="POST" enctype="multipart/form-data">
  <div id="modalSua" class="modal-sua">
  <div class="form-sua">
    <h2>Sửa địa điểm</h2>

    <label for="ten">Tên địa điểm</label>
    <input name="ten" type="text" id="ten">

    <label for="mota">Mô tả</label>
    <textarea name="mota" id="mota"></textarea>

    <label for="anh1">Ảnh 1 (URL)</label>
    <input type="text" name="anh1" id="anh1">

    <label for="anh2">Ảnh 2 (URL)</label>
    <input type="text" name="anh2" id="anh2">

    <!-- Preview ảnh -->
    <div class="preview-images">
      <img id="preview1" src="../../img/nhatrang1.png">
      <img id="preview2" src="../../img/nhatrang2.png">
    </div>

    <div class="actions">
      <button type="submit" class="luu">Lưu</button>
      <button class="huy" onclick="anFormSua()">Hủy</button>
    </div>
  </div>
</div>
</form>
<form action="../../php/IndexBannerCTL/IDaddBanner.php" method="POST" enctype="multipart/form-data">
  <div id="modalthem" class="modal-them">
  <div class="form-sua">
    <h2>Thêm địa điểm</h2>

    <label for="ten">Tên địa điểm</label>
    <input type="text" id="ten" name="ten" required>

    <label for="mota">Mô tả</label>
    <textarea id="mota" name="mota" required></textarea>

    <label for="anh1">Ảnh 1 (URL)</label>
    <input type="text" id="anh1" name="anh1" required>

    <label for="anh2">Ảnh 2 (URL)</label>
    <input type="text" id="anh2" name="anh2" required>

    <!-- Preview ảnh -->
    <div class="preview-images">
      <img id="preview1" src="../../img/vinhhalong1.png">
      <img id="preview2" src="../../img/vinhhalong2.png">
    </div>

    <div class="actions">
      <button type="submit" class="luu">Lưu</button>
      <button class="huy" onclick="anFormthem()">Hủy</button>
    </div>
  </div>
</div>
</form>



  </div>
<script>
let editRow = null;

function hienFormSua(row) {
  document.getElementById("modalSua").style.display = "flex";
}

function anFormSua() {
  document.getElementById("modalSua").style.display = "none";
}

// Bấm ra ngoài form cũng tắt
window.onclick = function(e) {
  const modal = document.getElementById("modalSua");
  if (e.target === modal) {
    modal.style.display = "none";
  }
}
function hienFormthem(row) {
  document.getElementById("modalthem").style.display = "flex";
}

function anFormthem() {
  document.getElementById("modalthem").style.display = "none";
}

// Bấm ra ngoài form cũng tắt
window.onclick = function(e) {
  const modal = document.getElementById("modalthem");
  if (e.target === modal) {
    modal.style.display = "none";
  }
}

document.getElementById("tourForm").onsubmit = function (e) {
  e.preventDefault();
  const ten = document.getElementById("ten").value;
  const mota = document.getElementById("mota").value;
  const anh1 = document.getElementById("anh1").value;
  const anh2 = document.getElementById("anh2").value;

  const tbody = document.getElementById("tourTable");

  if (editRow) {
    editRow.cells[0].innerText = ten;
    editRow.cells[1].innerText = mota;
    editRow.cells[2].innerHTML = `
      <img src="${anh1}" width="100">
      <img src="${anh2}" width="100">
    `;
  } else {
    const row = tbody.insertRow();
    row.insertCell(0).innerText = ten;
    row.insertCell(1).innerText = mota;
    row.insertCell(2).innerHTML = `
      <img src="${anh1}" width="100">
      <img src="${anh2}" width="100">
    `;
    row.insertCell(3).innerHTML = `
      <button onclick="editTour(this)">Sửa</button>
      <button onclick="deleteTour(this)">Xóa</button>
    `;
  }
  closeModal();
};

function editTour(btn) {
  const row = btn.closest("tr");
  openModal(true, row);
}

function deleteTour(btn) {
  if (confirm("Bạn có chắc muốn xóa địa điểm này?")) {
    btn.closest("tr").remove();
  }
}
</script>

</body>
</html>