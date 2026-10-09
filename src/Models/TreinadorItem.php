<?php
class TreinadorItem {
    private $id_inventario;
    private $id_treinador;
    private $id_item;
    private $quantidade;
    private $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function getIdInventario(){
        return $this->id_inventario;
    }
    public function getIdTreinador(){
        return $this->id_treinador;
    }
    public function getIdItem(){
        return $this->id_item;
    }
    public function getQuantidade(){
        return $this->quantidade;
    }

    public function setIdInventario($id_inventario){
        $this->id_inventario = $id_inventario;
    }
    public function setIdTreinador($id_treinador){
        $this->id_treinador = $id_treinador;
    }
    public function setIdItem($id_item){
        $this->id_item = $id_item;
    }
    public function setQuantidade($quantidade){
        $this->quantidade = $quantidade;
    }
}
