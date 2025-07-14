function xacnhan(){
    let username = document.getElementById("username").value;
    let password = document.getElementById("password").value;
    let email = document.getElementById("email").value;
    let confirmPassword = document.getElementById("confirmPassword").value;
    let textemail = document.getElementById("textemail")
    let textpw2 = document.getElementById("textpw2")
    let textten = document.getElementById("textten")
    let textpw = document.getElementById("textpw")

    dk = true

    if (username ==="" ) {  
        textten.textContent = "Tên đăng nhập không được để trống";
        textten.classList.add("error");
        
        dk = false;
    }
    else if (username.length < 6 ) {
        textten.textContent = "Tên đăng nhập phải có ít nhất 6 ký tự";
        textten.classList.add("error");
        
        dk = false;
        
    }else {
        textten.textContent = "Tên đăng nhập:";
        textten.classList.remove("error");
    }
    if (email ==="" ) {  
        textemail.textContent = "Email đăng nhập không được để trống";
        textemail.classList.add("error");
        
        dk = false;
    }
    else if (email.length < 8 ) {
        textemail.textContent = "Email đăng nhập phải có ít nhất 8 ký tự";
        textemail.classList.add("error");
        
        dk = false;
        
    }else {
        textemail.textContent = "Nhập email:";
        textemail.classList.remove("error");
    }
    
    if (password === "") {
         textpw.textContent = "Mật khẩu không được để trống";
        textpw.classList.add("error");
        
    dk = false;}
        
    else if (password.length < 6) {
     textpw.textContent = "Mật khẩu phải có ít nhất 6 ký tự";
        textpw.classList.add("error");
        
        dk = false;}
    else {
        textpw.textContent = "Mật khẩu";
        textpw.classList.remove("error");

        
    }
    if (password === "") {
         textpw2.textContent = "Nhập lại mật khẩu không được để trống";
        textpw2.classList.add("error");
        
    dk = false;}
    else if (password.length < 6) {
     textpw2.textContent = "Mật khẩu phải có ít nhất 6 ký tự";
        textpw2.classList.add("error");
        
        dk = false;}
        else if (confirmPassword !== password) {
        textpw2.textContent = "Mật khẩu không khớp";
        textpw2.classList.add("error");
        
        dk = false;
    }
    else {
        textpw2.textContent = "Xác nhận mật khẩu:";
        textpw2.classList.remove("error");

        
    }
    if (dk) {
        alert("Đăng ký thành công");
        window.location.href = "http://127.0.0.1:5500/WebIndex.html"
}
}

function dangnhap(){
      let username = document.getElementById("username").value;
    let password = document.getElementById("password").value; 
        let textten = document.getElementById("textten")
    let textpw = document.getElementById("textpw") 
    lg = true
      if (username ==="" ) {  
        textten.textContent = "Tên đăng nhập không được để trống";
        textten.classList.add("error");
        
        lg = false;
    }
    else if (username.length < 6 ) {
        textten.textContent = "Tên đăng nhập phải có ít nhất 6 ký tự";
        textten.classList.add("error");
        
        lg = false;
        
    }else {
        textten.textContent = "Tên đăng nhập:";
        textten.classList.remove("error");
    }

      if (password === "") {
         textpw.textContent = "Mật khẩu không được để trống";
        textpw.classList.add("error");
        
    lg = false;}
        
    else if (password.length < 6) {
     textpw.textContent = "Mật khẩu phải có ít nhất 6 ký tự";
        textpw.classList.add("error");
        
        lg = false;}
    else {
        textpw.textContent = "Mật khẩu";
        textpw.classList.remove("error");

        
    }
    if (lg) {
        alert("Đăng nhập thành công");
        window.location.href = "http://127.0.0.1:5500/WebIndex.html"
    }


}
window.addEventListener('scroll', function () {
    const menusearch = document.querySelector('.menusearch');
    const header = document.querySelector('header');

    const headerBottom = header.offsetTop + header.offsetHeight;
    const scrollY = window.scrollY;

    if (scrollY >= headerBottom) {
        menusearch.classList.add('scrolled');
    } else {
        menusearch.classList.remove('scrolled');
    }
});
document.querySelectorAll('.tour_slide').forEach((slideWrapper) => {
    const inner = slideWrapper.querySelector('.inner');
    const prevBtn = slideWrapper.parentElement.querySelector('.bttr');
    const nextBtn = slideWrapper.parentElement.querySelector('.btp');

    const tourItems = inner.querySelectorAll('.tour_item').length;

    const toursPerSlide = 1;
    const tourItemWidth = 510;   
    const spaceBetween = 15*2;  
    const slideWidth = tourItemWidth * toursPerSlide + spaceBetween;

    const maxIndex = (tourItems/2)+1;
    let currentIndex = 0;

    function updateSlider() {
        inner.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
    }

    nextBtn.addEventListener('click', () => {
        if (currentIndex < maxIndex) {
            currentIndex++;
            
            updateSlider();
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateSlider();
        }
    });
});
const elements = document.querySelectorAll('.box');

const observer = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
    } else {
      entry.target.classList.remove('visible'); 
    }
  });
}, {
  threshold: 0.15
});


elements.forEach(el => observer.observe(el));

const che = document.querySelectorAll('.che')
const nameitem = document.querySelectorAll('.name')
const thongtin = document.querySelectorAll('.thongtin_tv')

che.forEach((cherieng,anten)=>{
    let c = true;
  cherieng.addEventListener(`click`,()=>{
      if(c){
        cherieng.style.transform = 'translateX(-200px)';
        nameitem[anten].style.opacity = '0';
        thongtin[anten].style.opacity = '1';

        
    }else{
       cherieng.style.transform = 'translateX(0)'; 
        nameitem[anten].style.opacity = '1';
         thongtin[anten].style.opacity = '0';
    }
    c= !c;
  })
    
})


const dlbt = document.querySelector('.sangtoi')
const sunbt = document.querySelector('.sun')
const moon = document.querySelector('.moon')
let dl = true;
dlbt.addEventListener(`click`,()=>{
    document.body.classList.toggle('dark_mode')
    if (dl) {
        
        sunbt.style.opacity = '0';
        moon.style.opacity = '1';
    }else{
      
         sunbt.style.opacity = '1';
        moon.style.opacity = '0';
    }
    dl = !dl;
})





