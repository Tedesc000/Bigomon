<?php

class BolsaControlador {
    private $pdo;
    public function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    public function listarItens()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idTreinador = $_SESSION['usuario_logado']['id_treinador'] ?? null;
        $itensTreinador = [];

        if ($idTreinador) {
            require_once __DIR__ . "/../Models/TreinadorItem.php";
            $treinadorItem = new TreinadorItem($this->pdo);
            $itensTreinador = $treinadorItem->buscarItensTreinador($idTreinador);
        }

        require_once __DIR__ . "/../Views/bolsa/itens.php";
    }

    public function listarPokemons()
    {
        require_once __DIR__ . "/../Views/bolsa/pokemons.php";
    }
    
}