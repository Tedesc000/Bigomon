<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rustboro</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
</head>

<body id="corpoMapa1">
    <main id="paginaMapa1">
        <div id="mapaContainer">
            <img
                id="imagemMapa1"
                src="../public/assets/img/mapas/mapa1.png"
                alt="Mapa 1"
            >
        </div>
        <a href="?modulo=mapa&acao=ginasio1" id="botaoGinasio" aria-label="Ginásio">
            <img
                id="ginasio"
                src="../public/assets/img/icons/luta.png"
                alt="Entrar no ginásio de batalha."
            >
        </a>
        <?php if (!empty($_SESSION['ginasio1_visitado'])): ?>
            <a href="?modulo=mapa&acao=mapa2" id="botaoSeta" aria-label="Ir para a próxima cidade">
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
