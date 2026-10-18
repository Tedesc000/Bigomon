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

    public function salvar(){
        if ($this->id_treinador){
            $sql = "UPDATE treinador 
                    SET nome = :nome, 
                    email = :email, 
                    senha = :senha, 
                    sexo = :sexo, 
                    foto = :foto, 
                    dinheiro = :dinheiro, 
                    slot1 = :slot1, 
                    slot2 = :slot2, 
                    slot3 = :slot3, 
                    slot4 = :slot4, 
                    slot5 = :slot5, 
                    slot6 = :slot6 
                    WHERE id_treinador = :id_treinador";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'nome' => $this->nome,
                'email' => $this->email,
                'senha' => $this->senha,
                'sexo' => $this->sexo,
                'foto' => $this->foto,
                'dinheiro' => $this->dinheiro,
                'slot1' => $this->slot1,
                'slot2' => $this->slot2,
                'slot3' => $this->slot3,
                'slot4' => $this->slot4,
                'slot5' => $this->slot5,
                'slot6' => $this->slot6,
                'id_treinador' => $this->id_treinador
            ]);
        }else{
            try {
                $sql = "INSERT INTO treinador (nome, email, senha, sexo, foto, dinheiro, slot1, slot2, slot3, slot4, slot5, slot6) VALUES (:nome, :email, :senha, :sexo, :foto, :dinheiro, :slot1, :slot2, :slot3, :slot4, :slot5, :slot6)";
                $stmt = $this->pdo->prepare($sql);
                $ok = $stmt->execute([
                    'nome' => $this->nome,
                    'email' => $this->email,
                    'senha' => $this->senha,
                    'sexo' => $this->sexo,
                    'foto' => $this->foto,
                    'dinheiro' => $this->dinheiro,
                    'slot1' => $this->slot1,
                    'slot2' => $this->slot2,
                    'slot3' => $this->slot3,
                    'slot4' => $this->slot4,
                    'slot5' => $this->slot5,
                    'slot6' => $this->slot6
                ]);
                if ($ok){
                    $this->id_treinador = $this->pdo->lastInsertId();
                }
                return $ok;
            } catch (PDOException $e) {
                return false;
            }
        }
    }

    public function emailExiste($email){
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM treinador WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    public function deletar(){
        if (!$this->id_treinador){
            return false;
        }
        $stmt = $this->pdo->prepare("DELETE FROM treinador WHERE id_treinador = :id_treinador");
        return $stmt->execute([
            'id_treinador' => $this->id_treinador
        ]);
    }

    public function atualizarSlot1($id_treinador, $slot1){
        $stmt = $this->pdo->prepare("UPDATE treinador SET slot1 = :slot1 WHERE id_treinador = :id_treinador");
        return $stmt->execute([
            'slot1' => $slot1,
            'id_treinador' => $id_treinador
        ]);
    }

    public function logar($email, $senha){
        $stmt = $this->pdo->prepare("SELECT * FROM treinador WHERE email = :email AND senha = :senha");
        $stmt->execute([
            'email' => $email,
            'senha' => $senha
        ]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($usuario){
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['logado'] = true;
            $_SESSION['usuario_logado'] = $usuario;
            return true;
        }
        return false;
    }

    public function logout(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();
    }
}