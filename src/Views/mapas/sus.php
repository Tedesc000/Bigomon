<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SUS</title>
    <link rel="stylesheet" href="assets/css/mapas.css">
    <link rel="stylesheet" href="assets/css/sus.css">
</head>

<body id="corpoSus">
    <main id="paginaSus">
        <a href="?modulo=mapa&acao=<?= htmlspecialchars($_SESSION['mapa'] ?? 'mapaInicio') ?>" id="botaoSeta" class="setaVoltar" aria-label="Voltar para o mapa">
            <img
                id="seta"
                src="../public/assets/img/icons/seta.png"
                alt="Voltar"
            >
        </a>
        <div id="susContainer">
            <img
                id="imagemSus"
                src="../public/assets/img/mapas/sus.png"
                alt="Interior do Centro Pokémon"
            >
            <div id="healingMachineControl">
                <img
                    id="imagemHealingMachine"
                    src="../public/assets/img/icons/healing_machine.png"
                    alt="Máquina de cura Pokémon"
                >
                <button type="submit" class="botaoCurar">
                    <span class="hoverText">▸</span>
                    <span class="defaultText">CURAR</span>
                </button>
            </div>
        </div>
        <aside id="barraPokemons" aria-label="Pokémons do treinador">
            <?php if (!empty($pokemons)): ?>
                <div id="listaPokemons">
                    <?php foreach ($pokemons as $pokemon): ?>
                        <?php
                        $hpMaximo = max(1, (int) $pokemon['hp_maximo']);
                        $hpAtual = max(0, min((int) $pokemon['hp_atual'], $hpMaximo));
                        ?>
                        <article class="cartaoPokemon"> 
                            <img
                                class="imagemPokemon"
                                src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/<?= (int) $pokemon['id_pokedex'] ?>.png"
                                alt=""
                            >
                            <div class="dadosPokemon">
                                <h2><?= htmlspecialchars($pokemon['nome'], ENT_QUOTES, 'UTF-8') ?></h2>
                                <progress
                                    class="barraVidaPokemon"
                                    value="<?= $hpAtual ?>"
                                    max="<?= $hpMaximo ?>"
                                    aria-label="Vida de <?= htmlspecialchars($pokemon['nome'], ENT_QUOTES, 'UTF-8') ?>"
                                ></progress>
                                <span><?= $hpAtual ?>/<?= $hpMaximo ?> HP</span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p id="semPokemons">Você ainda não tem Pokémons.</p>
            <?php endif; ?>
        </aside>
    </main>
</body>
</html>
