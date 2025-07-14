let soa = ""
let sob = ""
let dau = ""
let nhapsothunhat = true

document.getElementById('cong').onclick = ()=> chondau("+")
document.getElementById('tru').onclick = ()=> chondau("-")
document.getElementById('nhan').onclick = ()=> chondau("x")
document.getElementById('chia').onclick = ()=> chondau(":")

document.getElementById('bang').onclick =tinh;

document.getElementById('xoa').onclick = ()=> {location.reload()}

function chondau(luachon){
    if (soa==="") return;
    dau = luachon;
    nhapsothunhat=false;
    hienthi();
}

for (let i = 0 ; i<=9; i++){
    document.getElementById('so'+i).onclick = function(){
        if(nhapsothunhat){
            soa+=i;
            hienthi();
        }else{
            sob+=i;
            hienthi();
        }
    }
 
}
function tinh(){
    let A = parseFloat(soa);
    let B = parseFloat(sob);

    if (isNaN(A) || isNaN(B)) {
        document.getElementById('hienketqua').textContent = "Thiếu số!";
        return;
    }

    switch (dau) {
        case "+":
            kq = A + B;
            break;
        case "-":
            kq = A - B;
            break;
        case "x":
            kq = A * B;
            break;
        case ":":
            if (B === 0) {
                document.getElementById('hienketqua').textContent = "Không chia cho 0!";
                return;
            }
            kq = A / B;
            break;
        default:
            document.getElementById('hienketqua').textContent = "Chưa chọn phép tính!";
            return;
    }

    hienthidapan();
}

    function hienthi(){
        document.getElementById('hienpheptinh').textContent=
        soa+" "+dau+" "+sob
    }
    function hienthidapan(){
    document.getElementById('hienketqua').textContent = "= " + kq;
}


    
