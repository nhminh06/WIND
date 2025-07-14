const cauhoi = document.getElementById('cauhoi');
const cautraloi = document.getElementById('cautraloi');
const nhaplieu = document.getElementById('nhaplieu');
const xacnhan = document.getElementById('xacnhan');
const next = document.getElementById('tiep');
const diem = document.getElementById('diem');

let tichhan = parseInt(localStorage.getItem("diem" ))||0;



let a = parseInt(Math.random()*100);
let b =  parseInt(Math.random()*100);
let kq = a+b;
cauhoi.textContent = `${a} + ${b}`;
cautraloi.textContent = "";

diem.textContent= "Điểm của bạn : " + tichhan;

xacnhan.addEventListener("click",()=>{
    const solieu = Number(nhaplieu.value);
    if (solieu == kq) {
        cautraloi.textContent = "đáp án : " + kq +" Chính xác"
        tichhan+=5;
        localStorage.setItem("diem",tichhan);
        setTimeout(()=>{
            location.reload();
        },1500);
        
    }else{
        cautraloi.textContent = "đáp án : " + solieu +" Sai" 
        tichhan-=5;
        localStorage.setItem("diem",tichhan);
         setTimeout(()=>{
            location.reload();
        },1500);
    }
}

)
next.addEventListener(`click` , ()=>{
    location.reload();
})
