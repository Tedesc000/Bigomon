<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/inicioLogin.css">
    <script src="../public/assets/js/pop.js"></script>
    <title>Bigomon - Início</title>
</head>
<body id="home">
    <div id="container">
        <div id="tituloContainer">
            <div id="titulos">
                <span id="titulo">BIGOMON</span>
                <img src="assets/img/inicioLogin/emeraldVersion.png" alt="Emerald Version" id="subtituloInicio">    
            </div>
        </div>
        <div id="botoes">
            <a href="?modulo=treinador&acao=roleta" class="botaoInicio">
                <span class="hoverText">▸</span>
                <span class="defaultText">INICIAR</span>
            </a>
            <button class="botaoInicio" onclick="pop(
                true,
                'SOBRE',
                'Bem-vindo ao Bigomon!',
                '/inicioLogin/foto_sobre.png',
                'Nós somos: Hiago (esquerda), Pedro (meio), Tedesco (direita). <br> Desenvolvemos este projeto como parte do nosso aprendizado em programação web. <br> Esperamos que você aproveite a experiência de jogar Bigomon!',
                'Voltar ao início'
                )">
                <span class="hoverText">▸</span>
                <span class="defaultText">SOBRE</span>
            </button>
        </div>
    </div>
</body>
</html>