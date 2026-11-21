<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bolsa de Itens</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
    <link rel="stylesheet" href="assets/css/loja.css">
    <link rel="stylesheet" href="assets/css/inicioLogin.css">
    <script src="../public/assets/js/pop.js"></script>
</head>

<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $origem = $_GET['origem'] ?? '';
    $emBatalha = ($origem === 'batalha');
    $tipoBatalha = $_GET['tipo'] ?? ($_SESSION['batalha_atual']['tipo'] ?? ($_SESSION['batalha'] === 'batalhaTreinador' ? 'treinador' : 'matinho'));

    $linkVoltar = '?modulo=mapa&acao=' . htmlspecialchars($_SESSION['mapa'] ?? 'mapaInicio');
    if ($emBatalha) {
        $linkVoltar = ($tipoBatalha === 'treinador')
            ? '?modulo=batalha&acao=batalhaTreinador'
            : '?modulo=batalha&acao=batalhaMatinho';
    }

    // Mapa de itens pelo ID oficial da PokeAPI (ex: 814 = Key Stone / Pedra de Megaevolução)
    $mapaPorId = [
        814 => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/key-stone.png'
    ];

    if (!empty($itensTreinador)) {
        foreach ($itensTreinador as $item) {
            $idItem = (int)($item['id_item'] ?? 0);
            if (!$idItem) continue;

            if (isset($mapaPorId[$idItem])) {
                $urlItem = $mapaPorId[$idItem];
                $_SESSION[$idItem]['urlItem'] = $urlItem;
                $_SESSION['itens'][$idItem]['urlItem'] = $urlItem;
                continue;
            }

            if (!empty($_SESSION[$idItem]['urlItem'])) continue;

            $nomeItem = strtolower(trim($item['nome'] ?? ''));
            $slug = $nomeItem;
            $slug = str_replace(['é', 'è', 'ê'], 'e', $slug);
            $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
            $slug = trim($slug, '-');
            $urlItem = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/{$slug}.png";
            $_SESSION[$idItem]['urlItem'] = $urlItem;
            $_SESSION['itens'][$idItem]['urlItem'] = $urlItem;
        }
    }
?>

<body id="corpoLoja">
    <main id="paginaLoja">
        <div id="lojaContainer">
            <img
                id="imagemLoja"
                src="../public/assets/img/mapas/loja.png"
                alt="Loja"
            >

            <div id="cabecalhoLoja">
                <p id="tituloLoja">BOLSA DE ITENS</p>
            </div>

            <!-- Painel de itens em lista dentro da imagem -->
            <div id="painelItensLoja">
                <?php if (!empty($itensTreinador)): ?>
                    <ul id="listaItensLoja">
                        <?php foreach ($itensTreinador as $item):
                            $idItem = (int)($item['id_item'] ?? 0);
                            $urlImagem = $_SESSION[$idItem]['urlItem'] ?? ($_SESSION['itens'][$idItem]['urlItem'] ?? '');
                            $qtd = (int)($item['quantidade'] ?? 1);
                        ?>
                            <li class="cardItemLoja">
                                <div class="infoItemLoja">
                                    <div class="detalhesItemLoja">
                                        <?php if (!empty($urlImagem)): ?>
                                            <img src="<?= htmlspecialchars($urlImagem) ?>" alt="<?= htmlspecialchars($item['nome'] ?? '') ?>" class="iconeItemLoja" onerror="this.style.display='none'">
                                        <?php endif; ?>
                                        <span class="nomeItem"><?= htmlspecialchars($item['nome'] ?? 'Item') ?></span>
                                    
                                    <button class="botaoInicio info" onclick="pop(
                                        false,
                                        '<?= htmlspecialchars($item['nome'] ?? 'Item', ENT_QUOTES) ?>',
                                        '',
                                        '<?= htmlspecialchars($urlImagem, ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($item['descricao'] ?? 'Item', ENT_QUOTES) ?>',
                                        'Voltar',
                                        'javascript:fecharPop()'
                                    )">
                                        <span class="defaultText info">?</span>
                                    </button>
                                    </div>
                                </div>
                                <div class="quantidadeItemLoja">
                                    <span class="precoItem">x<?= $qtd ?></span>
                                    <?php if ($emBatalha): ?>
                                        <?php
                                            $ehPokebola = ((int)($item['id_categoria'] ?? 0) === 2) || (stripos($item['nome'] ?? '', 'ball') !== false);
                                            $podeUsar = !($tipoBatalha === 'treinador' && $ehPokebola);
                                        ?>
                                        <?php if ($podeUsar): ?>
                                            <form method="POST" action="?modulo=batalha&acao=usarItem" class="formUsarItem">
                                                <input type="hidden" name="id_item" value="<?= $idItem ?>">
                                                <input type="hidden" name="tipo_batalha" value="<?= htmlspecialchars($tipoBatalha) ?>">
                                                <button type="submit" class="botaoUsarItem">
                                                    <span class="hoverText">▸</span>
                                                    <span class="defaultText">USAR</span>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button type="button" class="botaoUsarItem" disabled title="Não é possível usar Pokébola em batalha contra treinador!">
                                                <span class="defaultText">USAR</span>
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <h2 id="subtituloItens">Você não possui itens na bolsa</h2>
                <?php endif; ?>
            </div>
        </div>

        <a href="<?= $linkVoltar ?>" id="botaoSeta" class="setaVoltar" aria-label="Voltar">
            <img
                id="seta"
                src="../public/assets/img/icons/seta.png"
                alt="Voltar"
            >
        </a>
    </main>
</body>
</html>
