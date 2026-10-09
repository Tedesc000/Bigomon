<?php
class Pokedex {
    private $id_pokedex;
    private $nome;
    private $hp_max;
    private $atk;
    private $def;
    private $spa;
    private $spd;
    private $spe;
    private $fase;
    private $id_pokemon_modelo;
    private $sexo;
    private $id_evolucao;
    private $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function getIdPokedex(){
        return $this->id_pokedex;
    }
    public function getNome(){
        return $this->nome;
    }
    public function getHpMax(){
        return $this->hp_max;
    }
    public function getAtk(){
        return $this->atk;
    }
    public function getDef(){
        return $this->def;
    }
    public function getSpa(){
        return $this->spa;
    }
    public function getSpd(){
        return $this->spd;
    }
    public function getSpe(){
        return $this->spe;
    }
    public function getFase(){
        return $this->fase;
    }
    public function getIdPokemonModelo(){
        return $this->id_pokemon_modelo;
    }
    public function getSexo(){
        return $this->sexo;
    }
    public function getIdEvolucao(){
        return $this->id_evolucao;
    }

    public function setIdPokedex($id_pokedex){
        $this->id_pokedex = $id_pokedex;
    }
    public function setNome($nome){
        $this->nome = $nome;
    }
    public function setHpMax($hp_max){
        $this->hp_max = $hp_max;
    }
    public function setAtk($atk){
        $this->atk = $atk;
    }
    public function setDef($def){
        $this->def = $def;
    }
    public function setSpa($spa){
        $this->spa = $spa;
    }
    public function setSpd($spd){
        $this->spd = $spd;
    }
    public function setSpe($spe){
        $this->spe = $spe;
    }
    public function setFase($fase){
        $this->fase = $fase;
    }
    public function setIdPokemonModelo($id_pokemon_modelo){
        $this->id_pokemon_modelo = $id_pokemon_modelo;
    }
    public function setSexo($sexo){
        $this->sexo = $sexo;
    }
    public function setIdEvolucao($id_evolucao){
        $this->id_evolucao = $id_evolucao;
    }
}
