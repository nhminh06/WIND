const khungslide = document.querySelector('.inner');
let item = 800;
let vitri = 0;
let soluong = document.querySelectorAll('img').length;
const nuttrai = document.querySelector('.nuttrai');
const nutphai = document.querySelector('.nutphai');

function chuyenSlide(){
    khungslide.style.transform = `translateX(-${item * vitri}px)`;
}

nutphai.addEventListener(`click`, function(){
    if (vitri === soluong-1) {
        vitri = -1;

    }
    vitri++;
    chuyenSlide();
}
)
nuttrai.addEventListener(`click`, function(){
    vitri--;
   

    chuyenSlide();
}
 
)

