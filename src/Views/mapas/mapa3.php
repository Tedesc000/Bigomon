<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mauville</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
    <link rel="stylesheet" href="assets/css/frontJogo.css">
</head>

<body id="corpoMapa3">
    <main id="paginaMapa3">
        <a href="?modulo=mapa&acao=mapa2" id="botaoSetaAnterior" class="setaVoltar" aria-label="Voltar para Dewford">
            <img
                id="setaAnterior"
                src="../public/assets/img/icons/seta.png"
                alt="Mapa anterior"
            >
        </a>
        <div id="mapaContainer">
            <img
                id="imagemMapa3"
                src="../public/assets/img/mapas/mapa3.png"
                alt="Mapa 3"
            >
            <a href="?modulo=mapa&acao=ginasio3" id="botaoGinasio" class="pontoMapa" aria-label="Entrar no ginásio de Mauville">
                <img
                    id="ginasio"
                    src="../public/assets/img/icons/insignia_eletrico.png"
                    alt="Insígnia elétrica"
                >
            </a>
            <a href="?modulo=pokemon&acao=sus" id="botaoEnfermeira" class="pontoMapa" aria-label="Ir ao Poké Center de Mauville">
                <img
                    id="enfermeira"
                    src="../public/assets/img/icons/joy_enfermeira.png"
                    alt="Enfermeira"
                >
            </a>
            <a href="?modulo=loja&acao=verLoja" id="botaoPocao" class="pontoMapa" aria-label="Entrar na loja de Mauville">
                <img
                    id="pocao"
                    src="../public/assets/img/icons/pocao.jpg"
                    alt="Poção"
                >
            </a>
            <a
                href="?modulo=batalha&acao=batalhaMatinho"
                id="botaoBatalha"
                aria-label="Iniciar batalha contra um matinho"
            >
                <img
                    id="batalhaMatinho"
                    src="../public/assets/img/icons/matinho.gif"
                    alt="Matinho"
                >
            </a>

            <a
                href="?modulo=batalha&acao=batalhaTreinador&ginasio=3&treinador=4"
                id="botaoTreinadorExterno"
                class="pontoTreinadorPadrao"
                aria-label="Iniciar batalha contra o treinador da cidade"
            >
                <img
                    src="../public/assets/img/treinadores/trainer_default.png"
                    alt="Treinador da cidade"
                >
            </a>
        </div>
        <?php if ($ginasioConcluido): ?>
            <a href="?modulo=mapa&acao=mapa4" id="botaoSeta" aria-label="Ir para a próxima cidade">
                <img
                    id="seta"
                    src="../public/assets/img/icons/seta.png"
                    alt="Próxima cidade"
                >
            </a>
        <?php endif; ?>

        <!-- FRONT DO JOGO: posicionado fora da imagem do mapa -->
        <?php require_once __DIR__ . '/../../Utils/frontJogo.php'; ?>
    </main>
</body>
</html>
