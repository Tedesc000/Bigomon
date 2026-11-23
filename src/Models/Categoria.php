<?php
class Categoria extends Modelo {
    private $id_categoria;
    private $nome;
    private $descricao;

    public function getIdCategoria(){
        return $this->id_categoria;
    }
    public function getNome(){
        return $this->nome;
    }
    public function getDescricao(){
        return $this->descricao;
    }

    public function setIdCategoria($id_categoria){
        $this->id_categoria = $id_categoria;
    }
    public function setNome($nome){
        $this->nome = $nome;
    }
    public function setDescricao($descricao){
        $this->descricao = $descricao;
    }
}
