<?php
class TreinadorControlador {
    private $pdo;

    public function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    public function inicio(){
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM treinador");
        $total = $stmt->fetchColumn();
        // Se estiver vazia, chama as seeds automaticamente
        if ($total == 0) {
            require_once __DIR__ . "/SeedControlador.php";
            $seedCtrl = new SeedControlador($this->pdo);
            $seedCtrl->rodar();
            return;
        }
        require_once __DIR__ . "/../Views/treinador/inicio.php";
    }

    public function cadastroLogin(){
        require_once __DIR__ . "/../Views/treinador/cadastroLogin.php";
    }

    public function login(){
        require_once __DIR__ . "/../Views/treinador/login.php";
    }
    
    public function logar(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';

            $novoTreinador = new Treinador($this->pdo);
            $novoTreinador->setEmail($email);
            $novoTreinador->setSenha($senha);
            $novoTreinador->logar($email, $senha);

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!empty($_SESSION['logado'])) {
                require_once __DIR__ . "/../Views/treinador/inicioLogado.php";
            } else {
                require_once __DIR__ . "/../Views/treinador/erroLogin.php";
            }
        }
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
                require_once __DIR__ . "/../Views/treinador/inicioLogado.php";
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