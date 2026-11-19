<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dewford</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
</head>

<body id="corpoMapa2">
    <main id="paginaMapa2">
        <div id="mapaContainer">
            <img
                id="imagemMapa2"
                src="../public/assets/img/mapas/mapa2.png"
                alt="Mapa 2"
            >
            <a href="?modulo=mapa&acao=ginasio2" id="botaoGinasio" class="pontoMapa" aria-label="Entrar no ginásio de Dewford">
                <img
                    id="ginasio"
                    src="../public/assets/img/icons/insignia_lutador.png"
                    alt="Insígnia de lutador"
                >
            </a>
            <a href="?modulo=pokemon&acao=sus" id="botaoEnfermeira" class="pontoMapa" aria-label="Ir ao Poké Center de Dewford">
                <img
                    id="enfermeira"
                    src="../public/assets/img/icons/joy_enfermeira.png"
                    alt="Enfermeira"
                >
            </a>
            <a href="?modulo=loja&acao=verLoja" id="botaoPocao" class="pontoMapa" aria-label="Entrar na loja de Dewford">
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
        <?php if (!empty($_SESSION['ginasio2_visitado'])): ?>
            <a href="?modulo=mapa&acao=mapa3" id="botaoSeta" aria-label="Ir para a próxima cidade">
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
