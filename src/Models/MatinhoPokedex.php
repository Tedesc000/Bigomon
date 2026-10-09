<?php
class MatinhoPokedex {
    private $id_matinho;
    private $id_pokedex;
    private $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function getIdMatinho(){
        return $this->id_matinho;
    }
    public function getIdPokedex(){
        return $this->id_pokedex;
    }

    public function setIdMatinho($id_matinho){
        $this->id_matinho = $id_matinho;
    }
    public function setIdPokedex($id_pokedex){
        $this->id_pokedex = $id_pokedex;
    }
}
