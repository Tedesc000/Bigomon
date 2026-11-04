<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poke Rolation</title>

    <!-- importa o arquivo CSS da página -->
    <link rel="stylesheet" href="../public/assets/css/roleta.css">
    <link rel="stylesheet" href="../public/assets/css/inicioLogin.css">

    <!-- biblioteca da roleta -->
    <script src="https://cdn.jsdelivr.net/npm/spin-wheel@5.0.2/dist/spin-wheel-iife.js"></script>
    <script src="../public/assets/js/pop.js" defer></script>
    <script src="../public/assets/js/roleta.js" defer></script>
</head>
<body id="corpoRoleta">
    <main class="pagina-roleta">
        <h1>Poke Rolation</h1>
        <p>Gire a roleta e descubra seu Bigomon!</p>

        <div id="roleta"></div>

        <button type="button" id="girar" class="botaoInicio" aria-label="Girar a roleta">
            <span class="hoverText">▸</span>
            <span class="defaultText">GIRAR</span>
        </button>

        <p id="resultado">Qual será seu Bigomon?</p>
    </main>
</body>
</html>