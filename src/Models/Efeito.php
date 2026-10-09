<?php
class Efeito {
    private $id_efeito;
    private $nome;
    private $chance;
    private $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function getIdEfeito(){
        return $this->id_efeito;
    }
    public function getNome(){
        return $this->nome;
    }
    public function getChance(){
        return $this->chance;
    }

    public function setIdEfeito($id_efeito){
        $this->id_efeito = $id_efeito;
    }
    public function setNome($nome){
        $this->nome = $nome;
    }
    public function setChance($chance){
        $this->chance = $chance;
    }
}
