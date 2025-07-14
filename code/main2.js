let soThuNhat = "";
let soThuHai = "";
let phepTinh = "";
let dangNhapSoThuNhat = true;

// Gắn sự kiện cho các số
for (let i = 0; i <= 9; i++) {
  document.getElementById("so" + i).onclick = function () {
    if (dangNhapSoThuNhat) {
      soThuNhat += i;
      capNhatHienThi();
    } else {
      soThuHai += i;
      capNhatHienThi();
    }
  };
}

// Gắn sự kiện cho các phép toán
document.getElementById("cong").onclick = () => chonPhepTinh("+");
document.getElementById("tru").onclick = () => chonPhepTinh("-");
document.getElementById("nhan").onclick = () => chonPhepTinh("*");
document.getElementById("chia").onclick = () => chonPhepTinh("/");

document.getElementById('xoa').onclick = () => {location.reload()}

// Gắn sự kiện cho nút "="
document.getElementById("bang").onclick = tinhToan;

function chonPhepTinh(toanTu) {
  if (soThuNhat === "") return;
  phepTinh = toanTu;
  dangNhapSoThuNhat = false;
  capNhatHienThi();
}

function tinhToan() {
  let ketQua = 0;

  const a = parseFloat(soThuNhat);
  const b = parseFloat(soThuHai);

  if (phepTinh === "+") ketQua = a + b;
  else if (phepTinh === "-") ketQua = a - b;
  else if (phepTinh === "*") ketQua = a * b;
  else if (phepTinh === "/") {
    if (b === 0) {
      document.getElementById("hienketqua").textContent = "Lỗi chia 0";
      return;
    }
    ketQua = a / b;
  }

  document.getElementById("hienketqua").textContent = "= " + ketQua;
}

function capNhatHienThi() {
  document.getElementById("hienpheptinh").textContent =
    soThuNhat + " " + phepTinh + " " + soThuHai;
}
