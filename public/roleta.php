
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poke Rolation</title>

    <!-- importa o arquivo CSS da página -->
    <link rel="stylesheet" href="assets/css/roleta.css">

    <!-- biblioteca da roleta -->
    <script src="https://cdn.jsdelivr.net/npm/spin-wheel@5.0.2/dist/spin-wheel-iife.js"></script>

    <!-- carrega o JavaScript após a leitura do HTML -->
    <script src="assets/js/roleta.js" defer></script>
</head>
<body>
    <main class="pagina-roleta">
        <h1>Poke Rolation</h1>
        <p>Gire a roleta e descubra seu Bigomon!</p>

        <!-- área onde a roleta será desenhada pelo JavaScript -->
        <div id="roleta"></div>

        <!-- botão que inicia o sorteio -->
        <button id="girar" type="button">GIRAR</button>

        <!-- área que apresenta o resultado do sorteio -->
        <p id="resultado">Qual será seu Bigomon?</p>
    </main>
</body>
</html>
