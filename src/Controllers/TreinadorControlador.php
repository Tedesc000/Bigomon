<?php
class TreinadorControlador {
    private $pdo;

    public function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    public function inicio(){
        require_once __DIR__ . "/../Views/treinador/inicio.php";
    }

    public function cadastroLogin(){
        require_once __DIR__ . "/../Views/treinador/cadastroLogin.php";
    }

    public function login(){
        require_once __DIR__ . "/../Views/treinador/login.php";
    }
    
    public function logar(){
        
    }
    
    public function logout(){
        
    }
    
    public function cadastrar(){
        require_once __DIR__ . "/../Views/treinador/cadastro.php";
    }

    public function salvar(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $novoTreinador = new Treinador($this->pdo);
            $novoTreinador->setNome($_POST['nome'] ?? '');
            $novoTreinador->setEmail($_POST['email'] ?? '');
            $novoTreinador->setSenha($_POST['senha'] ?? '');
            $novoTreinador->setSexo($_POST['sexo'] ?? '');
            $novoTreinador->setFoto($_POST['foto'] ?? '');
            $novoTreinador->setDinheiro(0);
            $novoTreinador->setSlot1($_POST['slot1'] ?? null);
            $novoTreinador->setSlot2(null);
            $novoTreinador->setSlot3(null);
            $novoTreinador->setSlot4(null);
            $novoTreinador->setSlot5(null);
            $novoTreinador->setSlot6(null);
            if($novoTreinador->salvar()){
                $novoTreinador->logar($_POST['email'], $_POST['senha']);
                header("Location: index.php");
            }else{
                require_once __DIR__ . "/../Views/treinador/erroCadastro.php";
            }
        }
    }

    public function roleta(){
        require_once __DIR__ . "/../Views/treinador/roleta.php";
    }

    public function editar(){
        
    }
    
    public function atualizar(){
        
    }
    
    public function deletar(){
        
    }
}