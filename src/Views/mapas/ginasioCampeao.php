<?php
/** @var bool $ginasioConcluido */
/** @var array{interno1: string, interno2: string, interno3: string, interno4: string, lider: string, externo: string} $statusAdversarios */
?><!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ginásio do Campeão</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
    <link rel="stylesheet" href="assets/css/frontJogo.css">
</head>

<body id="corpoGinasioCampeao">
    <main id="paginaGinasioCampeao">
        <div id="ginasioContainer">
            <img
                id="imagemGinasioCampeao"
                src="../public/assets/img/mapas/campeaoGinasio.png"
                alt="Ginásio do campeão da Liga Pokémon"
            >
            <a
                <?php if ($statusAdversarios['lider'] === 'disponivel'): ?>href="?modulo=batalha&acao=batalhaTreinador&ginasio=9&treinador=3"<?php else: ?>aria-disabled="true"<?php endif; ?>
                id="botaoLiderGinasio"
                class="pontoLiderGinasio <?= htmlspecialchars($statusAdversarios['lider'], ENT_QUOTES, 'UTF-8') ?>"
                aria-label="<?= $ginasioConcluido ? 'Campeão Wallace derrotado' : 'Desafiar Wallace, campeão da Liga Pokémon' ?>"
            >
                <img
                    id="wallace"
                    src="../public/assets/img/treinadores/wallace.png"
                    alt="Wallace, campeão da Liga Pokémon"
                >
            </a>
            <?php if ($ginasioConcluido): ?>
                <p id="mensagemCampeao">Você conquistou a Liga Pokémon!</p>
            <?php endif; ?>
        </div>
        <a href="?modulo=mapa&acao=mapaCampeao" id="botaoSeta" class="setaVoltar" aria-label="Voltar para a entrada da Liga">
            <img
                id="seta"
                src="../public/assets/img/icons/seta.png"
                alt="Voltar para a entrada da Liga"
            >
        </a>

        <?php require_once __DIR__ . '/../../Utils/frontJogo.php'; ?>
    </main>
</body>
</html>
