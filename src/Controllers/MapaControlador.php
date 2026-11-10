<?php
class MapaControlador {
    private $pdo;   

    public function __construct($pdo) {
        $this->pdo = $pdo;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function mapaInicio() {
        require_once __DIR__ . "/../Views/mapas/mapaInicio.php";
        $_SESSION['mapa'] = 'mapaInicio';
    }

    public function mapa1() {
        require_once __DIR__ . "/../Views/mapas/mapa1.php";
        $_SESSION['mapa'] = 'mapa1';
    }
    public function ginasio1() {
        $_SESSION['ginasio1_visitado'] = true;
        require_once __DIR__ . "/../Views/mapas/ginasio1.php";
        $_SESSION['mapa'] = 'ginasio1';
    }

    public function mapa2() {
        require_once __DIR__ . "/../Views/mapas/mapa2.php";
        $_SESSION['mapa'] = 'mapa2';
    }

    public function ginasio2() {
        $_SESSION['ginasio2_visitado'] = true;
        require_once __DIR__ . "/../Views/mapas/ginasio2.php";
        $_SESSION['mapa'] = 'ginasio2';
    }

    public function mapa3() {
        require_once __DIR__ . "/../Views/mapas/mapa3.php";
        $_SESSION['mapa'] = 'mapa3';
    }

    public function ginasio3() {
        $_SESSION['ginasio3_visitado'] = true;
        require_once __DIR__ . "/../Views/mapas/ginasio3.php";
        $_SESSION['mapa'] = 'ginasio3';
    }

    public function mapa4() {
        require_once __DIR__ . "/../Views/mapas/mapa4.php";
        $_SESSION['mapa'] = 'mapa4';
    }

    public function ginasio4() {
        $_SESSION['ginasio4_visitado'] = true;
        require_once __DIR__ . "/../Views/mapas/ginasio4.php";
        $_SESSION['mapa'] = 'ginasio4';
    }

    public function mapa5() {
        require_once __DIR__ . "/../Views/mapas/mapa5.php";
        $_SESSION['mapa'] = 'mapa5';
    }

    public function ginasio5() {
        $_SESSION['ginasio5_visitado'] = true;
        require_once __DIR__ . "/../Views/mapas/ginasio5.php";
        $_SESSION['mapa'] = 'ginasio5';
    }

    public function mapa6() {
        require_once __DIR__ . "/../Views/mapas/mapa6.php";
        $_SESSION['mapa'] = 'mapa6';
    }

    public function ginasio6() {
        $_SESSION['ginasio6_visitado'] = true;
        require_once __DIR__ . "/../Views/mapas/ginasio6.php";
        $_SESSION['mapa'] = 'ginasio6';
    }

    public function mapa7() {
        require_once __DIR__ . "/../Views/mapas/mapa7.php";
        $_SESSION['mapa'] = 'mapa7';
    }

    public function ginasio7() {
        $_SESSION['ginasio7_visitado'] = true;
        require_once __DIR__ . "/../Views/mapas/ginasio7.php";
        $_SESSION['mapa'] = 'ginasio7';
    }
    
    public function mapa8() {
        require_once __DIR__ . "/../Views/mapas/mapa8.php";
        $_SESSION['mapa'] = 'mapa8';
    }

    public function ginasio8() {
        $_SESSION['ginasio8_visitado'] = true;
        require_once __DIR__ . "/../Views/mapas/ginasio8.php";
        $_SESSION['mapa'] = 'ginasio8';
    }
}