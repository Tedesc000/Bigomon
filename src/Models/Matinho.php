<?php
class Matinho {
    private $id_matinho;
    private $nome;
    private $pokemons;
    private $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function getIdMatinho(){
        return $this->id_matinho;
    }
    public function getNome(){
        return $this->nome;
    }
    public function getPokemons(){
        return $this->pokemons;
    }

    public function setIdMatinho($id_matinho){
        $this->id_matinho = $id_matinho;
    }
    public function setNome($nome){
        $this->nome = $nome;
    }
    public function setPokemons($pokemons){
        $this->pokemons = $pokemons;
    }
}
