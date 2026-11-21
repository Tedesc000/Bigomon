<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarioLogado = $_SESSION['usuario_logado'] ?? [];
$nomeTreinador = !empty($usuarioLogado['nome']) ? $usuarioLogado['nome'] : 'Treinador';
$fotoTreinador = !empty($usuarioLogado['foto']) ? $usuarioLogado['foto'] : 'fotoPerfilDefault.webp';
$dinheiroTreinador = $usuarioLogado['dinheiro'] ?? 0;

// Se os dados dos pokémons dos slots ainda não foram passados pelo controlador,
// busca no banco a partir dos IDs dos slots salvos na sessão através de TreinadorControlador
if (!isset($slotsEquipe) || !is_array($slotsEquipe)) {
    require_once __DIR__ . '/conexao.php';
    $pdoFront = getConexao();
    require_once __DIR__ . '/../Controllers/TreinadorControlador.php';
    $treinadorCtrl = new TreinadorControlador($pdoFront);
    $slotsEquipe = $treinadorCtrl->buscarPokemonsDosSlots();
}
?>

<!-- Front do Jogo (fora da imagem do mapa) -->
<div id="hudFrontJogo" aria-label="Interface do Treinador">

    <!-- Canto Superior Esquerdo: Quantidade de Dinheiro -->
    <div id="hudDinheiro" class="pontoFront" title="Bigoreais">
        <img src="assets/img/icons/pokedollar.webp" alt="Moeda" class="hudIconeMoeda">
        <span class="hudValorDinheiro">R$ <?= number_format($dinheiroTreinador, 2, ',', '.') ?></span>
    </div>

    <!-- Canto Superior Direito: Bags de Itens e de Pokémons -->
    <a href="?modulo=bolsa&acao=listarItens" id="hudBolsaItens" class="pontoFront hudBotaoFront" title="Mochila de Itens" aria-label="Abrir Bolsa de Itens">
        <span class="hudIconeFront" aria-hidden="true">
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/potion.png" alt="Itens" class="iconItem">
        </span>
        <span class="hudTextoFront">ITENS</span>
    </a>

    <a href="?modulo=bolsa&acao=listarTodosPokemons" id="hudBolsaPokemons" class="pontoFront hudBotaoFront" title="Equipe de Pokémons" aria-label="Ver Pokémons">
        <span class="hudIconeFront" aria-hidden="true">
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png" alt="Pokemons" class="iconPokebola">
        </span>
        <span class="hudTextoFront">POKÉMONS</span>
    </a>

    <!-- Canto Inferior Esquerdo: Perfil do Treinador -->
    <a href="?modulo=treinador&acao=verPerfil" id="hudPerfil" class="pontoFront" title="Ver Perfil" aria-label="Ver Perfil">
        <div class="hudFotoWrapper">
            <img src="assets/img/fotoPerfil/<?= htmlspecialchars($fotoTreinador) ?>" alt="Foto Perfil" class="hudFotoPerfil">
        </div>
        <div class="hudDadosPerfil">
            <span class="hudNomeTreinador"><?= htmlspecialchars($nomeTreinador) ?></span>
            <span class="hudVerPerfilAviso">VER PERFIL ▸</span>
        </div>
    </a>

    <!-- Canto Direito: Coluna com os 6 Slots e HP Atual -->
    <aside id="hudSlotsColuna" class="pontoFront" aria-label="Equipe Pokémon">
        <div class="hudSlotsCabecalho">
            <span class="hudSlotsTitulo">EQUIPE</span>
        </div>
        <div class="hudSlotsLista">
            <?php foreach ($slotsEquipe as $idx => $pkm): ?>
                <?php if ($pkm): ?>
                    <?php 
                        $hpMax = max(1, (int)($pkm['hp_maximo'] ?? 100));
                        $hpAtu = max(0, min((int)($pkm['hp_atual'] ?? $hpMax), $hpMax));
                        $pctHp = round(($hpAtu / $hpMax) * 100);
                        $classeHp = ($pctHp > 50) ? 'hpAlto' : (($pctHp > 20) ? 'hpMedio' : 'hpBaixo');
                    ?>
                    <div class="hudSlotCard slotOcupado" title="<?= htmlspecialchars($pkm['nome']) ?>: <?= $hpAtu ?>/<?= $hpMax ?> HP">
                        <img 
                            src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/<?= (int)$pkm['id_pokedex'] ?>.png" 
                            alt="<?= htmlspecialchars($pkm['nome']) ?>" 
                            class="hudSlotSprite"
                            onerror="this.src='https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/0.png'"
                        >
                        <div class="hudSlotInfo">
                            <span class="hudSlotNome"><?= htmlspecialchars($pkm['nome']) ?></span>
                            <div class="hudSlotBarraHpFundo">
                                <div class="hudSlotBarraHpProgresso <?= $classeHp ?>" style="width: <?= $pctHp ?>%;"></div>
                            </div>
                            <span class="hudSlotHpTexto"><?= $hpAtu ?>/<?= $hpMax ?></span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="hudSlotCard slotVazio" title="Slot <?= $idx + 1 ?> vazio">
                        <span class="hudSlotVazioIcone" aria-hidden="true">⚪</span>
                        <span class="hudSlotVazioTexto">SLOT <?= $idx + 1 ?></span>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </aside>

</div>
