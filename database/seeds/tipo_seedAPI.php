<?php

set_time_limit(0); 
ini_set('max_execution_time', '0');

require_once __DIR__ . '/../../src/Utils/conexao.php';

$sql = "";

$inicio = 252;
$fim = 386;

for($i=$inicio; $i<=$fim; $i++){

    $url = "https://pokeapi.co/api/v2/pokemon/{$i}";

    usleep(250000);
    $resposta = @file_get_contents($url);

    if ($resposta === false) {
        continue;
    }

    $dados = json_decode($resposta);

    if (!isset($dados->id, $dados->name)) {
        continue;
    }

    $id = $dados->id;
    $tipo1 = (int) basename(rtrim($dados->types[0]->type->url, '/'));
    if(isset($dados->types[1])){
        $tipo2 = (int) basename(rtrim($dados->types[1]->type->url, '/'));
    } 
    if(isset($tipo2)){
        $sql.="INSERT INTO pokedex_tipo (id_pokedex, id_tipo) VALUES ({$id}, {$tipo1})
            INSERT INTO pokedex_tipo (id_pokedex, id_tipo) VALUES ({$id}, {$tipo2});";
    }
    else{
        $sql.="INSERT INTO pokedex_tipo (id_pokedex, id_tipo) VALUES ({$id}, {$tipo1});";
    }
}

echo $sql;
file_put_contents(__DIR__ . "/3-pokedex_tipo_seed.sql", $sql);

