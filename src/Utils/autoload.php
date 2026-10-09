<?php
function carregar($classe){
    require_once(__DIR__ . "/src/Models/" . $classe . ".php");
}

spl_autoload_register('carregar');
?>