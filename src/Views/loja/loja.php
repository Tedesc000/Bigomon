<?php
// Versão dos assets pela data de modificação: força o navegador a baixar de novo quando o arquivo muda
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
    <title><?= htmlspecialchars($nomeLoja ?? 'Loja') ?></title>
    <link rel="stylesheet" href="assets/css/mapas.css?v=<?= $versaoAsset('assets/css/mapas.css') ?>">
    <link rel="stylesheet" href="assets/css/loja.css?v=<?= $versaoAsset('assets/css/loja.css') ?>">
    <link rel="stylesheet" href="assets/css/inicioLogin.css?v=<?= $versaoAsset('assets/css/inicioLogin.css') ?>">
    <script src="../public/assets/js/pop.js?v=<?= $versaoAsset('assets/js/pop.js') ?>"></script>
    <script defer src="assets/js/loja.js?v=<?= $versaoAsset('assets/js/loja.js') ?>"></script>
</head>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Utils/tmImagens.php';

$idsPossuidos = $idsPossuidos ?? [];
?>

<body id="corpoLoja">
    <main id="paginaLoja">
        <div id="lojaContainer">
            <img id="imagemLoja" src="../public/assets/img/mapas/loja.png" alt="Loja">

            <!-- Título no topo da imagem da loja -->
            <div id="cabecalhoLoja">
                <p id="tituloLoja">Bem-vindo à <?= htmlspecialchars($nomeLoja ?? 'Loja') ?>!</p>
                <?php if (isset($_SESSION['usuario_logado']['dinheiro'])): ?>
                    <span id="saldoLoja">BIGOREAIS: R$
                        <?= number_format($_SESSION['usuario_logado']['dinheiro'], 2, ',', '.') ?>
                        <img id="dinheiro" src="../public/assets/img/icons/pokedollar.webp" alt="bigoreais">
                    </span>
                <?php endif; ?>
            </div>

            <!-- Painel de itens em lista dentro da imagem-->
            <div id="painelItensLoja">
                <h2 id="subtituloItens">ITENS À VENDA</h2>
                <ul id="listaItensLoja">
                    <?php
                    $saldoTreinador = $_SESSION['usuario_logado']['dinheiro'] ?? 0;
                    if (!empty($itensLoja)):
                        ?>
                        <?php foreach ($itensLoja as $item):
                            $preco = $item['preco'] ?? 0;
                            $semSaldo = $preco > $saldoTreinador;
                            $idItem = $item['id_item'] ?? 0;
                            $urlImagem = urlImagemItem((int) $idItem, (string) ($item['nome'] ?? ''));
                            // TM é reutilizável: quem já tem não compra de novo
                            $tmAdquirido = ItemEfeito::ehTM((int) $idItem) && in_array((int) $idItem, $idsPossuidos, true);
                            ?>
                            <li class="cardItemLoja" data-preco="<?= (float) $preco ?>">
                                <div class="infoItemLoja">
                                    <div class="detalhesItemLoja">
                                        <?php if (!empty($urlImagem)): ?>
                                            <img src="<?= htmlspecialchars($urlImagem) ?>"
                                                alt="<?= htmlspecialchars($item['nome'] ?? '') ?>" class="iconeItemLoja"
                                                onerror="this.style.display='none'">
                                        <?php endif; ?>
                                        <span class="nomeItem"><?= htmlspecialchars($item['nome'] ?? 'Item') ?></span>
                                    </div>
                                    <span class="precoItem">R$ <?= number_format($preco, 2, ',', '.') ?></span>

                                    <button class="botaoInicio info" onclick="pop(
                                    false,
                                    '<?= htmlspecialchars($item['nome'] ?? 'Item') ?>',
                                    '',
                                    '<?= htmlspecialchars($urlImagem) ?>',
                                    '<?= htmlspecialchars($item['descricao'] ?? 'Item') ?>',
                                    'Voltar',
                                    'javascript:fecharPop()'
                                    )">
                                    <span class="defaultText info">?</span>
                                </button>
                            </div>
                            <form action="?modulo=loja&acao=comprar" method="POST" class="formComprarItem">
                                <input type="hidden" name="id_item" value="<?= htmlspecialchars($item['id_item'] ?? '') ?>">
                                <?php if ($tmAdquirido): ?>
                                    <button type="button" class="botaoComprarItem" disabled title="Você já tem esse TM">
                                        <span class="defaultText">ADQUIRIDO</span>
                                    </button>
                                <?php else: ?>
                                    <button type="submit" class="botaoComprarItem" <?= $semSaldo ? 'disabled' : '' ?> title="<?= $semSaldo ? 'Saldo insuficiente' : 'Comprar item' ?>">
                                        <span class="hoverText">▸</span>
                                        <span class="defaultText">COMPRAR</span>
                                    </button>
                                <?php endif; ?>
                            </form>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="cardItemVazio">Nenhum item disponível nesta loja.</li>
                <?php endif; ?>
                </ul>
            </div>
        </div>

        <a href="?modulo=mapa&acao=<?= htmlspecialchars($_SESSION['mapa'] ?? 'mapaInicio') ?>" id="botaoSeta"
            class="setaVoltar" aria-label="Voltar para o mapa">
            <img id="seta" src="../public/assets/img/icons/seta.png" alt="Voltar">
        </a>
    </main>
</body>

</html>