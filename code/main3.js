function xacnhan(){
    const ten = document.getElementById('nhapten');
    const layten = ten.value.trim();
    const sdt = document.getElementById('nhapsdt')
    const laysdt = sdt.value.trim();
    const email = document.getElementById('nhapgmail');
    const layemail = email.value.trim();
    const pw = document.getElementById('nhappw');
    const laypw = pw.value.trim();
    const pw2 = document.getElementById('nhappw2');
    const laypw2 = pw2.value.trim();

    const tbten = document.getElementById('ten');
    const tbsdt = document.getElementById('sdt');
    const tbemail = document.getElementById('gmail');
    const tbpw = document.getElementById('pw');
    const tbpw2 = document.getElementById('pw2');

    dk = true;


    if (layten==="") {
        tbten.textContent = "Tên không được để trống"
        tbten.classList.add("loi")  
        dk = false;   
    } else if (layten.length<8) {
        tbten.textContent = "Số kí tự của tên phải lớn hơn 8"
        tbten.classList.add("loi") 
         dk = false;   
        
    }else{
       tbten.textContent = "Tên:"
        tbten.classList.remove("loi")
    }


     if (laysdt==="") {
        tbsdt.textContent = "Số điện thoại không được để trống"
        tbsdt.classList.add("loi")
         dk = false; 
        
    }else if (laysdt.length<10) {
     tbsdt.textContent = "Số điện thoại không hợp lệ"
        tbsdt.classList.add("loi") 
         dk = false;     
    }else{
          tbsdt.textContent = "Số điện thoại:"
        tbsdt.classList.remove("loi")
    }

     if (laysdt==="") {
        tbemail.textContent = "Email không được để trống"
        tbemail.classList.add("loi")
         dk = false; 
     }else{
        tbemail.textContent = "Nhập vào email:"
        tbemail.classList.remove("loi")
     }

      if (laypw==="") {
        tbpw.textContent = "Mật khẩu không được để trống"
        tbpw.classList.add("loi")  
         dk = false;    
    } else if (laypw.length<8) {
        tbpw.textContent = "Số kí tự của Mật khẩu phải lớn hơn 8"
        tbpw.classList.add("loi")
         dk = false;    
        
    }else{
        tbpw.textContent = "Mật khẩu:"
        tbpw.classList.remove("loi")  
    }



    if(laypw2===""){
        tbpw2.textContent = "Nhập lại mật khẩu không được để trống"
        tbpw2.classList.add("loi");
         dk = false; 
    }
    else if (laypw != laypw2) {
        tbpw2.textContent = "Mật khẩu nhập lại không khớp"
        tbpw2.classList.add("loi")   
         dk = false;   
    }else{
        tbpw2.textContent = "Xác nhận mật khẩu:"
        tbpw2.classList.remove("loi");
    }


    if(dk){
        alert("Đăng kí thàng công")
    }
   


    

}