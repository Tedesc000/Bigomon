<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lavaridge</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
</head>

<body id="corpoMapa4">
    <main id="paginaMapa4">
        <div id="mapaContainer">
            <img
                id="imagemMapa4"
                src="../public/assets/img/mapas/mapa4.png"
                alt="Mapa 4"
            >
        </div>
        <a href="?modulo=mapa&acao=ginasio4" id="botaoGinasio" aria-label="Ginásio">
            <img
                id="ginasio"
                src="../public/assets/img/icons/luta.png"
                alt="Entrar no ginásio de batalha."
            >
        </a>
        <?php if (!empty($_SESSION['ginasio4_visitado'])): ?>
            <a href="?modulo=mapa&acao=mapa5" id="botaoSeta" aria-label="Ir para a próxima cidade">
                <img
                    id="seta"
                    src="../public/assets/img/icons/seta.png"
                    alt="Próxima cidade"
                >
            </a>
        <?php endif; ?>
    </main>
</body>
</html>
