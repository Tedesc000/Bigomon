<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/batalha.css">
    <title>Bigomon - Wild Battle</title>
</head>
<body id="wildBody">
    <main class="battle-screen" aria-label="Tela de batalha">
        <section class="pokemon-status pokemon-status-front" aria-label="Status do Pokémon adversário">
            <div class="status-name-row">
                <span class="pokemon-name">TREECKO</span>
            </div>
            <div class="hp-row">
                <span class="hp-label">HP</span>
                <div class="hp-track"><div class="hp-bar"></div></div>
            </div>
        </section>

        <div class="pokemon-sprite pokemon-sprite-front" aria-label="Treecko adversário">
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/versions/generation-viii/brilliant-diamond-shining-pearl/252.webp" alt="Treecko adversário">
        </div>

        <section class="pokemon-status pokemon-status-back" aria-label="Status do seu Pokémon">
            <div class="status-name-row">
                <span class="pokemon-name">TREECKO</span>
            </div>
            <div class="hp-row">
                <span class="hp-label">HP</span>
                <div class="hp-track"><div class="hp-bar"></div></div>
            </div>
            <div class="hp-numbers">36 / 36</div>
        </section>

        <div class="pokemon-sprite pokemon-sprite-back" aria-label="Seu Treecko">
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/versions/generation-viii/brilliant-diamond-shining-pearl/back/252.webp" alt="Seu Treecko">
        </div>

        <section class="battle-menu" aria-label="Menu de batalha">
            <div class="battle-message">
                What will<br>TREECKO do?
            </div>
            <nav class="battle-actions" aria-label="Ações de batalha">
                <button type="button" class="battle-action"><span class="cursor">▶</span> FIGHT</button>
                <button type="button" class="battle-action"><span class="cursor">▶</span> BAG</button>
                <button type="button" class="battle-action"><span class="cursor">▶</span> POKEMON</button>
                <button type="button" class="battle-action"><span class="cursor">▶</span> RUN</button>
            </nav>
        </section>
    </main>
</body>
</html>