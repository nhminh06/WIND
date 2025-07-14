const trai = document.querySelector('.trai');
const phai = document.querySelector('.phai');
const len = document.querySelector('.len');
const xuong = document.querySelector('.xuong');
const ban = document.querySelector('.ban');
const box = document.getElementById('container')
const Main = document.querySelector('.Main');
const them = document.getElementById('them');

let x = 0;
let y = 0;
them.addEventListener('click', () => {
    const newDiv = document.createElement('div');
    box.appendChild(newDiv);
    newDiv.classList.add('Main');
    newDiv.style.color = 'pink';
    newDiv.style.backgroundColor = 'black';
    newDiv.style.borderRadius = '50%';
    newDiv.style.top = '50%'
    newDiv.style.right = '50%';
    newDiv.style.marginRight = '50px';
    newDiv.style.position = 'absolute';
    
})

 function updetemove(){
    Main.style.transform = `translate(${x}px, ${y}px)`;
 }


trai.addEventListener('click', () => {
    x -= 100;
    updetemove();
})

phai.addEventListener('click', () => {
    x += 100;
    updetemove();

})

len.addEventListener('click', () => {
    y -= 100;
    updetemove();
   
})

xuong.addEventListener('click', () => {
    y += 100;
    updetemove();
  
})
ban.addEventListener(`click`, () => {
    x=0;
    y=0;
    updetemove();
})

