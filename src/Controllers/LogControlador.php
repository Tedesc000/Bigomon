<?php

class LogControlador{
    private $pdo;

    public function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    public function criarLog($mensagem){
        $dataHora = date('Y-m-d H:i:s');
        $registro = "[$dataHora] $mensagem" . PHP_EOL;

        $log = new Log($this->pdo);
    }
}
