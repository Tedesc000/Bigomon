<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petalburg</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
</head>

<body id="corpoMapa5">
    <main id="paginaMapa5">
        <div id="mapaContainer">
            <img
                id="imagemMapa5"
                src="../public/assets/img/mapas/mapa5.png"
                alt="Mapa 5"
            >
            <a href="?modulo=mapa&acao=ginasio5" id="botaoGinasio" class="pontoMapa" aria-label="Entrar no ginásio de Petalburg">
                <img
                    id="ginasio"
                    src="../public/assets/img/icons/insignia_normal.png"
                    alt="Insígnia normal"
                >
            </a>
            <a href="sus" id="botaoEnfermeira" class="pontoMapa" aria-label="Ir ao Poké Center de Petalburg">
                <img
                    id="enfermeira"
                    src="../public/assets/img/icons/joy_enfermeira.png"
                    alt="Enfermeira"
                >
            </a>
            <a href="loja" id="botaoPocao" class="pontoMapa" aria-label="Entrar na loja de Petalburg">
                <img
                    id="pocao"
                    src="../public/assets/img/icons/pocao.jpg"
                    alt="Poção"
                >
            </a>
        </div>
        <?php if (!empty($_SESSION['ginasio5_visitado'])): ?>
            <a href="?modulo=mapa&acao=mapa6" id="botaoSeta" aria-label="Ir para a próxima cidade">
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
