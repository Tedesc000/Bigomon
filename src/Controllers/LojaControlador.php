<?php
class LojaControlador {
    private $pdo;

    public function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    public function verLoja(){
        require_once __DIR__ . '/../Views/loja/loja.php';
        $loja = new Loja($this->pdo);
        $nomeLoja = $loja->nomeLoja($_SESSION['id_loja']);
        $lojaItem = new LojaItem($this->pdo);
        $itensLoja = $lojaItem->getItensLoja($_SESSION['id_loja']);
        return [
            'nomeLoja' => $nomeLoja,
            'itensLoja' => $itensLoja
        ];
    }
}