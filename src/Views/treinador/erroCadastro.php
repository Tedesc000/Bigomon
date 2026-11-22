<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/inicioLogin.css">
    <title>Erro no Cadastro - Bigomon</title>
</head>
<body id="home">
    <div id="tituloContainer">
        <div id="titulos">
            <img src="assets/img/inicioLogin/LogoBigomon.png" alt="Bigomon" id="titulo" class="logoBigomon">
            <img src="assets/img/inicioLogin/emeraldVersion.png" alt="Emerald Version" id="subtituloInicio">
        </div>
    </div>
    <div class="containerErro">
        <span class="textoErro"><?= htmlspecialchars($tituloErro ?? 'NÃO FOI POSSÍVEL REALIZAR O CADASTRO!') ?></span>
        <span class="descricaoErro"><?= htmlspecialchars($mensagemErro ?? 'VERIFIQUE SE OS DADOS ESTÃO CORRETOS OU SE O E-MAIL JÁ ESTÁ EM USO.') ?></span>
    </div>
    <div id="botoes">
        <a href="?modulo=treinador&acao=cadastrar" class="botaoInicio">
            <span class="hoverText">▸</span>
            <span class="defaultText">TENTAR NOVAMENTE</span>
        </a>
        <a href="?modulo=treinador&acao=login" class="botaoInicio">
            <span class="hoverText">▸</span>
            <span class="defaultText">FAZER LOGIN</span>
        </a>
    </div>
</body>
</html>
