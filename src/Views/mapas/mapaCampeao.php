<?php
/** @var bool $ginasioConcluido */
/** @var array{interno1: string, interno2: string, interno3: string, interno4: string, lider: string, externo: string} $statusAdversarios */
?><!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elite dos 4</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
    <link rel="stylesheet" href="assets/css/frontJogo.css">
</head>

<body id="corpoMapaCampeao">
    <main id="paginaMapaCampeao">
        <a href="?modulo=mapa&acao=mapa8" id="botaoSetaAnterior" class="setaVoltar" aria-label="Voltar para Sootopolis">
            <img
                id="setaAnterior"
                src="../public/assets/img/icons/seta.png"
                alt="Mapa anterior"
            >
        </a>
        <div id="mapaContainer">
            <img
                id="imagemMapaCampeao"
                src="../public/assets/img/mapas/campeaoMapa.png"
                alt="Entrada da Liga Pokémon"
            >
            <a
                <?php if ($statusAdversarios['interno1'] === 'disponivel'): ?>href="?modulo=batalha&acao=batalhaTreinador&ginasio=9&elite=1"<?php else: ?>aria-disabled="true"<?php endif; ?>
                id="botaoEliteSidney"
                class="pontoTreinadorPadrao pontoElite <?= htmlspecialchars($statusAdversarios['interno1'], ENT_QUOTES, 'UTF-8') ?>"
                aria-label="Desafiar Sidney, membro da Elite dos 4"
            >
                <img
                    src="../public/assets/img/treinadores/sidney.png"
                    alt="Sidney"
                >
                <span class="nomeElite">Sidney</span>
            </a>
            <a
                <?php if ($statusAdversarios['interno2'] === 'disponivel'): ?>href="?modulo=batalha&acao=batalhaTreinador&ginasio=9&elite=2"<?php else: ?>aria-disabled="true"<?php endif; ?>
                id="botaoElitePhoebe"
                class="pontoTreinadorPadrao pontoElite <?= htmlspecialchars($statusAdversarios['interno2'], ENT_QUOTES, 'UTF-8') ?>"
                aria-label="Desafiar Phoebe, membro da Elite dos 4"
            >
                <img
                    src="../public/assets/img/treinadores/phoebe.png"
                    alt="Phoebe"
                >
                <span class="nomeElite">Phoebe</span>
            </a>
            <a
                <?php if ($statusAdversarios['interno3'] === 'disponivel'): ?>href="?modulo=batalha&acao=batalhaTreinador&ginasio=9&elite=3"<?php else: ?>aria-disabled="true"<?php endif; ?>
                id="botaoEliteGlacia"
                class="pontoTreinadorPadrao pontoElite <?= htmlspecialchars($statusAdversarios['interno3'], ENT_QUOTES, 'UTF-8') ?>"
                aria-label="Desafiar Glacia, membro da Elite dos 4"
            >
                <img
                    src="../public/assets/img/treinadores/glacia.png"
                    alt="Glacia"
                >
                <span class="nomeElite">Glacia</span>
            </a>
            <a
                <?php if ($statusAdversarios['interno4'] === 'disponivel'): ?>href="?modulo=batalha&acao=batalhaTreinador&ginasio=9&elite=4"<?php else: ?>aria-disabled="true"<?php endif; ?>
                id="botaoEliteDrake"
                class="pontoTreinadorPadrao pontoElite <?= htmlspecialchars($statusAdversarios['interno4'], ENT_QUOTES, 'UTF-8') ?>"
                aria-label="Desafiar Drake, membro da Elite dos 4"
            >
                <img
                    src="../public/assets/img/treinadores/drake.png"
                    alt="Drake"
                >
                <span class="nomeElite">Drake</span>
            </a>
            <h1 id="tituloMapaCampeao">Elite dos 4</h1>
            <?php if ($ginasioConcluido): ?>
                <p id="mensagemCampeao">Você conquistou a Liga Pokémon!</p>
            <?php endif; ?>
        </div>

        <?php if ($statusAdversarios['lider'] !== 'bloqueado'): ?>
            <a href="?modulo=mapa&acao=ginasioCampeao" id="botaoSeta" aria-label="Entrar no ginásio do campeão">
                <img
                    id="seta"
                    src="../public/assets/img/icons/seta.png"
                    alt="Entrar no ginásio do campeão"
                >
            </a>
        <?php endif; ?>

        <?php require_once __DIR__ . '/../../Utils/frontJogo.php'; ?>
    </main>
</body>
</html>
