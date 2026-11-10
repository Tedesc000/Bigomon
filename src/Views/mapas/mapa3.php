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
            <a href="?modulo=mapa&acao=ginasio3" id="botaoGinasio" class="pontoMapa" aria-label="Entrar no ginásio de Mauville">
                <img
                    id="ginasio"
                    src="../public/assets/img/icons/insignia_eletrico.png"
                    alt="Insígnia elétrica"
                >
            </a>
            <a href="sus" id="botaoEnfermeira" class="pontoMapa" aria-label="Ir ao Poké Center de Mauville">
                <img
                    id="enfermeira"
                    src="../public/assets/img/icons/joy_enfermeira.png"
                    alt="Enfermeira"
                >
            </a>
            <a href="loja" id="botaoPocao" class="pontoMapa" aria-label="Entrar na loja de Mauville">
                <img
                    id="pocao"
                    src="../public/assets/img/icons/pocao.jpg"
                    alt="Poção"
                >
            </a>
        </div>
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
