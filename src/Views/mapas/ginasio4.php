<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lavaridge - Ginásio</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
    <link rel="stylesheet" href="assets/css/frontJogo.css">
</head>

<body id="corpoGinasio4">
    <main id="paginaGinasio4">
        <div id="ginasioContainer">
            <img
                id="imagemGinasio4"
                src="../public/assets/img/mapas/ginasio4.png"
                alt="Ginásio 4"
            >
            <a
                href="?modulo=batalha&acao=batalhaTreinador&ginasio=4&treinador=1"
                id="botaoTreinadorPadrao1"
                class="pontoTreinadorPadrao"
                aria-label="Iniciar batalha contra o treinador padrão 1"
            >
                <img
                    id="treinadorPadrao1"
                    src="../public/assets/img/treinadores/trainer_default.png"
                    alt="Treinador padrão 1"
                >
            </a>
            <a
                href="?modulo=batalha&acao=batalhaTreinador&ginasio=4&treinador=2"
                id="botaoTreinadorPadrao2"
                class="pontoTreinadorPadrao"
                aria-label="Iniciar batalha contra o treinador padrão 2"
            >
                <img
                    id="treinadorPadrao2"
                    src="../public/assets/img/treinadores/trainer_default.png"
                    alt="Treinador padrão 2"
                >
            </a>
            <a
                href="?modulo=batalha&acao=batalhaTreinador&ginasio=4&treinador=3"
                id="botaoLiderGinasio"
                class="pontoLiderGinasio"
                aria-label="Iniciar batalha contra Flannery"
            >
                <img
                    src="../public/assets/img/treinadores/flannery.png"
                    alt="Flannery, líder do ginásio"
                >
            </a>
        </div>
        <a href="?modulo=mapa&acao=mapa4" id="botaoSeta" class="setaVoltar" aria-label="Voltar para Lavaridge">
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
