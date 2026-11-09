<?php

class LogControlador{
    private $pdo;

    public function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    public function criarLog($mensagem){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['usuario_logado']['id_treinador'])) {
            return false;
        }

        $data = date('Y-m-d H:i:s');

        $log = new Log($this->pdo);
        $log->setIdTreinador($_SESSION['usuario_logado']['id_treinador']);
        $log->setTexto($mensagem);
        $log->setData($data);

        return $log->salvar();
    }
}
