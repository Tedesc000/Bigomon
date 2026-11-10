<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lavaridge</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
</head>

<body id="corpoMapa4">
    <main id="paginaMapa4">
        <div id="mapaContainer">
            <img
                id="imagemMapa4"
                src="../public/assets/img/mapas/mapa4.png"
                alt="Mapa 4"
            >
            <a href="?modulo=mapa&acao=ginasio4" id="botaoGinasio" class="pontoMapa" aria-label="Entrar no ginásio de Lavaridge">
                <img
                    id="ginasio"
                    src="../public/assets/img/icons/insignia_fogo.png"
                    alt="Insígnia de fogo"
                >
            </a>
            <a href="sus" id="botaoEnfermeira" class="pontoMapa" aria-label="Ir ao Poké Center de Lavaridge">
                <img
                    id="enfermeira"
                    src="../public/assets/img/icons/joy_enfermeira.png"
                    alt="Enfermeira"
                >
            </a>
            <a href="loja" id="botaoPocao" class="pontoMapa" aria-label="Entrar na loja de Lavaridge">
                <img
                    id="pocao"
                    src="../public/assets/img/icons/pocao.jpg"
                    alt="Poção"
                >
            </a>
        </div>
        <?php if (!empty($_SESSION['ginasio4_visitado'])): ?>
            <a href="?modulo=mapa&acao=mapa5" id="botaoSeta" aria-label="Ir para a próxima cidade">
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
