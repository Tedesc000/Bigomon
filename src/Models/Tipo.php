<?php
class Tipo {
    private $id_tipo;
    private $nome;
    private $res_d;
    private $res_q;
    private $frq_d;
    private $frq_q;
    private $imunidade;
    private $id_pokemon;
    private $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function getIdTipo(){
        return $this->id_tipo;
    }
    public function getNome(){
        return $this->nome;
    }
    public function getResD(){
        return $this->res_d;
    }
    public function getResQ(){
        return $this->res_q;
    }
    public function getFrqD(){
        return $this->frq_d;
    }
    public function getFrqQ(){
        return $this->frq_q;
    }
    public function getImunidade(){
        return $this->imunidade;
    }
    public function getIdPokemon(){
        return $this->id_pokemon;
    }

    public function setIdTipo($id_tipo){
        $this->id_tipo = $id_tipo;
    }
    public function setNome($nome){
        $this->nome = $nome;
    }
    public function setResD($res_d){
        $this->res_d = $res_d;
    }
    public function setResQ($res_q){
        $this->res_q = $res_q;
    }
    public function setFrqD($frq_d){
        $this->frq_d = $frq_d;
    }
    public function setFrqQ($frq_q){
        $this->frq_q = $frq_q;
    }
    public function setImunidade($imunidade){
        $this->imunidade = $imunidade;
    }
    public function setIdPokemon($id_pokemon){
        $this->id_pokemon = $id_pokemon;
    }
}
