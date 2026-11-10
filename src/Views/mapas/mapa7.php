<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mossdeep</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
</head>

<body id="corpoMapa7">
    <main id="paginaMapa7">
        <div id="mapaContainer">
            <img
                id="imagemMapa7"
                src="../public/assets/img/mapas/mapa7.png"
                alt="Mapa 7"
            >
            <a href="?modulo=mapa&acao=ginasio7" id="botaoGinasio" class="pontoMapa" aria-label="Entrar no ginásio de Mossdeep">
                <img
                    id="ginasio"
                    src="../public/assets/img/icons/insignia_psiquico.png"
                    alt="Insígnia psíquica"
                >
            </a>
            <a href="sus" id="botaoEnfermeira" class="pontoMapa" aria-label="Ir ao Poké Center de Mossdeep">
                <img
                    id="enfermeira"
                    src="../public/assets/img/icons/joy_enfermeira.png"
                    alt="Enfermeira"
                >
            </a>
            <a href="loja" id="botaoPocao" class="pontoMapa" aria-label="Entrar na loja de Mossdeep">
                <img
                    id="pocao"
                    src="../public/assets/img/icons/pocao.jpg"
                    alt="Poção"
                >
            </a>
        </div>
        <?php if (!empty($_SESSION['ginasio7_visitado'])): ?>
            <a href="?modulo=mapa&acao=mapa8" id="botaoSeta" aria-label="Ir para a próxima cidade">
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
