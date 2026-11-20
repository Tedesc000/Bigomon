<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dewford - Ginásio</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
    <link rel="stylesheet" href="assets/css/frontJogo.css">
</head>

<body id="corpoGinasio2">
    <main id="paginaGinasio2">
        <div id="ginasioContainer">
            <img
                id="imagemGinasio2"
                src="../public/assets/img/mapas/ginasio2.png"
                alt="Ginásio 2"
            >
        </div>
        <a href="?modulo=mapa&acao=mapa2" id="botaoSeta" class="setaVoltar" aria-label="Voltar para Dewford">
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
