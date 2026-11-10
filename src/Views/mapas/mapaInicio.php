<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oldale Town</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
</head>

<body id="corpoMapaInicio">
    <main id="paginaMapaInicio">
        <div id="mapaContainer">
            <img
                id="imagemMapaInicio"
                src="../public/assets/img/mapas/mapaInicio.png"
                alt="Mapa inicial"
            >
            <a href="loja" id="botaoPocao" class="pontoMapa" aria-label="Entrar na loja">
                <img
                    id="pocao"
                    src="../public/assets/img/icons/pocao.jpg"
                    alt="Loja"
                >
            </a>
            <a href="sus" id="botaoEnfermeira" class="pontoMapa" aria-label="Entrar no hospital">
                <img
                    id="enfermeira"
                    src="../public/assets/img/icons/joy_enfermeira.png"
                    alt="Ir para o hospital"
                >
            </a>
        </div>
        <a href="?modulo=mapa&acao=mapa1" id="botaoSeta" aria-label="Avançar pelo mapa">
            <img
                id="seta"
                src="../public/assets/img/icons/seta.png"
                alt="Avançar"
            >
        </a>
    </main>
</body>
</html>
