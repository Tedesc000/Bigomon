<?php

class BatalhaControlador
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function BatalhaMatinho()
    {
        $_SESSION['batalha'] = 'batalhaMatinho';
        require_once __DIR__ . '/../Views/batalha/batalhaMatinho.php';
    }
}