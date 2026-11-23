<?php
class Item extends Modelo {
    private $id_item;
    private $nome;
    private $descricao;
    private $id_categoria;
    private $preco;

    public function getIdItem(){
        return $this->id_item;
    }
    public function getNome(){
        return $this->nome;
    }
    public function getDescricao(){
        return $this->descricao;
    }
    public function getIdCategoria(){
        return $this->id_categoria;
    }
    public function getPreco(){
        return $this->preco;
    }

    public function setIdItem($id_item){
        $this->id_item = $id_item;
    }
    public function setNome($nome){
        $this->nome = $nome;
    }
    public function setDescricao($descricao){
        $this->descricao = $descricao;
    }
    public function setIdCategoria($id_categoria){
        $this->id_categoria = $id_categoria;
    }
    public function setPreco($preco){
        $this->preco = $preco;
    }

    public function buscarPorId($id_item){
        $stmt = $this->pdo->prepare("SELECT * FROM item WHERE id_item = :id_item");
        $stmt->execute(['id_item' => $id_item]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
