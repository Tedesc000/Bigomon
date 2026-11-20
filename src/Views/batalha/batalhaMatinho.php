<?php
$jogador = $estadoInicial['jogador'];
$selvagem = $estadoInicial['selvagem'];

$hpPctJogador = $jogador['hp_maximo'] > 0 ? round(($jogador['hp_atual'] / $jogador['hp_maximo']) * 100, 2) : 0;
$hpPctSelvagem = $selvagem['hp_maximo'] > 0 ? round(($selvagem['hp_atual'] / $selvagem['hp_maximo']) * 100, 2) : 0;

$classeHp = static function (float $pct): string {
    if ($pct > 50) {
        return 'hp-high';
    }
    if ($pct > 20) {
        return 'hp-mid';
    }
    return 'hp-low';
};
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/batalha.css">
    <title>Bigomon - Wild Battle</title>
</head>
<body id="wildBody">
    <main class="battle-screen">
        <!-- Status do Pokémon selvagem -->
        <section class="pokemon-status pokemon-status-front" aria-label="Status do Pokémon adversário">
            <div class="status-name-row">
                <span class="pokemon-name" id="wildName"><?= htmlspecialchars($selvagem['nome'], ENT_QUOTES, 'UTF-8') ?></span>
                <span class="status-badge hidden" id="wildStatusBadge"></span>
            </div>
            <div class="hp-row">
                <span class="hp-label">HP</span>
                <div class="hp-track">
                    <div class="hp-bar <?= $classeHp($hpPctSelvagem) ?>" id="wildHpBar" style="width: <?= $hpPctSelvagem ?>%;"></div>
                </div>
            </div>
        </section>

        <!-- Sprite do Pokémon selvagem -->
        <div class="pokemon-sprite pokemon-sprite-front" id="wildSpriteWrap" aria-label="Pokémon adversário">
            <img
                id="wildSprite"
                src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/versions/generation-viii/brilliant-diamond-shining-pearl/<?= (int) $selvagem['id_pokedex'] ?>.webp"
                alt="<?= htmlspecialchars($selvagem['nome'], ENT_QUOTES, 'UTF-8') ?> selvagem"
            >
        </div>

        <!-- Status do Pokémon do jogador -->
        <section class="pokemon-status pokemon-status-back" aria-label="Status do seu Pokémon">
            <div class="status-name-row">
                <span class="pokemon-name" id="playerName"><?= htmlspecialchars($jogador['nome'], ENT_QUOTES, 'UTF-8') ?></span>
                <span class="status-badge hidden" id="playerStatusBadge"></span>
            </div>
            <div class="hp-row">
                <span class="hp-label">HP</span>
                <div class="hp-track">
                    <div class="hp-bar <?= $classeHp($hpPctJogador) ?>" id="playerHpBar" style="width: <?= $hpPctJogador ?>%;"></div>
                </div>
            </div>
            <div class="hp-numbers" id="playerHpNumbers"><?= (int) $jogador['hp_atual'] ?> / <?= (int) $jogador['hp_maximo'] ?></div>
        </section>

        <!-- Sprite do Pokémon do jogador -->
        <div class="pokemon-sprite pokemon-sprite-back" id="playerSpriteWrap" aria-label="Seu Pokémon">
            <img
                id="playerSprite"
                src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/back/<?= (int) $jogador['id_pokedex'] ?>.png"
                alt="Seu <?= htmlspecialchars($jogador['nome'], ENT_QUOTES, 'UTF-8') ?>"
            >
        </div>

        <!-- Menu inferior (Estilo Pokémon Emerald) -->
        <section class="battle-menu" id="battleMenu" aria-label="Menu de batalha">
            <!-- Modo principal / mensagem -->
            <div class="battle-message" id="battleMessage">
                <span id="battleMessageText">O que<br><?= htmlspecialchars($jogador['nome'], ENT_QUOTES, 'UTF-8') ?> vai fazer?</span>
                <span class="next-arrow hidden" id="nextArrow" aria-hidden="true">▼</span>
            </div>

            <!-- Ações principais -->
            <nav class="battle-actions" id="battleActions" aria-label="Ações de batalha">
                <button type="button" class="battle-action" id="btnFight" data-action="fight"><span class="cursor">▶</span> FIGHT</button>
                <button type="button" class="battle-action" id="btnBag" data-action="bag"><span class="cursor">▶</span> BAG</button>
                <button type="button" class="battle-action" id="btnPokemon" data-action="pokemon"><span class="cursor">▶</span> POKEMON</button>
                <button type="button" class="battle-action" id="btnRun" data-action="run"><span class="cursor">▶</span> RUN</button>
            </nav>

            <!-- Submenu FIGHT -->
            <div class="fight-panel hidden" id="fightPanel" aria-label="Escolha de golpes">
                <div class="move-grid" id="moveGrid"></div>
                <aside class="move-details" id="moveDetails">
                    <div class="move-detail-row">
                        <span class="move-detail-label">TYPE/</span>
                        <span class="move-type-pill" id="moveDetailType">NORMAL</span>
                    </div>
                    <div class="move-detail-row">
                        <span class="move-detail-label">CAT/</span>
                        <span class="move-detail-value" id="moveDetailCat">PHYSICAL</span>
                    </div>
                    <div class="move-detail-row">
                        <span class="move-detail-label">POWER/</span>
                        <span class="move-detail-value" id="moveDetailPower">40</span>
                    </div>
                    <div class="move-detail-row">
                        <span class="move-detail-label">ACC/</span>
                        <span class="move-detail-value" id="moveDetailAcc">100%</span>
                    </div>
                    <button type="button" class="move-back-btn" id="btnMoveBack"><span class="cursor">▶</span> BACK</button>
                </aside>
            </div>
        </section>

        <!-- Party Screen -->
        <section class="party-overlay hidden" id="partyOverlay" aria-label="Equipe Pokémon" aria-modal="true" role="dialog">
            <div class="party-layout">
                <div class="party-active-column" id="partyActiveSlot"></div>
                <div class="party-bench-column" id="partyBenchList"></div>
            </div>

            <footer class="party-footer">
                <div class="party-prompt" id="partyPrompt">Escolha um POKÉMON.</div>
                <button type="button" class="party-cancel-btn" id="btnPartyCancel">CANCELAR</button>
            </footer>

            <!-- Mini-menu TROCAR / CANCELAR-->
            <div class="party-action-box hidden" id="partyActionBox">
                <button type="button" class="party-action-item" id="btnPartySwitch"><span class="cursor">▶</span> TROCAR</button>
                <button type="button" class="party-action-item" id="btnPartyActionCancel"><span class="cursor">▶</span> CANCELAR</button>
            </div>
        </section>
    </main>

    <script>
        window.BATALHA_INICIAL = <?= json_encode($estadoInicial, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    </script>
    <script src="assets/js/batalha.js"></script>
</body>
</html>
