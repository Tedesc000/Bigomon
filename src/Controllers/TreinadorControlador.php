<?php
class TreinadorControlador {
    private $pdo;

    public function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    public function home(){
        require_once __DIR__ . "/../Views/treinador/home.php";
    }

    public function cadastroLogin(){
        require_once __DIR__ . "/SeedControlador.php";
        $seedCtrl = new SeedControlador($this->pdo);

        // Se as triggers ainda não existirem, executa o arquivo database/triggers.sql
        if (!$seedCtrl->verificarTriggers()) {
            $seedCtrl->rodarTriggers();
        }

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM treinador");
        $total = $stmt->fetchColumn();
        // Se estiver vazia, chama as seeds automaticamente
        if ($total == 0) {
            $seedCtrl->rodar();
        }
        require_once __DIR__ . "/../Views/treinador/cadastroLogin.php";
    }

    public function login(){
        require_once __DIR__ . "/../Views/treinador/login.php";
    }

    public function pokedexAPI(){
        require_once __DIR__ . "/../Views/treinador/api.php";
    }

    public function executarAPI(){
        require_once __DIR__ . "/../../database/seeds/pokedexAPI.php";
    }

    public function logar(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';

            $novoTreinador = new Treinador($this->pdo);
            $sucesso = $novoTreinador->logar($email, $senha);

            if ($sucesso) {
                header("Location: ?modulo=treinador&acao=home");
                exit;
            } else {
                require_once __DIR__ . "/../Views/treinador/erroLogin.php";
            }
        }
    }
    
    public function logout(){
        $treinador = new Treinador($this->pdo);
        $treinador->logout();
        header("Location: ?modulo=treinador&acao=cadastroLogin");
        exit;
    }
    
    public function cadastrar(){
        require_once __DIR__ . "/../Views/treinador/cadastro.php";
    }

    public function salvar(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $isLogado = !empty($_SESSION['usuario_logado']['id_treinador']);
            $usuarioLogado = $_SESSION['usuario_logado'] ?? null;

            $email = trim($_POST['email'] ?? '');
            $treinador = new Treinador($this->pdo);

            // Verifica se o e-mail já está sendo utilizado por outro treinador
            if (!empty($email)) {
                $emailMudou = $isLogado ? ($email !== $usuarioLogado['email']) : true;
                if ($emailMudou && $treinador->emailExiste($email)) {
                    require_once __DIR__ . "/../Views/treinador/erroCadastro.php";
                    return;
                }
            }

            // Processa a foto de perfil:
            // Na edição, se o usuário não escolheu imagem nova, preserva a foto já existente
            $temArquivoNovo = !empty($_FILES['foto']['name']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE;
            if ($isLogado && !$temArquivoNovo) {
                $nomeFoto = $usuarioLogado['foto'] ?? 'fotoPerfilDefault.webp';
            } else {
                require_once __DIR__ . "/../Utils/upload.php";
                $upload = uploadFotoPerfil($_FILES['foto'] ?? null, false);

                if (!$upload['sucesso']) {
                    require_once __DIR__ . "/../Views/treinador/erroCadastro.php";
                    return;
                }
                $nomeFoto = $upload['nomeArquivo'];
            }

            // Senha: na edição, se ficar em branco, mantém a senha atual
            if ($isLogado && empty($_POST['senha'])) {
                $senha = $usuarioLogado['senha'];
            } else {
                $senha = $_POST['senha'] ?? '';
            }

            $treinador->setNome($_POST['nome'] ?? '');
            $treinador->setEmail($email);
            $treinador->setSenha($senha);
            $treinador->setSexo($_POST['sexo'] ?? '');
            $treinador->setFoto($nomeFoto);

            if ($isLogado) {
                // Busca os dados do banco para garantir que dinheiro e slots estejam sempre atualizados
                $dadosBanco = $treinador->buscarPorId($usuarioLogado['id_treinador']);

                // Na edição mantém id, dinheiro e slots atuais
                $treinador->setIdTreinador($usuarioLogado['id_treinador']);
                $treinador->setDinheiro($dadosBanco['dinheiro'] ?? $usuarioLogado['dinheiro'] ?? 0);
                $treinador->setSlot1($dadosBanco['slot1'] ?? $usuarioLogado['slot1'] ?? null);
                $treinador->setSlot2($dadosBanco['slot2'] ?? $usuarioLogado['slot2'] ?? null);
                $treinador->setSlot3($dadosBanco['slot3'] ?? $usuarioLogado['slot3'] ?? null);
                $treinador->setSlot4($dadosBanco['slot4'] ?? $usuarioLogado['slot4'] ?? null);
                $treinador->setSlot5($dadosBanco['slot5'] ?? $usuarioLogado['slot5'] ?? null);
                $treinador->setSlot6($dadosBanco['slot6'] ?? $usuarioLogado['slot6'] ?? null);

                if ($treinador->salvar()) {
                    // Atualiza a sessão com todos os dados novos (incluindo dinheiro e slots)
                    $_SESSION['usuario_logado']['id_treinador'] = $treinador->getIdTreinador();
                    $_SESSION['usuario_logado']['nome'] = $treinador->getNome();
                    $_SESSION['usuario_logado']['email'] = $treinador->getEmail();
                    $_SESSION['usuario_logado']['senha'] = $treinador->getSenha();
                    $_SESSION['usuario_logado']['sexo'] = $treinador->getSexo();
                    $_SESSION['usuario_logado']['foto'] = $treinador->getFoto();
                    $_SESSION['usuario_logado']['dinheiro'] = $treinador->getDinheiro();
                    $_SESSION['usuario_logado']['slot1'] = $treinador->getSlot1();
                    $_SESSION['usuario_logado']['slot2'] = $treinador->getSlot2();
                    $_SESSION['usuario_logado']['slot3'] = $treinador->getSlot3();
                    $_SESSION['usuario_logado']['slot4'] = $treinador->getSlot4();
                    $_SESSION['usuario_logado']['slot5'] = $treinador->getSlot5();
                    $_SESSION['usuario_logado']['slot6'] = $treinador->getSlot6();

                    $treinador->recarregarSessao($treinador->getIdTreinador());

                    header("Location: ?modulo=treinador&acao=home");
                    exit;
                } else {
                    require_once __DIR__ . "/../Views/treinador/erroCadastro.php";
                }
            } else {
                // valores iniciais do cadastro
                $treinador->setDinheiro(0);
                $treinador->setSlot1(null);
                $treinador->setSlot2(null);
                $treinador->setSlot3(null);
                $treinador->setSlot4(null);
                $treinador->setSlot5(null);
                $treinador->setSlot6(null);

                if ($treinador->salvar()){
                    $treinador->logar($email, $senha);
                    header("Location: ?modulo=treinador&acao=home");
                    exit;
                } else {
                    require_once __DIR__ . "/../Views/treinador/erroCadastro.php";
                }
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

                // Atualiza também na sessão ativa com todos os dados para refletir no jogo
                $_SESSION['usuario_logado']['slot1'] = $slot1;
                $treinador->recarregarSessao($idTreinador);
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

        if (!empty($_SESSION['usuario_logado']['id_treinador'])) {
            $treinador = new Treinador($this->pdo);
            $treinador->recarregarSessao($_SESSION['usuario_logado']['id_treinador']);
        }

        require_once __DIR__ . "/../Views/treinador/inicioLogado.php";
    }

    public function editar(){
        require_once __DIR__ . "/../Views/treinador/cadastro.php";
    }
    public function verPerfil(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['usuario_logado']['id_treinador'])) {
            $treinador = new Treinador($this->pdo);
            $treinador->recarregarSessao($_SESSION['usuario_logado']['id_treinador']);
        }

        require_once __DIR__ . "/../Views/treinador/verPerfil.php";
    }

    public function verLogs(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['logado']) || empty($_SESSION['usuario_logado']['id_treinador'])) {
            header("Location: ?modulo=treinador&acao=login");
            exit;
        }

        $idTreinador = $_SESSION['usuario_logado']['id_treinador'];
        $categoria = $_GET['categoria'] ?? 'todos';
        $periodo = $_GET['periodo'] ?? 'todos';

        // Garante que as views estejam instaladas no banco
        require_once __DIR__ . "/SeedControlador.php";
        $seedCtrl = new SeedControlador($this->pdo);
        if (!$seedCtrl->verificarViews()) {
            $seedCtrl->rodarViews();
        }

        $logModel = new Log($this->pdo);
        $logs = $logModel->buscarLogsView($idTreinador, $categoria, $periodo);

        require_once __DIR__ . "/../Views/treinador/verLogs.php";
    }
}