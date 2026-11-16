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

    public function nomeLoja($id_loja) {
        $stmt = $this->pdo->prepare("SELECT nome FROM loja WHERE id_loja = :id_loja");
        $stmt->bindParam(':id_loja', $id_loja, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['nome'] : null;
    }
}
