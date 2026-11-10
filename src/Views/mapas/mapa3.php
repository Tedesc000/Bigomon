<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mauville</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
</head>

<body id="corpoMapa3">
    <main id="paginaMapa3">
        <div id="mapaContainer">
            <img
                id="imagemMapa3"
                src="../public/assets/img/mapas/mapa3.png"
                alt="Mapa 3"
            >
        </div>
        <a href="?modulo=mapa&acao=ginasio3" id="botaoGinasio" aria-label="Ginásio">
            <img
                id="ginasio"
                src="../public/assets/img/icons/luta.png"
                alt="Entrar no ginásio de batalha."
            >
        </a>
        <?php if (!empty($_SESSION['ginasio3_visitado'])): ?>
            <a href="?modulo=mapa&acao=mapa4" id="botaoSeta" aria-label="Ir para a próxima cidade">
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
