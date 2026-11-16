<?php
class LojaControlador {
    private $pdo;

    public function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    public function verLoja(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idLoja = $_SESSION['id_loja'] ?? 1;

        $loja = new Loja($this->pdo);
        $nomeLoja = $loja->nomeLoja($idLoja);

        $lojaItem = new LojaItem($this->pdo);
        $itensLoja = $lojaItem->getItensLoja($idLoja);

        require_once __DIR__ . '/../Views/loja/loja.php';
    }
}