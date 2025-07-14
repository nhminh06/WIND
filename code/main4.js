const box = document.getElementById('thung')

let hanhdong = false;
let start = 0;
let move = 0;

box.addEventListener('mousedown',(e)=>{
    hanhdong= true;
    start = e.pageX - box.offsetLeft;
    move = box.scrollLeft;

})

box.addEventListener('mouseup',()=>{
    hanhdong = false;
})
box.addEventListener('mousemove',(e)=>{
    if(!hanhdong) return
    const a = e.pageX - box.offsetLeft;
    const dichuyen = (a - start)* 1.5;
    box.scrollLeft = move - dichuyen;
})