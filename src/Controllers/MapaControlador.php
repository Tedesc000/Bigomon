<?php
class MapaControlador {
    private $pdo;   

    public function __construct($pdo) {
        $this->pdo = $pdo;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function carregarSlotsEquipe(): array {
        require_once __DIR__ . "/TreinadorControlador.php";
        $treinadorCtrl = new TreinadorControlador($this->pdo);
        return $treinadorCtrl->buscarPokemonsDosSlots();
    }

    public function mapaInicio() {
        $_SESSION['mapa'] = 'mapaInicio';
        $_SESSION['id_loja'] = 1;
        $slotsEquipe = $this->carregarSlotsEquipe();
        require_once __DIR__ . "/../Views/mapas/mapaInicio.php";
    }

    public function mapa1() {
        $_SESSION['mapa'] = 'mapa1';
        $_SESSION['id_loja'] = 2;
        $slotsEquipe = $this->carregarSlotsEquipe();
        require_once __DIR__ . "/../Views/mapas/mapa1.php";
    }

    public function ginasio1() {
        $_SESSION['ginasio1_visitado'] = true;
        $_SESSION['mapa'] = 'ginasio1';
        $slotsEquipe = $this->carregarSlotsEquipe();
        require_once __DIR__ . "/../Views/mapas/ginasio1.php";
    }

    public function mapa2() {
        $_SESSION['mapa'] = 'mapa2';
        $_SESSION['id_loja'] = 3;
        $slotsEquipe = $this->carregarSlotsEquipe();
        require_once __DIR__ . "/../Views/mapas/mapa2.php";
    }

    public function ginasio2() {
        $_SESSION['ginasio2_visitado'] = true;
        $_SESSION['mapa'] = 'ginasio2';
        $slotsEquipe = $this->carregarSlotsEquipe();
        require_once __DIR__ . "/../Views/mapas/ginasio2.php";
    }

    public function mapa3() {
        $_SESSION['mapa'] = 'mapa3';
        $_SESSION['id_loja'] = 4;
        $slotsEquipe = $this->carregarSlotsEquipe();
        require_once __DIR__ . "/../Views/mapas/mapa3.php";
    }

    public function ginasio3() {
        $_SESSION['ginasio3_visitado'] = true;
        $_SESSION['mapa'] = 'ginasio3';
        $slotsEquipe = $this->carregarSlotsEquipe();
        require_once __DIR__ . "/../Views/mapas/ginasio3.php";
    }

    public function mapa4() {
        $_SESSION['mapa'] = 'mapa4';
        $_SESSION['id_loja'] = 5;
        $slotsEquipe = $this->carregarSlotsEquipe();
        require_once __DIR__ . "/../Views/mapas/mapa4.php";
    }

    public function ginasio4() {
        $_SESSION['ginasio4_visitado'] = true;
        $_SESSION['mapa'] = 'ginasio4';
        $slotsEquipe = $this->carregarSlotsEquipe();
        require_once __DIR__ . "/../Views/mapas/ginasio4.php";
    }

    public function mapa5() {
        $_SESSION['mapa'] = 'mapa5';
        $_SESSION['id_loja'] = 6;
        $slotsEquipe = $this->carregarSlotsEquipe();
        require_once __DIR__ . "/../Views/mapas/mapa5.php";
    }

    public function ginasio5() {
        $_SESSION['ginasio5_visitado'] = true;
        $_SESSION['mapa'] = 'ginasio5';
        $slotsEquipe = $this->carregarSlotsEquipe();
        require_once __DIR__ . "/../Views/mapas/ginasio5.php";
    }

    public function mapa6() {
        $_SESSION['mapa'] = 'mapa6';
        $_SESSION['id_loja'] = 7;
        $slotsEquipe = $this->carregarSlotsEquipe();
        require_once __DIR__ . "/../Views/mapas/mapa6.php";
    }

    public function ginasio6() {
        $_SESSION['ginasio6_visitado'] = true;
        $_SESSION['mapa'] = 'ginasio6';
        $slotsEquipe = $this->carregarSlotsEquipe();
        require_once __DIR__ . "/../Views/mapas/ginasio6.php";
    }

    public function mapa7() {
        $_SESSION['mapa'] = 'mapa7';
        $_SESSION['id_loja'] = 8;
        $slotsEquipe = $this->carregarSlotsEquipe();
        require_once __DIR__ . "/../Views/mapas/mapa7.php";
    }

    public function ginasio7() {
        $_SESSION['ginasio7_visitado'] = true;
        $_SESSION['mapa'] = 'ginasio7';
        $slotsEquipe = $this->carregarSlotsEquipe();
        require_once __DIR__ . "/../Views/mapas/ginasio7.php";
    }
    
    public function mapa8() {
        $_SESSION['mapa'] = 'mapa8';
        $_SESSION['id_loja'] = 9;
        $slotsEquipe = $this->carregarSlotsEquipe();
        require_once __DIR__ . "/../Views/mapas/mapa8.php";
    }

    public function ginasio8() {
        $_SESSION['ginasio8_visitado'] = true;
        $_SESSION['mapa'] = 'ginasio8';
        $slotsEquipe = $this->carregarSlotsEquipe();
        require_once __DIR__ . "/../Views/mapas/ginasio8.php";
    }
}