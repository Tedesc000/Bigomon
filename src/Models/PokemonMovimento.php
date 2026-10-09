<?php
class PokemonMovimento {
    private $id_pokemon;
    private $id_movimento;
    private $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function getIdPokemon(){
        return $this->id_pokemon;
    }
    public function getIdMovimento(){
        return $this->id_movimento;
    }

    public function setIdPokemon($id_pokemon){
        $this->id_pokemon = $id_pokemon;
    }
    public function setIdMovimento($id_movimento){
        $this->id_movimento = $id_movimento;
    }
}
