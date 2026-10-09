<?php
class Treinador {
    private $id_treinador;
    private $nome;
    private $email;
    private $senha;
    private $sexo;
    private $foto;
    private $dinheiro;
    private $slot1;
    private $slot2;
    private $slot3;
    private $slot4;
    private $slot5;
    private $slot6;
    private $pdo;

    //o private pdo e essa função vai em todos
    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }
    public function getIdTreinador(){
        return $this->id_treinador;
    }
    public function getNome(){
        return $this->nome;
    }
    public function getEmail(){
        return $this->email;
    }
    public function getSenha(){
        return $this->senha;
    }
    public function getSexo(){
        return $this->sexo;
    }
    public function getFoto(){
        return $this->foto;
    }
    public function getDinheiro(){
        return $this->dinheiro;
    }
    public function getSlot1(){
        return $this->slot1;
    }
    public function getSlot2(){
        return $this->slot2;
    }
    public function getSlot3(){
        return $this->slot3;
    }
    public function getSlot4(){
        return $this->slot4;
    }
    public function getSlot5(){
        return $this->slot5;
    }
    public function getSlot6(){
        return $this->slot6;
    }
    public function setIdTreinador($id_treinador){
        $this->id_treinador = $id_treinador; 
    }
    public function setNome($nome){
        $this->nome = $nome; 
    }
    public function setEmail($email){
        $this->email = $email; 
    }
    public function setSenha($senha){
        $this->senha = $senha; 
    }
    public function setSexo($sexo){
        $this->sexo = $sexo; 
    }
    public function setFoto($foto){
        $this->foto = $foto; 
    }
    public function setDinheiro($dinheiro){
        $this->dinheiro = $dinheiro; 
    }
    public function setSlot1($slot1){
        $this->slot1 = $slot1; 
    }
    public function setSlot2($slot2){
        $this->slot2 = $slot2; 
    }
    public function setSlot3($slot3){
        $this->slot3 = $slot3; 
    }
    public function setSlot4($slot4){
        $this->slot4 = $slot4; 
    }
    public function setSlot5($slot5){
        $this->slot5 = $slot5; 
    }
    public function setSlot6($slot6){
        $this->slot6 = $slot6; 
    }

}