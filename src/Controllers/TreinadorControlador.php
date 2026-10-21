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
            $sucesso = $novoTreinador->logar($email, $senha);

            if ($sucesso) {
                header("Location: ?modulo=treinador&acao=roleta");
                exit;
            } else {
                require_once __DIR__ . "/../Views/treinador/erroLogin.php";
            }
        }
    }
    
    public function logout(){
        $treinador = new Treinador($this->pdo);
        $treinador->logout();
        header("Location: ?modulo=treinador&acao=inicio");
        exit;
    }
    
    public function cadastrar(){
        require_once __DIR__ . "/../Views/treinador/cadastro.php";
    }

    public function salvar(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');

            $novoTreinador = new Treinador($this->pdo);

            // Verifica se o e-mail já está sendo utilizado por outro treinador
            if (!empty($email) && $novoTreinador->emailExiste($email)) {
                require_once __DIR__ . "/../Views/treinador/erroCadastro.php";
                return;
            }

            // Processa o upload da foto de perfil
            require_once __DIR__ . "/../Utils/upload.php";
            $upload = uploadFotoPerfil($_FILES['foto'] ?? null, false);

            if (!$upload['sucesso']) {
                require_once __DIR__ . "/../Views/treinador/erroCadastro.php";
                return;
            }

            $novoTreinador->setNome($_POST['nome'] ?? '');
            $novoTreinador->setEmail($email);
            $novoTreinador->setSenha($_POST['senha'] ?? '');
            $novoTreinador->setSexo($_POST['sexo'] ?? '');
            $novoTreinador->setFoto($upload['nomeArquivo']);
            $novoTreinador->setDinheiro(0);
            $novoTreinador->setSlot1(null);
            $novoTreinador->setSlot2(null);
            $novoTreinador->setSlot3(null);
            $novoTreinador->setSlot4(null);
            $novoTreinador->setSlot5(null);
            $novoTreinador->setSlot6(null);

            if ($novoTreinador->salvar()){
                $novoTreinador->logar($email, $_POST['senha'] ?? '');
                header("Location: ?modulo=treinador&acao=roleta");
                exit;
            } else {
                require_once __DIR__ . "/../Views/treinador/erroCadastro.php";
            }
        }
    }

    public function roleta(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['logado'])) {
            header("Location: ?modulo=treinador&acao=login");
            exit;
        }

        require_once __DIR__ . "/../Views/treinador/roleta.php";
    }

    public function salvarRoleta(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['logado'])) {
            header("Location: ?modulo=treinador&acao=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idTreinador = $_SESSION['usuario_logado']['id_treinador'] ?? null;
            $slot1 = $_POST['slot1'] ?? null;

            if ($idTreinador && $slot1) {
                $treinador = new Treinador($this->pdo);
                $treinador->atualizarSlot1($idTreinador, $slot1);

                // Atualiza também na sessão ativa para refletir no jogo
                $_SESSION['usuario_logado']['slot1'] = $slot1;
            }

            header("Location: ?modulo=treinador&acao=inicioLogado");
            exit;
        }

        header("Location: ?modulo=treinador&acao=roleta");
        exit;
    }

    public function inicioLogado(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['logado'])) {
            header("Location: ?modulo=treinador&acao=login");
            exit;
        }

        require_once __DIR__ . "/../Views/treinador/inicioLogado.php";
    }

    public function editar(){
        
    }
    
    public function atualizar(){
        
    }
    
    public function deletar(){
        
    }
}