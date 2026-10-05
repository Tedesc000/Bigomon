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

    //fazer os getters e setters
}