<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sootopolis - Ginásio</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
    <link rel="stylesheet" href="assets/css/frontJogo.css">
</head>

<body id="corpoGinasio8">
    <main id="paginaGinasio8">
        <div id="ginasioContainer">
            <img
                id="imagemGinasio8"
                src="../public/assets/img/mapas/ginasio8.png"
                alt="Ginásio 8"
            >
            <a
                <?php if ($statusAdversarios['interno1'] === 'disponivel'): ?>href="?modulo=batalha&acao=batalhaTreinador&ginasio=8&treinador=1"<?php else: ?>aria-disabled="true"<?php endif; ?>
                id="botaoTreinadorPadrao1"
                class="pontoTreinadorPadrao <?= $statusAdversarios['interno1'] ?>"
                aria-label="Iniciar batalha contra o treinador padrão 1"
            >
                <img
                    id="treinadorPadrao1"
                    src="../public/assets/img/treinadores/trainer_default.png"
                    alt="Treinador padrão 1"
                >
            </a>
            <a
                <?php if ($statusAdversarios['interno2'] === 'disponivel'): ?>href="?modulo=batalha&acao=batalhaTreinador&ginasio=8&treinador=2"<?php else: ?>aria-disabled="true"<?php endif; ?>
                id="botaoTreinadorPadrao2"
                class="pontoTreinadorPadrao <?= $statusAdversarios['interno2'] ?>"
                aria-label="Iniciar batalha contra o treinador padrão 2"
            >
                <img
                    id="treinadorPadrao2"
                    src="../public/assets/img/treinadores/trainer_default.png"
                    alt="Treinador padrão 2"
                >
            </a>
            <a
                <?php if ($statusAdversarios['lider'] === 'disponivel'): ?>href="?modulo=batalha&acao=batalhaTreinador&ginasio=8&treinador=3"<?php else: ?>aria-disabled="true"<?php endif; ?>
                id="botaoLiderGinasio"
                class="pontoLiderGinasio <?= $statusAdversarios['lider'] ?>"
                aria-label="Iniciar batalha contra Juan"
            >
                <img
                    src="../public/assets/img/treinadores/juan.png"
                    alt="Juan, líder do ginásio"
                >
            </a>
        </div>
        <a href="?modulo=mapa&acao=mapa8" id="botaoSeta" class="setaVoltar" aria-label="Voltar para Sootopolis">
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
