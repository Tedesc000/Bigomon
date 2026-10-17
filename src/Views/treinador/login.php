<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/inicioLogin.css">
    <title>Login</title>
</head>
<body id="login">
    <div id="tituloContainer">
        <span id="tituloLogin">Bigomon</span>
    </div>
    <form action="?modulo=treinador&acao=logar" method="POST" id="formLogin">
        <div id="camposLogin">
            <input type="email" name="email" id="email" placeholder="Email">
            <input type="password" name="senha" id="senha" placeholder="Senha">
        </div>
        <button type="submit" class="botaoLogin">Login</button>
    </form>
</body>
</html>