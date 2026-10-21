<?php
require_once "../src/Utils/autoload.php";
require_once "../src/Utils/conexao.php";

$pdo = getConexao();

//captura o modulo e ação
$modulo = $_GET['modulo'] ?? 'treinador';
$acao = $_GET['acao'] ?? 'inicio';

//define o nome da classe controlador e o seu caminho
$nomeControlador = ucfirst($modulo) . "Controlador"; //ex: PokemonControlador
$arquivoControlador = "../src/Controllers/{$nomeControlador}.php";

//verifica se o controlador existe
if (file_exists($arquivoControlador)){
    require_once $arquivoControlador;
    $controlador = new $nomeControlador($pdo);

    //verifica se o método existe
    if (method_exists($controlador, $acao)){
        $controlador->$acao();
    } else{
        echo "Ação não encontrada!";
    }
} else {
    echo "Página não encontrada!";
}