<?php
require_once "../src/Utils/autoload.php";

//captura o modulo e ação
$modulo = $_GET['modulo'];
$acao = $_GET['acao'];

//define o nome da classe controlador e o seu caminho
$nomeControlador = ucfirst($modulo) . "Controlador"; //ex: PokemonControlador
$arquivoControlador = "Controllers/{$nomeControlador}.php";

//verifica se o controlador existe
if (file_exists($arquivoControlador)){
    require_once $arquivoControlador;
    $controlador = new $nomeControlador($db);

    //verifica se o método existe
    if (method_exists($controlador, $acao)){
        $controlador->$acao();
    } else{
        echo "Ação não encontrada!";
    }
} else {
    echo "Página não encontrada!";
}