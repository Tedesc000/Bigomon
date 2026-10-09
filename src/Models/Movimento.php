<?php
class Movimento {
    private $id_movimento;
    private $id_tipo;
    private $id_efeito;
    private $nome;
    private $precisao;
    private $prioridade;
    private $poder;
    private $tipo;
    private $efeito;
    private $chance;
    private $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function getIdMovimento(){
        return $this->id_movimento;
    }
    public function getIdTipo(){
        return $this->id_tipo;
    }
    public function getIdEfeito(){
        return $this->id_efeito;
    }
    public function getNome(){
        return $this->nome;
    }
    public function getPrecisao(){
        return $this->precisao;
    }
    public function getPrioridade(){
        return $this->prioridade;
    }
    public function getPoder(){
        return $this->poder;
    }
    public function getTipo(){
        return $this->tipo;
    }
    public function getEfeito(){
        return $this->efeito;
    }
    public function getChance(){
        return $this->chance;
    }

    public function setIdMovimento($id_movimento){
        $this->id_movimento = $id_movimento;
    }
    public function setIdTipo($id_tipo){
        $this->id_tipo = $id_tipo;
    }
    public function setIdEfeito($id_efeito){
        $this->id_efeito = $id_efeito;
    }
    public function setNome($nome){
        $this->nome = $nome;
    }
    public function setPrecisao($precisao){
        $this->precisao = $precisao;
    }
    public function setPrioridade($prioridade){
        $this->prioridade = $prioridade;
    }
    public function setPoder($poder){
        $this->poder = $poder;
    }
    public function setTipo($tipo){
        $this->tipo = $tipo;
    }
    public function setEfeito($efeito){
        $this->efeito = $efeito;
    }
    public function setChance($chance){
        $this->chance = $chance;
    }
}
