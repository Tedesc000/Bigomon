function pop(x=true,
            title="TITULO",
            textTop="TEXTO",
            img="/fotoPerfil/fotoPerfilDefault.webp",
            textBottom="TEXTO",
            btnText="CONTINUAR",
            btnHref="?modulo=treinador&acao=home")
{
    if(!document.getElementById("popBack")){
        document.body.innerHTML +=
        `<div id="popBack">
            <div id="pop">
                <button id="popX" onclick="fecharPop()">X</button>
                <header id="popHeader">
                    <h1 id="popTitulo"></h1>
                </header>
                
                <span id="popTextTop" class="popText"></span>

                <div id="popImgDiv">
                    <img src="" alt="Foto" id="popImg">
                </div>
                
                <span id="popTextBottom" class="popText"></span>
                
                <a href="" id="popButton"></a>
            </div>
        </div>`;
    }
    if(x==false){
        document.getElementById("popButton").style.display = "none";
    }
    document.getElementById("popBack").style.display = "flex";
    document.getElementById("popTitulo").textContent = title;
    document.getElementById("popTextTop").textContent = textTop;
    document.getElementById("popTextBottom").innerHTML = textBottom;
    document.getElementById("popImg").src = "assets/img" + img;
    document.getElementById("popButton").textContent = btnText;
    document.getElementById("popButton").href = btnHref;
}

function fecharPop(){
    document.getElementById("popBack").style.display = "none";
}