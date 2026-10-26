<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/inicioLogin.css">
    <script src="../public/assets/js/pop.js"></script>
    <title>Bigomon - Início</title>
</head>
<body id="inicio">
    <div id="container">
        <div id="tituloContainer">
            <!--<img src="assets/img/inicioLogin/groundon.webp" alt="groundon" id="groundonInicio"> -->
            <div id="titulos">
                <span id="tituloInicio">BIGOMON</span> <br>
                <img src="assets/img/inicioLogin/emeraldVersion.png" alt="Emerald Version" id="subtituloInicio">
            </div>
            <!-- <img src="assets/img/inicioLogin/kyogre.webp" alt="kyogre" id="kyogreInicio"> -->
        </div>
        <div id="botoes">
            <a href="?modulo=treinador&acao=login" class="botaoInicio">
                <span class="hoverText">▸</span>
                <span class="defaultText">LOGIN</span>
            </a>
            <a href="?modulo=treinador&acao=cadastrar" class="botaoInicio">
                <span class="hoverText">▸</span>
                <span class="defaultText">CADASTRAR</span>
            </a>
            <button type="button" onclick="pop(true, 'POKEROLATION', 'PARABÉNS! SEU POKEMON ESCOLHIDO FOI:', '/inicioLogin/groudon.webp', 'GROUDON')" class="botaoInicio">
                <span class="hoverText">▸</span>
                <span class="defaultText">POP-UP EXEMPLO</span>
            </button>
        </div>
    </div>
</body>
</html>