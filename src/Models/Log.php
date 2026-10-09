<?php
class Log {
    private $id_log;
    private $id_treinador;
    private $texto;
    private $data;
    private $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function getIdLog(){
        return $this->id_log;
    }
    public function getIdTreinador(){
        return $this->id_treinador;
    }
    public function getTexto(){
        return $this->texto;
    }
    public function getData(){
        return $this->data;
    }

    public function setIdLog($id_log){
        $this->id_log = $id_log;
    }
    public function setIdTreinador($id_treinador){
        $this->id_treinador = $id_treinador;
    }
    public function setTexto($texto){
        $this->texto = $texto;
    }
    public function setData($data){
        $this->data = $data;
    }
}
