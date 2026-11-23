<?php
class LojaItem extends Modelo {
    private $id_loja;
    private $id_item;
    private $preco;

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

    public function getItensLoja($id_loja) {
        $sql = "SELECT i.id_item, i.nome, i.preco, i.descricao, i.id_categoria, li.id_loja 
                FROM loja_item li
                INNER JOIN item i ON li.id_item = i.id_item
                WHERE li.id_loja = :id_loja";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_loja', $id_loja, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
