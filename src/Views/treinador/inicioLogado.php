<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Parabéns, você conseguiu logar!</h1>
    <?php
        echo '<img src="assets/img/fotoPerfil/' . $_SESSION['usuario_logado']['foto'] . '" alt="Foto Perfil" id="foto">';
        echo $_SESSION['usuario_logado']['nome'];
        echo '<br>';
        echo $_SESSION['usuario_logado']['email'];
        echo '<br>';
        echo $_SESSION['usuario_logado']['sexo'];
        echo '<br>';
        echo $_SESSION['usuario_logado']['foto'];
        echo '<br>';
        echo $_SESSION['usuario_logado']['dinheiro'];
        echo '<br>';
        echo $_SESSION['usuario_logado']['slot1'];
        echo $_SESSION['usuario_logado']['slot2'];
        echo $_SESSION['usuario_logado']['slot3'];
        echo $_SESSION['usuario_logado']['slot4'];
        echo $_SESSION['usuario_logado']['slot5'];
        echo $_SESSION['usuario_logado']['slot6'];
    ?>
</body>
</html>