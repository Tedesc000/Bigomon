<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fortree - Ginásio</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
    <link rel="stylesheet" href="assets/css/frontJogo.css">
</head>

<body id="corpoGinasio6">
    <main id="paginaGinasio6">
        <div id="ginasioContainer">
            <img
                id="imagemGinasio6"
                src="../public/assets/img/mapas/ginasio6.png"
                alt="Ginásio 6"
            >
        </div>
        <a href="?modulo=mapa&acao=mapa6" id="botaoSeta" class="setaVoltar" aria-label="Voltar para Fortree">
            <img
                id="seta"
                src="../public/assets/img/icons/seta.png"
                alt="Voltar"
            >
        </a>

        <!-- FRONT DO JOGO: posicionado fora da imagem do ginasio -->
        <?php require_once __DIR__ . '/../../Utils/frontJogo.php'; ?>
    </main>
</body>
</html>
