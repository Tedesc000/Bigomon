<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mossdeep</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
</head>

<body id="corpoMapa7">
    <main id="paginaMapa7">
        <div id="mapaContainer">
            <img
                id="imagemMapa7"
                src="../public/assets/img/mapas/mapa7.png"
                alt="Mapa 7"
            >
        </div>
        <a href="?modulo=mapa&acao=ginasio7" id="botaoGinasio" aria-label="Ginásio">
            <img
                id="ginasio"
                src="../public/assets/img/icons/luta.png"
                alt="Entrar no ginásio de batalha."
            >
        </a>
        <?php if (!empty($_SESSION['ginasio7_visitado'])): ?>
            <a href="?modulo=mapa&acao=mapa8" id="botaoSeta" aria-label="Ir para a próxima cidade">
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
