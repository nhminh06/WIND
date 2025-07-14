const nut = document.getElementById("nut");
const text = document.getElementById("text");
let x = 0;
let y = 0;

function dichuyen(){
    nut.style.transform= `translate(${x}px, ${y}px)`;
}

const caunoi = [
    `Đừng bấm nữa`,
    `Bấm nữa đi`,
    `Bấm đi`,
    `Bấm đi mà`,
    `Bấm đi, đừng ngại`,
    `Bấm đi, đừng sợ`,
    `Bấm đi, đừng lo`,
    `Bấm đi, đừng nghĩ`,
    `Bấm đi, đừng suy nghĩ`,
    `Bấm đi, đừng do dự`
]




nut.addEventListener(`mouseover`,()=>{
 x = Math.floor(Math.random() * 600) * (Math.random() < 0.5 ? -1 : 1); 
    y = Math.floor(Math.random() * 600) * (Math.random() < 0.5 ? -1 : 1);
    dichuyen();
    text.textContent = caunoi[Math.floor(Math.random() * caunoi.length)];
})
nut.addEventListener(`click`,()=>{
    text.textContent = "Thế méo nào còn bấm được"
})