<?php
class MapaControlador {
    private $pdo;   

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function mapaInicio() {
        require_once __DIR__ . "/../Views/mapas/mapaInicio.php";
    }
}