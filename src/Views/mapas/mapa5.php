<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petalburg</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
</head>

<body id="corpoMapa5">
    <main id="paginaMapa5">
        <div id="mapaContainer">
            <img
                id="imagemMapa5"
                src="../public/assets/img/mapas/mapa5.png"
                alt="Mapa 5"
            >
        </div>
        <a href="?modulo=mapa&acao=ginasio5" id="botaoGinasio" aria-label="Ginásio">
            <img
                id="ginasio"
                src="../public/assets/img/icons/luta.png"
                alt="Entrar no ginásio de batalha."
            >
        </a>
        <?php if (!empty($_SESSION['ginasio5_visitado'])): ?>
            <a href="?modulo=mapa&acao=mapa6" id="botaoSeta" aria-label="Ir para a próxima cidade">
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
