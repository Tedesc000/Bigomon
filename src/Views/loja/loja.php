<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($nomeLoja ?? 'Loja') ?></title>
    <link rel="stylesheet" href="assets/css/mapas.css">
    <link rel="stylesheet" href="assets/css/loja.css">
    <link rel="stylesheet" href="assets/css/inicioLogin.css">
    <script src="../public/assets/js/pop.js"></script>
</head>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Utils/tmImagens.php';

// Mapa de itens pelo ID oficial da PokeAPI (ex: 814 = Key Stone / Pedra de Megaevolução)
$mapaPorId = [
    814 => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/key-stone.png'
];

// Carrega e salva as URLs oficiais da PokeAPI na sessão sem travar a página
if (!empty($itensLoja)) {
    foreach ($itensLoja as $item) {
        $idItem = (int) ($item['id_item'] ?? 0);
        if (!$idItem) {
            continue;
        }

        // Para o ID 814 (Pedra de Megaevolução), define o sprite oficial
        if (isset($mapaPorId[$idItem])) {
            $urlItem = $mapaPorId[$idItem];
            $_SESSION[$idItem]['urlItem'] = $urlItem;
            $_SESSION['itens'][$idItem]['urlItem'] = $urlItem;
            continue;
        }

        // TMs: sprite da PokeAPI pelo tipo do ataque (tm-fire.png, tm-water.png...)
        if ($urlTM = urlImagemTM($idItem)) {
            $_SESSION[$idItem]['urlItem'] = $urlTM;
            $_SESSION['itens'][$idItem]['urlItem'] = $urlTM;
            continue;
        }

        // Se já está na sessão, não processa novamente
        if (!empty($_SESSION[$idItem]['urlItem'])) {
            continue;
        }

        $nomeItem = strtolower(trim($item['nome'] ?? ''));
        // Gera a URL oficial direta da CDN de sprites da PokeAPI
        $slug = $nomeItem;
        $slug = str_replace(['é', 'è', 'ê'], 'e', $slug);
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
        $slug = trim($slug, '-');
        $urlItem = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/{$slug}.png";

        // Salva na sessão conforme solicitado
        $_SESSION[$idItem]['urlItem'] = $urlItem;
        $_SESSION['itens'][$idItem]['urlItem'] = $urlItem;
    }
}
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
                            $urlImagem = $_SESSION[$idItem]['urlItem'] ?? ($_SESSION['itens'][$idItem]['urlItem'] ?? '');
                            ?>
                            <li class="cardItemLoja">
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
                                <button type="submit" class="botaoComprarItem" <?= $semSaldo ? 'disabled' : '' ?> title="<?= $semSaldo ? 'Saldo insuficiente' : 'Comprar item' ?>">
                                    <span class="hoverText">▸</span>
                                    <span class="defaultText">COMPRAR</span>
                                </button>
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