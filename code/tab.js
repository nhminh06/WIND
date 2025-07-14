const bt = document.getElementById('tab');
const up = document.getElementById('upicon');
const dow = document.getElementById('dow');
const trai = document.getElementById('trai');
const phai = document.getElementById('phai');
const tt = document.getElementById('tt');
const td = document.getElementById('td');
const tp = document.getElementById('tp');
const dp = document.getElementById('dp');
let kh = true;

bt.addEventListener('click', () => {
  if (kh) {
    bt.style.width = '600px';
    bt.style.height = '600px';
    bt.style.borderRadius = '50%';
    up.style.opacity = '1'; 
    dow.style.opacity = '1';
    trai.style.opacity = '1';
    phai.style.opacity = '1';
    tt.style.opacity = '1';
    td.style.opacity = '1';
    tp.style.opacity = '1';
    dp.style.opacity = '1';
  } else {
    bt.style.width = '40px';
    bt.style.height = '40px';
    bt.style.borderRadius = '12px';
    bt.style.border = '1px solid rgb(64, 151, 201)';
    up.style.opacity = '0';
     dow.style.opacity = '0';
    trai.style.opacity = '0';
    phai.style.opacity = '0';
    tt.style.opacity = '0';
    td.style.opacity = '0';
    tp.style.opacity = '0';
    dp.style.opacity = '0';
  }
  kh = !kh;
});
