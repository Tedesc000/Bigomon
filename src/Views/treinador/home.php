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
            <img src="assets/img/inicioLogin/groundon.webp" alt="groundon" id="groundonInicio">
            <span id="tituloInicio">Bigomon</span>
            <img src="assets/img/inicioLogin/kyogre.webp" alt="kyogre" id="kyogreInicio">
        </div>
        <div id="botoes">
            <a href="?modulo=treinador&acao=roleta" class="botaoInicio">Iniciar</a>
            <button class="botaoInicio" onclick="pop(
                true,
                'SOBRE',
                'Bem-vindo ao mundo de Bigomon!',
                'Aqui você irá encontrar criaturas modificadas e enfrentar desafios em batalhas. Prepare-se para sua aventura!',
                '/inicioLogin/groundon.webp',
                'Voltar ao início'
                )">
                Sobre
            </button>
        </div>
    </div>
</body>
</html>