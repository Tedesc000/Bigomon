<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Battle Tower</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
    <link rel="stylesheet" href="assets/css/frontJogo.css">
</head>

<body id="corpoMapa9">
    <main id="paginaMapa9">
        <a href="?modulo=mapa&acao=mapa8" id="botaoSetaAnterior" class="setaVoltar" aria-label="Voltar para Sootopolis">
            <img
                id="setaAnterior"
                src="../public/assets/img/icons/seta.png"
                alt="Mapa anterior"
            >
        </a>
        <div id="mapaContainer">
            <img
                id="imagemMapa9"
                src="../public/assets/img/mapas/mapa9.png"
                alt="Mapa da Battle Tower"
            >
            <a href="?modulo=pokemon&acao=sus" id="botaoEnfermeira" class="pontoMapa" aria-label="Ir ao Poké Center da Battle Tower">
                <img
                    id="enfermeira"
                    src="../public/assets/img/icons/joy_enfermeira.png"
                    alt="Enfermeira"
                >
            </a>
            <a href="?modulo=loja&acao=verLoja" id="botaoPocao" class="pontoMapa" aria-label="Entrar na loja da Battle Tower">
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

        </div>
        <a href="?modulo=mapa&acao=mapaCampeao" id="botaoSeta" aria-label="Avançar para a Elite dos 4">
            <img
                id="seta"
                src="../public/assets/img/icons/seta.png"
                alt="Avançar para a Elite dos 4"
            >
        </a>

        <!-- FRONT DO JOGO: posicionado fora da imagem do mapa -->
        <?php require_once __DIR__ . '/../../Utils/frontJogo.php'; ?>
    </main>
</body>
</html>
