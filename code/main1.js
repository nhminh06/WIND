 function nhapvao(){
    const input = document.getElementById('nhap')
    const ghichu = input.value.trim();

    if (ghichu==="") return;

    const li = document.createElement('li')
    li.textContent= ghichu;

    li.addEventListener(`click`,()=>{
        li.classList.toggle("completed")
    })
    const deletebt = document.createElement('div')
    deletebt.textContent = "X"
    deletebt.classList.add("delete");
    deletebt.addEventListener(`click`, ()=>{

        li.remove();
    })
    li.appendChild(deletebt);
    document.getElementById("hiendanhsach").appendChild(li);
     

    input.value=""
 }