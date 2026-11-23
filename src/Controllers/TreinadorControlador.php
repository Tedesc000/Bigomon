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

            try {
                $novoTreinador = new Treinador($this->pdo);
                $sucesso = $novoTreinador->logar($email, $senha);

                if ($sucesso) {
                    header("Location: ?modulo=treinador&acao=home");
                    exit;
                } else {
                    $tituloErro = "ERRO NO LOGIN";
                    $mensagemErro = "E-mail ou senha incorretos!";
                    require_once __DIR__ . "/../Views/treinador/erroLogin.php";
                }
            } catch (TreinadorException $e) {
                $tituloErro = "ERRO NO LOGIN";
                $mensagemErro = $e->getMessage();
                require_once __DIR__ . "/../Views/treinador/erroLogin.php";
            } catch (Throwable $e) {
                $tituloErro = "ERRO INESPERADO";
                $mensagemErro = "Ocorreu um erro no servidor. Tente novamente.";
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

            try {
                $nome = trim($_POST['nome'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $sexo = trim($_POST['sexo'] ?? '');
                $treinador = new Treinador($this->pdo);

                if (empty($nome)) {
                    throw new TreinadorException("O nome do treinador é obrigatório.");
                }

                if (empty($email)) {
                    throw new TreinadorException("O e-mail é obrigatório.");
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new TreinadorException("O e-mail informado não possui um formato válido.");
                }

                // Verifica se o e-mail já está sendo utilizado por outro treinador
                $emailMudou = $isLogado ? ($email !== $usuarioLogado['email']) : true;
                if ($emailMudou && $treinador->emailExiste($email)) {
                    throw new TreinadorException("Este e-mail já está em uso por outro treinador.");
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
                        throw new TreinadorException($upload['erro'] ?? "Erro no upload da foto de perfil.");
                    }
                    $nomeFoto = $upload['nomeArquivo'];
                }

                // Senha: na edição, se ficar em branco, mantém a senha atual
                if ($isLogado && empty($_POST['senha'])) {
                    $senha = $usuarioLogado['senha'];
                } else {
                    $senha = $_POST['senha'] ?? '';
                    if (empty($senha)) {
                        throw new TreinadorException("A senha é obrigatória.");
                    }
                    if (strlen($senha) < 4) {
                        throw new TreinadorException("A senha deve ter pelo menos 4 caracteres.");
                    }
                }

                $treinador->setNome($nome);
                $treinador->setEmail($email);
                $treinador->setSenha($senha);
                $treinador->setSexo($sexo);
                $treinador->setFoto($nomeFoto);

                if ($isLogado) {
                    // Na edição, basta setar o id_treinador: o salvar() atualiza apenas os campos alterados
                    $treinador->setIdTreinador($usuarioLogado['id_treinador']);

                    if ($treinador->salvar()) {
                        $treinador->recarregarSessao($treinador->getIdTreinador());
                        header("Location: ?modulo=treinador&acao=verPerfil");
                        exit;
                    } else {
                        throw new TreinadorException("Não foi possível atualizar o perfil.");
                    }
                } else {
                    // valores iniciais do cadastro
                    $treinador->setDinheiro(5000);
                    $treinador->setSlot1(null);
                    $treinador->setSlot2(null);
                    $treinador->setSlot3(null);
                    $treinador->setSlot4(null);
                    $treinador->setSlot5(null);
                    $treinador->setSlot6(null);

                    if ($treinador->salvar()) {
                        $treinador->logar($email, $senha);
                        header("Location: ?modulo=treinador&acao=home");
                        exit;
                    } else {
                        throw new TreinadorException("Não foi possível concluir o cadastro.");
                    }
                }
            } catch (TreinadorException $e) {
                $tituloErro = $isLogado ? "ERRO AO ATUALIZAR" : "ERRO NO CADASTRO";
                $mensagemErro = $e->getMessage();
                require_once __DIR__ . "/../Views/treinador/erroCadastro.php";
            } catch (Throwable $e) {
                $tituloErro = "ERRO INESPERADO";
                $mensagemErro = "Ocorreu um erro no servidor. Tente novamente.";
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

        $idTreinador = $_SESSION['usuario_logado']['id_treinador'] ?? null;
        $pokemonInicial = null;
        if ($idTreinador !== null) {
            $stmt = $this->pdo->prepare(
                "SELECT pokemon.nome, pokemon.id_pokedex
                FROM pokemon
                WHERE pokemon.id_treinador = :id_treinador
                ORDER BY pokemon.id_pokemon
                LIMIT 1"
            );
            $stmt->execute(['id_treinador' => $idTreinador]);
            $pokemonInicial = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        require_once __DIR__ . "/../Views/treinador/roleta.php";
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

    public function capturarPokemon(){
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
            return;
        }

        $idPokemonRecebido = $_POST['id_pokemon_capturado'] ?? null;
        $idPokemon = is_scalar($idPokemonRecebido)
            ? filter_var($idPokemonRecebido, FILTER_VALIDATE_INT)
            : false;

        if ($idPokemon === false || $idPokemon < 1) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID do Pokémon inválido.']);
            return;
        }

        $idTreinador = $_SESSION['usuario_logado']['id_treinador'] ?? null;
        if ($idTreinador === null) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Treinador não autenticado.']);
            return;
        }

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                "SELECT id_treinador
                FROM treinador
                WHERE id_treinador = :id_treinador
                FOR UPDATE"
            );
            $stmt->execute(['id_treinador' => $idTreinador]);
            if (!$stmt->fetchColumn()) {
                $this->pdo->rollBack();
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Treinador não encontrado.']);
                return;
            }

            $stmt = $this->pdo->prepare(
                "SELECT id_pokemon
                FROM pokemon
                WHERE id_treinador = :id_treinador
                LIMIT 1"
            );
            $stmt->execute(['id_treinador' => $idTreinador]);
            if ($stmt->fetchColumn()) {
                $this->pdo->rollBack();
                http_response_code(409);
                echo json_encode([
                    'success' => false,
                    'already_captured' => true,
                    'message' => 'Você já ganhou um Pokémon nesta roleta.'
                ]);
                return;
            }

            $treinador = new Treinador($this->pdo);
            if (!$treinador->carregarPorId($idTreinador)) {
                $this->pdo->rollBack();
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Treinador não encontrado.']);
                return;
            }

            $slotDisponivel = null;
            for ($slot = 1; $slot <= 6; $slot++) {
                $getter = 'getSlot' . $slot;
                if (empty($treinador->$getter())) {
                    $slotDisponivel = $slot;
                    break;
                }
            }

            $pokemon = new Pokemon($this->pdo);
            $pokemon->setIdPokedex($idPokemon);
            if (!$pokemon->setDadosPokedex($idTreinador)) {
                $this->pdo->rollBack();
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Pokémon não encontrado na Pokédex.']);
                return;
            }

            // Sorteia 4 movimentos aleatórios da tabela movimento para o Pokémon obtido na roleta
            $stmtMov = $this->pdo->query("SELECT id_movimento FROM movimento ORDER BY RAND() LIMIT 4");
            $movimentosSorteados = $stmtMov->fetchAll(PDO::FETCH_COLUMN);
            if (!empty($movimentosSorteados)) {
                $stmtInsertMov = $this->pdo->prepare(
                    "INSERT INTO pokemon_movimento (id_pokemon, id_movimento) VALUES (:id_pokemon, :id_movimento)"
                );
                foreach ($movimentosSorteados as $idMovimento) {
                    $stmtInsertMov->execute([
                        'id_pokemon' => $pokemon->getIdPokemon(),
                        'id_movimento' => $idMovimento
                    ]);
                }
            }

            if ($slotDisponivel !== null) {
                $setter = 'setSlot' . $slotDisponivel;
                $treinador->$setter((int) $pokemon->getIdPokemon());
                if (!$treinador->salvar()) {
                    $this->pdo->rollBack();
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Não foi possível atualizar o time.']);
                    return;
                }
            }

            $this->pdo->commit();
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }

        $treinador->recarregarSessao($idTreinador);
        echo json_encode([
            'success' => true,
            'id_pokemon' => $pokemon->getIdPokemon(),
            'id_pokedex' => $idPokemon,
            'slot' => $slotDisponivel
        ]);
    }

    public function buscarPokemonsDosSlots(?array $slotIds = null): array {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idTreinador = $_SESSION['usuario_logado']['id_treinador'] ?? null;
        require_once __DIR__ . "/../Models/Treinador.php";
        $treinador = new Treinador($this->pdo);

        if ($idTreinador) {
            $treinador->recarregarSessao($idTreinador);
        }

        // Se não foi passado array, obtém direto dos slots gravados na sessão
        if ($slotIds === null) {
            $usuario = $_SESSION['usuario_logado'] ?? [];
            $slotIds = [
                'slot1' => $usuario['slot1'] ?? null,
                'slot2' => $usuario['slot2'] ?? null,
                'slot3' => $usuario['slot3'] ?? null,
                'slot4' => $usuario['slot4'] ?? null,
                'slot5' => $usuario['slot5'] ?? null,
                'slot6' => $usuario['slot6'] ?? null,
            ];
        }

        return $treinador->buscarPokemonsPorIds($slotIds, $idTreinador);
    }
}