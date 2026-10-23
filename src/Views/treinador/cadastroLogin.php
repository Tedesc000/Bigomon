<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/inicioLogin.css">
    <title>Bigomon - Início</title>
</head>
<body id="inicio">
    <div id="popBack">
        <div id="pop">
            <button id="popX" onclick="fecharPop()">X</button>
            <header id="popHeader">
                <h1 id="popTitulo">Bigomon</h1>
            </header>
            
            <span class="popText">Lorem Ipsum Dolor</span>

            <div id="popImgDiv">
                <img src="assets/img/fotoPerfil/fotoPerfilDefault.webp" alt="Foto do Bigomon" id="popImg">
            </div>
            
            <span class="popText">Lorem Ipsum Dolor</span>
            
            <a href="inicio.php" id="popButton">Continuar</a>
        </div>
        
    </div>
    <div id="container">
        <div id="tituloContainer">
            <img src="assets/img/inicioLogin/groundon.webp" alt="groundon" id="groundonInicio">
            <span id="tituloInicio">Bigomon</span>
            <img src="assets/img/inicioLogin/kyogre.webp" alt="kyogre" id="kyogreInicio">
        </div>
        <div id="botoes">
            <a href="?modulo=treinador&acao=login" class="botaoInicio">Login</a>
            <a href="?modulo=treinador&acao=cadastrar" class="botaoInicio">Cadastrar</a>
        </div>
    </div>
</body>
</html>