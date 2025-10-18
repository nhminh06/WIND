
function loadpage(file , el){
    document.querySelectorAll('.menu li').forEach(item => {
        item.classList.remove('active');
    });
    el.classList.add('active');

    const content = document.getElementById('content');
    content.innerHTML = '';

    fetch(file)
        .then(response => response.text())
        .then(data => {
            content.innerHTML = data;
            loadSettingFeatures();
        })
        .catch(error => {
            console.error('Error loading page:', error);
        });
}


function loadSettingFeatures() {
  const tabs = document.querySelectorAll('.tabs11 span');
  const infoSections = document.querySelectorAll('.form-section, .email-section, .phone-section');
  const securitySection = document.querySelector('.security-section');

  if (tabs.length > 0) {
    tabs.forEach((tab, index) => {
      tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active-tab'));
        tab.classList.add('active-tab');

        if (index === 0) {
          // Hiện thông tin tài khoản
          infoSections.forEach(sec => sec.style.display = "block");
          if (securitySection) securitySection.style.display = "none";
        } else {
          // Hiện mật khẩu & bảo mật
          infoSections.forEach(sec => sec.style.display = "none");
          if (securitySection) securitySection.style.display = "block";
        }
      });
    });
  }
}


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
   return dk;
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
    return lg;


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


const elements1 = document.querySelectorAll('.ex_card');

const observer1 = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
      observer1.unobserve(entry.target); // 👈 Ngừng quan sát để hiệu ứng chỉ chạy 1 lần
    }
  });
}, {
  threshold: 0.10
});

elements1.forEach(el => observer1.observe(el));


elements.forEach(el => observer.observe(el));








const menuTT = document.getElementById('menuToggle');
const menu = document.querySelector('.rbc_menu');

menuTT.addEventListener('click', () => {
    if (menu.style.display === 'flex') {
        menu.style.display = 'none';
    } else {
        menu.style.display = 'flex';
    }
});
// const teamitem = document.querySelectorAll('.team_item')
// const che = document.querySelector('.che')
// const nameitem = document.querySelectorAll('.name')
// const thongtin = document.querySelectorAll('.thongtin_tv')


// teamitem.forEach((iteamrieng,anten)=>{
//     let c = true;
//   cherieng.addEventListener(`click`,()=>{
//       if(c){
        
//         iteamrieng.che.style.opacity = '0';
//         nameitem[anten].style.opacity = '0';
//         thongtin[anten].style.opacity = '1';

        
//     }else{
//        cherieng.style.transform = 'translateX(0)'; 
//         nameitem[anten].style.opacity = '1';
//          thongtin[anten].style.opacity = '0';
//     }
//     c= !c;
//   })
    
