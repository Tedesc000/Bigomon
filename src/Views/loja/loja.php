<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rustboro - Loja</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
    <link rel="stylesheet" href="assets/css/loja.css">
</head>

<body id="corpoLoja">
    <main id="paginaLoja">
        <div id="lojaContainer">
            <img
                id="imagemLoja"
                src="../public/assets/img/mapas/loja.png"
                alt="Loja"
            >
            <p id="tituloLoja">Bem-vindo à <?= htmlspecialchars($nomeLoja ?? 'Loja') ?>!</p>
        </div>

        <div id="painelItensLoja">
            <h2>Itens à Venda</h2>
            <ul id="listaItensLoja">
            <?php if (!empty($itensLoja)): ?>
                <?php foreach ($itensLoja as $item): ?>
                    <li class="cardItemLoja">
                        <span class="nomeItem"><?= htmlspecialchars($item['nome'] ?? 'Item') ?></span>
                        <span class="precoItem">R$ <?= number_format($item['preco'] ?? 0, 2, ',', '.') ?></span>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="cardItemLoja">Nenhum item disponível nesta loja.</li>
            <?php endif; ?>
            </ul>
        </div>

        <a href="?modulo=mapa&acao=<?= htmlspecialchars($_SESSION['mapa'] ?? 'mapaInicio') ?>" id="botaoSeta" class="setaVoltar" aria-label="Voltar para o mapa">
            <img
                id="seta"
                src="../public/assets/img/icons/seta.png"
                alt="Voltar"
            >
        </a>
    </main>
</body>
</html>
