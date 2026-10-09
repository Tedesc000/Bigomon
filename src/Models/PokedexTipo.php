<?php
class PokedexTipo {
    private $id_pokemon_modelo;
    private $id_tipo;
    private $id_pokedex;
    private $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function getIdPokemonModelo(){
        return $this->id_pokemon_modelo;
    }
    public function getIdTipo(){
        return $this->id_tipo;
    }
    public function getIdPokedex(){
        return $this->id_pokedex;
    }

    public function setIdPokemonModelo($id_pokemon_modelo){
        $this->id_pokemon_modelo = $id_pokemon_modelo;
    }
    public function setIdTipo($id_tipo){
        $this->id_tipo = $id_tipo;
    }
    public function setIdPokedex($id_pokedex){
        $this->id_pokedex = $id_pokedex;
    }
}
