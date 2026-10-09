<?php
class LojaItem {
    private $id_loja;
    private $id_item;
    private $preco;
    private $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function getIdLoja(){
        return $this->id_loja;
    }
    public function getIdItem(){
        return $this->id_item;
    }
    public function getPreco(){
        return $this->preco;
    }

    public function setIdLoja($id_loja){
        $this->id_loja = $id_loja;
    }
    public function setIdItem($id_item){
        $this->id_item = $id_item;
    }
    public function setPreco($preco){
        $this->preco = $preco;
    }
}
