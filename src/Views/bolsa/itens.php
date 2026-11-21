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
    <title>Bolsa de Itens</title>
    <link rel="stylesheet" href="assets/css/mapas.css?v=<?= $versaoAsset('assets/css/mapas.css') ?>">
    <link rel="stylesheet" href="assets/css/loja.css?v=<?= $versaoAsset('assets/css/loja.css') ?>">
    <link rel="stylesheet" href="assets/css/inicioLogin.css?v=<?= $versaoAsset('assets/css/inicioLogin.css') ?>">
    <link rel="stylesheet" href="assets/css/bolsaItens.css?v=<?= $versaoAsset('assets/css/bolsaItens.css') ?>">
</head>

<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once __DIR__ . '/../../Utils/tmImagens.php';

    $linkVoltar = '?modulo=mapa&acao=' . htmlspecialchars($_SESSION['mapa'] ?? 'mapaInicio');

    // Bolso de cada item (abas da bolsa) a partir do efeito
    $bolsoDoItem = static function (int $idItem): string {
        return match (ItemEfeito::efeito($idItem)['tipo'] ?? '') {
            'cura', 'reviver' => 'cura',
            'pokebola' => 'pokebola',
            'tm' => 'tm',
            default => 'especial',
        };
    };

    $bolsos = [
        'todos' => 'TODOS',
        'cura' => 'CURA',
        'pokebola' => 'POKÉBOLAS',
        'tm' => 'TMs',
        'especial' => 'ESPECIAIS',
    ];

    // Dados da equipe para o modal de uso de item (escolher o Pokémon e, no TM, o ataque a esquecer)
    $equipeJs = array_map(static fn($p) => [
        'id_pokemon' => $p['id_pokemon'],
        'id_pokedex' => $p['id_pokedex'],
        'nome' => $p['nome'],
        'hp_atual' => $p['hp_atual'],
        'hp_maximo' => $p['hp_maximo'],
        'status' => $p['status'],
        'pode_evoluir' => $p['pode_evoluir'] ?? false,
        'movimentos' => array_map(static fn($m) => [
            'id_movimento' => $m['id_movimento'],
            'nome' => $m['nome'],
            'tipo_nome' => $m['tipo_nome'],
            'poder' => $m['poder'],
        ], $p['movimentos']),
    ], $equipeBolsa ?? []);
?>

<body id="corpoLoja">
    <main id="paginaLoja">
        <div id="lojaContainer">
            <img id="imagemLoja" src="../public/assets/img/mapas/loja.png" alt="Loja">

            <div id="cabecalhoLoja">
                <p id="tituloLoja">BOLSA DE ITENS</p>
            </div>

            <section id="painelBolsa" aria-label="Itens da bolsa">
                <nav class="bolsaAbas" id="bolsaAbas" aria-label="Bolsos">
                    <?php foreach ($bolsos as $chave => $rotulo): ?>
                        <button type="button" class="bolsaAba<?= $chave === 'todos' ? ' ativa' : '' ?>" data-bolso="<?= $chave ?>"><?= $rotulo ?></button>
                    <?php endforeach; ?>
                </nav>

                <div class="bolsaCorpo">
                    <ul class="bolsaLista" id="bolsaLista">
                        <?php foreach ($itensTreinador as $item):
                            $idItem = (int) ($item['id_item'] ?? 0);
                            $nome = (string) ($item['nome'] ?? 'Item');
                            $ehTM = ItemEfeito::ehTM($idItem);
                        ?>
                            <li>
                                <button type="button" class="bolsaItem"
                                    data-id-item="<?= $idItem ?>"
                                    data-nome="<?= htmlspecialchars($nome, ENT_QUOTES) ?>"
                                    data-descricao="<?= htmlspecialchars((string) ($item['descricao'] ?? ''), ENT_QUOTES) ?>"
                                    data-tipo="<?= htmlspecialchars(ItemEfeito::efeito($idItem)['tipo'] ?? '', ENT_QUOTES) ?>"
                                    data-bolso="<?= $bolsoDoItem($idItem) ?>"
                                    data-usavel="<?= ItemEfeito::usavelForaDeBatalha($idItem) ? '1' : '0' ?>"
                                    data-quantidade="<?= $ehTM ? 'TM' : 'x' . (int) ($item['quantidade'] ?? 1) ?>"
                                    data-imagem="<?= htmlspecialchars(urlImagemItem($idItem, $nome), ENT_QUOTES) ?>">
                                    <img class="bolsaItemImg" src="<?= htmlspecialchars(urlImagemItem($idItem, $nome)) ?>" alt="" onerror="this.style.visibility='hidden'">
                                    <span class="bolsaItemNome"><?= htmlspecialchars(mb_strtoupper($nome, 'UTF-8')) ?></span>
                                    <span class="bolsaItemQtd"><?= $ehTM ? 'TM' : 'x' . (int) ($item['quantidade'] ?? 1) ?></span>
                                </button>
                            </li>
                        <?php endforeach; ?>
                        <li class="bolsaVazia<?= empty($itensTreinador) ? '' : ' hidden' ?>" id="bolsaVazia">
                            <?= empty($itensTreinador) ? 'Você não possui itens na bolsa.' : 'Nenhum item neste bolso.' ?>
                        </li>
                    </ul>

                    <aside class="bolsaDetalhe" id="bolsaDetalhe">
                        <div class="bolsaDetalheImgWrap">
                            <img class="bolsaDetalheImg hidden" id="bolsaDetalheImg" src="" alt="">
                        </div>
                        <p class="bolsaDetalheNome" id="bolsaDetalheNome">BOLSA</p>
                        <p class="bolsaDetalheTexto" id="bolsaDetalheTexto">Escolha um item para ver o que ele faz.</p>
                        <button type="button" class="bolsaBotaoUsar hidden" id="bolsaBotaoUsar"><span class="cursor">▶</span> USAR</button>
                    </aside>
                </div>
            </section>
        </div>

        <a href="<?= $linkVoltar ?>" id="botaoSeta" class="setaVoltar" aria-label="Voltar">
            <img id="seta" src="../public/assets/img/icons/seta.png" alt="Voltar">
        </a>
    </main>

    <!-- Escolha do Pokémon (mesmo visual da tela de POKEMON da batalha) -->
    <section class="bolsaParty hidden" id="bolsaParty" aria-modal="true" role="dialog" aria-label="Escolher Pokémon">
        <div class="bolsaPartyLista" id="bolsaPartyLista"></div>
        <footer class="bolsaPartyRodape">
            <div class="bolsaPrompt" id="bolsaPartyPrompt">Usar em qual POKÉMON?</div>
            <button type="button" class="bolsaCancelar" id="bolsaPartyCancelar">CANCELAR</button>
        </footer>
    </section>

    <!-- Mensagem de resultado (estilo caixa de texto da batalha) -->
    <div class="bolsaMensagemFundo hidden" id="bolsaMensagemFundo">
        <div class="bolsaMensagem" id="bolsaMensagem" role="alert" tabindex="-1">
            <img class="bolsaMensagemImg hidden" id="bolsaMensagemImg" src="" alt="">
            <p class="bolsaMensagemTexto" id="bolsaMensagemTexto"></p>
            <span class="bolsaMensagemSeta" aria-hidden="true">▼</span>
        </div>
    </div>

    <script>
        window.EQUIPE_BOLSA = <?= json_encode($equipeJs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    </script>
    <script src="../public/assets/js/usarItem.js?v=<?= $versaoAsset('assets/js/usarItem.js') ?>"></script>
</body>
</html>
