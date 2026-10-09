<?php
class Loja {
    private $id_loja;
    private $nome;
    private $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function getIdLoja(){
        return $this->id_loja;
    }
    public function getNome(){
        return $this->nome;
    }

    public function setIdLoja($id_loja){
        $this->id_loja = $id_loja;
    }
    public function setNome($nome){
        $this->nome = $nome;
    }
}
