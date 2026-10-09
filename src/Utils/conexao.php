<?php
function getConexao(){
    $host = "localhost";
    $dbname = "bigomon";
    $user = "root";
    $pass = "";

    try{
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOexception $e){
        die("Erro de conexão: " . $e->getMessage());
    }
}