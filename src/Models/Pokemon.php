<?php
class Pokemon {
    private $id_pokemon;
    private $id_treinador;
    private $id_pokedex;
    private $nome;
    private $hp_atual;
    private $sexo;
    private $status;
    private $pdo;
    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function getIdPokemon(){
        return $this->id_pokemon;
    }
    public function getIdTreinador(){
        return $this->id_treinador;
    }
    public function getIdPokedex(){
        return $this->id_pokedex;
    }
    public function getNome(){
        return $this->nome;
    }
    public function getHpAtual(){
        return $this->hp_atual;
    }
    public function getSexo(){
        return $this->sexo;
    }
    public function getStatus(){
        return $this->status;
    }

    public function setIdPokemon($id_pokemon){
        $this->id_pokemon = $id_pokemon;
    }
    public function setIdTreinador($id_treinador){
        $this->id_treinador = $id_treinador;
    }
    public function setIdPokedex($id_pokedex){
        $this->id_pokedex = $id_pokedex;
    }
    public function setNome($nome){
        $this->nome = $nome;
    }
    public function setHpAtual($hp_atual){
        $this->hp_atual = $hp_atual;
    }
    public function setSexo($sexo){
        $this->sexo = $sexo;
    }
    public function setStatus($status){
        $this->status = $status;
    }

}