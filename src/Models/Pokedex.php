<?php
class Pokedex {
    private $id_pokedex;
    private $nome;
    private $hp;
    private $atk;
    private $def;
    private $spa;
    private $spd;
    private $spe;
    private $fase;
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
    public function getHp(){
        return $this->hp;
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
    public function getIdEvolucao(){
        return $this->id_evolucao;
    }

    public function setIdPokedex($id_pokedex){
        $this->id_pokedex = $id_pokedex;
    }
    public function setNome($nome){
        $this->nome = $nome;
    }
    public function setHp($hp){
        $this->hp = $hp;
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
    public function setIdEvolucao($id_evolucao){
        $this->id_evolucao = $id_evolucao;
    }
}