// })
document.querySelectorAll('.team_item').forEach((teamitem,an)=>{
    const che = teamitem.querySelector('.che')
    const avata = teamitem.querySelector('.avata')
    const nameitem = teamitem.querySelector('.name')
    const thongtin = teamitem.querySelector('.thongtin_tv')
     let c =true;
    che.addEventListener(`click`,()=>{
       
     if (c) {
           che.style.opacity = '0';
        nameitem.style.opacity ='0'
         thongtin.style.opacity= '1'
        avata.style.transform = 'translate(-50%,-175px)'
     }else{
           che.style.opacity = '1';
        nameitem.style.opacity ='1'
        avata.style.transform = 'translate(-50%,-50%)'
        thongtin.style.opacity= '0'
        thongtin.style.transition= 'all 0.5s ease';
     }
     c = !c
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


document.querySelectorAll('.team_slide').forEach((slideWrapper1) => {
    const inner = slideWrapper1.querySelector('.inner2');
    const prevBtn = slideWrapper1.parentElement.querySelector('.bttr');
    const nextBtn = slideWrapper1.parentElement.querySelector('.btp');

    const tourItems = inner.querySelectorAll('.team_item').length;

    const toursPerSlide = 1;
    const tourItemWidth = 354;   
    const spaceBetween = 20*2;  
    const slideWidth = tourItemWidth * toursPerSlide + spaceBetween;

    const maxIndex = (tourItems/2);
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








document.querySelectorAll('.review_slide').forEach((slideWrapper2) => {
    const inner = slideWrapper2.querySelector('.inner3');
    const prevBtn = slideWrapper2.parentElement.querySelector('.bttr');
    const nextBtn = slideWrapper2.parentElement.querySelector('.btp');

    const tourItems = inner.querySelectorAll('.review-box').length;

    const toursPerSlide = 1;
    const tourItemWidth = 354;   
    const spaceBetween = 20*2;  
    const slideWidth = tourItemWidth * toursPerSlide + spaceBetween;

    const maxIndex = (tourItems/2);
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

document.querySelectorAll('.review_detailed_img').forEach((detailed_img)=>{
    const khung = detailed_img.querySelector('.inner4');
    const trai = khung.parentElement.querySelector('.bttr');
    const phai = khung.parentElement.querySelector('.btp');

    const soimg = khung.querySelectorAll('.inner4 > img').length;

    const detailed_w = 800;
    const soluongluoc = 1;
    const dichuyen = detailed_w*soluongluoc;

    let vt =0;

    function move(){
        khung.style.transform = `translateX(-${vt*dichuyen}px`
    }

    const khung1 = document.querySelector('.img_bd')
    const bd_khoangcanh = 150 + 12; 
    const khung2  = document.querySelector('.slide_detailed')
    const imgcon = khung2.querySelectorAll('img');

        imgcon.forEach((bam,vtanh)=>{
            bam.addEventListener(`click`,()=>{
                vt = vtanh;
                move();
                bd_move();

            })
        })

    function bd_move(){
        if (vt>=0) {
            let bd_dichuyen = bd_khoangcanh*vt;
            khung1.style.transform = `translate(${bd_dichuyen}px)`
            
        }
    }
    trai.addEventListener(`click`,()=>{
        if (vt>0) {
            vt--;
            move();
            bd_move();
        }
    })
    phai.addEventListener(`click`,()=>{
        if (vt<soimg-1) {
            vt++;
            move();
            bd_move();
        }
    })

})



// const bd_img = document.querySelectorAll('.slide_detailed > img')

// bd_img.forEach(imgs=>{
//     imgs.addEventListener(`click`,()=>{
//        bd_img.forEach(i=>i.classList.remove('highlighted'));
//          imgs.classList.add('highlighted')
//     })
// })\\



let tong =0;
document.querySelectorAll('.quantity-row').forEach((them)=>{
    const cong = them.querySelector('.cong')
    const tru = them.querySelector('.tru')
    const hiensoluong = them.querySelector('.counter > span')
    const hiengia = them.querySelector('.price')
    let soluong = 0;
    let gia1 = parseInt(them.dataset.gia);
    
    cong.addEventListener(`click`,()=>{
        soluong++;
       tong = soluong*gia1;
        hiensoluong.textContent = soluong;
        hiengia.textContent = tong+" VND";
        tonggia();
    })
    tru.addEventListener(`click`,()=>{
        if (soluong>0) {
            soluong--;
            tong = gia1*soluong
            hiensoluong.textContent=  soluong;
            hiengia.textContent = tong+" VND";
            if (soluong=="0") {
                hiengia.textContent = ""
            }
            tonggia();
        }
    })

})
function tonggia(){
    let tongso = 0;
    document.querySelectorAll('.price').forEach((i)=>{
      const giatext = i.textContent.replace(/[^\d]/g, '')
      if (giatext) {
        tongso+=parseInt(giatext)
      }
      const hiengia = document.querySelector('.highlight')
      const giagoc = document.querySelector('.strike')
        if (hiengia || giagoc) {
        hiengia.textContent = tongso.toLocaleString() + " VND";
        giagoc.textContent = tongso.toLocaleString() + " VND";
    }
    })
}






