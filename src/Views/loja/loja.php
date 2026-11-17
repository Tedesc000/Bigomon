<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($nomeLoja ?? 'Loja') ?></title>
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
            
            <!-- Título no topo da imagem da loja -->
            <div id="cabecalhoLoja">
                <p id="tituloLoja">Bem-vindo à <?= htmlspecialchars($nomeLoja ?? 'Loja') ?>!</p>
                <?php if (isset($_SESSION['usuario_logado']['dinheiro'])): ?>
                <span id="saldoLoja">BIGOREAIS: R$ <?= number_format($_SESSION['usuario_logado']['dinheiro'], 2, ',', '.') ?>
                <img id="dinheiro" src="../public/assets/img/icons/pokedollar.webp" alt="bigoreais" >    
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
                    ?>
                        <li class="cardItemLoja">
                            <div class="infoItemLoja">
                                <span class="nomeItem"><?= htmlspecialchars($item['nome'] ?? 'Item') ?></span>
                                <span class="precoItem">R$ <?= number_format($preco, 2, ',', '.') ?></span>
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
