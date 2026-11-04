<?php

set_time_limit(0); 
ini_set('max_execution_time', '0');

require_once __DIR__ . '/../../src/Utils/conexao.php';

$sql = "";

$inicio = 252;
$fim = 386;
$cadeias = [];

for($i=$inicio; $i<=$fim; $i++){
    
    $url = "https://pokeapi.co/api/v2/pokemon/{$i}";
    $urlSpecie = "https://pokeapi.co/api/v2/pokemon-species/{$i}/";

    usleep(250000);
    $resposta = @file_get_contents($url);
    usleep(250000);
    $respostaSpecie = @file_get_contents($urlSpecie);

    if ($resposta === false || $respostaSpecie === false) {
        continue;
    }

    $dados = json_decode($resposta);
    $dadosSpecie = json_decode($respostaSpecie);

    if (!isset($dados->id, $dados->name)) {
        continue;
    }

    $id = $dados->id;
    $nome = str_replace("'", "''", $dados->name);

    $fase = 1;
    $id_evolucao = 'NULL';

    if (isset($dadosSpecie->evolution_chain->url)) {
        $urlChain = $dadosSpecie->evolution_chain->url;
        if (!isset($cadeias[$urlChain])) {
            usleep(250000);
            $respostaChain = @file_get_contents($urlChain);
            if ($respostaChain !== false) {
                $cadeias[$urlChain] = json_decode($respostaChain);
            }
        }

        $dadosChain = $cadeias[$urlChain] ?? null;

        if ($dadosChain && isset($dadosChain->chain)) {
            $evolucao = $dadosChain->chain;

            while ($evolucao && isset($evolucao->species->url)) {
                $idAtual = (int) basename(rtrim($evolucao->species->url, '/'));
                if ($idAtual === (int) $id) {
                    break;
                }

                $fase++;
                $evolucao = $evolucao->evolves_to[0] ?? null;
            }

            if ($evolucao && isset($evolucao->evolves_to[0]->species->url)) {
                $id_evolucao = (int) basename(rtrim($evolucao->evolves_to[0]->species->url, '/'));
            }
        }
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