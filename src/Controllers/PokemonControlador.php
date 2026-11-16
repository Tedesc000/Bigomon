<?php
class PokemonControlador {
    private $pdo;   

    public function __construct($pdo) {
        $this->pdo = $pdo;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function sus() {
        $_SESSION['sus'] = 'sus';
        require_once __DIR__ . "/../Views/mapas/sus.php";
    }
}