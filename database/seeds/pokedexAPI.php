<?php

require_once __DIR__ . '/../../src/Utils/conexao.php';

$sql = "";

$inicio = 252;
$fim = 386;
$cadeias = [];

for($i=$inicio; $i<=$fim; $i++){
    
    $url = "https://pokeapi.co/api/v2/pokemon/{$i}";
    $urlSpecie = "https://pokeapi.co/api/v2/pokemon-species/{$i}/";

    $resposta = file_get_contents($url);
    $respostaSpecie = file_get_contents($urlSpecie);

    $dados = json_decode($resposta);
    $dadosSpecie = json_decode($respostaSpecie);

    $urlChain = $dadosSpecie->evolution_chain->url;
    if (!isset($cadeias[$urlChain])) {
        $respostaChain = file_get_contents($urlChain);
        $cadeias[$urlChain] = json_decode($respostaChain);
    }
    $dadosChain = $cadeias[$urlChain];

    $id = $dados->id;
    $nome = $dados->name;

    $fase = 1;
    $evolucao = $dadosChain->chain;
    while ((int) basename(rtrim($evolucao->species->url, '/')) !== $id) {
        $fase++;
        $evolucao = $evolucao->evolves_to[0];
    }
    if(isset($evolucao->evolves_to[0])){
        $id_evolucao = (int) basename(rtrim($evolucao->evolves_to[0]->species->url, '/'));
    } else {
        $id_evolucao = 'NULL';
    }

    $hp = $dados->stats[0]->base_stat;
    $atk = $dados->stats[1]->base_stat;
    $def = $dados->stats[2]->base_stat;
    $spa = $dados->stats[3]->base_stat;
    $spd = $dados->stats[4]->base_stat;
    $spe = $dados->stats[5]->base_stat;

    $sql .= 
    "INSERT INTO pokedex (id_pokedex, nome, fase, hp, atk, def, spa, spd, spe, id_evolucao) 
    VALUES ({$id}, '{$nome}', {$fase}, {$hp}, {$atk}, {$def}, {$spa}, {$spd}, {$spe}, {$id_evolucao});";
}

echo $sql;
file_put_contents(__DIR__ . "/pokedex_seed.sql", $sql);