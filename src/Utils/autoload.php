<?php
function carregar($classe){
    $arquivo = __DIR__ . "/../Models/" . $classe . ".php";
    if (file_exists($arquivo)) {
        require_once $arquivo;
    }
}

spl_autoload_register('carregar');
?>