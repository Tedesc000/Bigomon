<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/inicioLogin.css">
    <title>Login</title>
</head>
<body id="home">
    <div id="tituloContainer">
        <div id="titulos">
            <span id="titulo">BIGOMON</span>
            <img src="assets/img/inicioLogin/emeraldVersion.png" alt="Emerald Version" id="subtituloInicio">  
        </div>
          
    </div>
    <form action="?modulo=treinador&acao=logar" method="POST" id="formLogin">
        <div id="camposLogin">
            <input type="email" name="email" id="email" placeholder="EMAIL">
            <input type="password" name="senha" id="senha" placeholder="SENHA">
        </div>
        <button type="submit" class="botaoInicio">
            <span class="hoverText">▸</span>
            <span class="defaultText">LOGIN</span>
        </button>
        <a href="?modulo=treinador&acao=cadastroLogin" class="botaoInicio">
            <span class="hoverText">▸</span>
            <span class="defaultText">VOLTAR</span>
        </a>
    </form>
</body>
</html>