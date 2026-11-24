<?php
$versaoAsset = static function (string $caminho): int {
    $arquivo = __DIR__ . '/../../../public/' . $caminho;
    return is_file($arquivo) ? filemtime($arquivo) : 0;
};
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livro de Insígnias</title>
    <link rel="stylesheet" href="assets/css/mapas.css?v=<?= $versaoAsset('assets/css/mapas.css') ?>">
    <link rel="stylesheet" href="assets/css/inicioLogin.css?v=<?= $versaoAsset('assets/css/inicioLogin.css') ?>">
    <link rel="stylesheet" href="assets/css/insignias.css?v=<?= $versaoAsset('assets/css/insignias.css') ?>">
</head>
<body id="corpoLoja">
    <main id="paginaLoja">
        <section class="livroInsignias" aria-labelledby="tituloInsignias">
            <h1 id="tituloInsignias">LIVRO DE INSÍGNIAS</h1>
            <ul class="listaInsignias">
                <?php foreach ($insignias as $insignia): ?>
                    <li class="cartaoInsignia<?= $insignia['conquistada'] ? ' conquistada' : ' pendente' ?>">
                        <img
                            src="../public/assets/img/icons/insignia_<?= htmlspecialchars($insignia['icone'], ENT_QUOTES, 'UTF-8') ?>.png"
                            alt=""
                            class="imagemInsignia"
                        >
                        <span class="nomeInsignia">Insígnia <?= htmlspecialchars($insignia['nome'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="cidadeInsignia"><?= htmlspecialchars($insignia['cidade'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="estadoInsignia"><?= $insignia['conquistada'] ? 'Conquistada' : 'Não conquistada' ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <a href="<?= htmlspecialchars($linkVoltar, ENT_QUOTES, 'UTF-8') ?>" id="botaoSeta" class="setaVoltar" aria-label="Voltar ao jogo">
            <img id="seta" src="../public/assets/img/icons/seta.png" alt="Voltar">
        </a>
    </main>
</body>
</html>
