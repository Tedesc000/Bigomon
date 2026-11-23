<?php
class LojaControlador {
    private $pdo;

    public function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    public function verLoja(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idLoja = $_SESSION['id_loja'] ?? 1;

        $loja = new Loja($this->pdo);
        $nomeLoja = $loja->nomeLoja($idLoja);

        $lojaItem = new LojaItem($this->pdo);
        $itensLoja = $lojaItem->getItensLoja($idLoja);

        // Ids dos itens que o treinador já tem (para marcar TMs já adquiridos)
        $idsPossuidos = [];
        $idTreinador = $_SESSION['usuario_logado']['id_treinador'] ?? null;
        if ($idTreinador) {
            $itensTreinador = (new TreinadorItem($this->pdo))->buscarItensTreinador($idTreinador);
            $idsPossuidos = array_map(fn($i) => (int) $i['id_item'], $itensTreinador);
        }

        require_once __DIR__ . '/../Views/loja/loja.php';
    }

    public function comprar(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false);

        $payload = [];
        $raw = file_get_contents('php://input');
        if (!empty($raw)) {
            $payload = json_decode($raw, true) ?: [];
        }

        $idItem = $payload['id_item'] ?? $_POST['id_item'] ?? null;
        $idTreinador = $_SESSION['usuario_logado']['id_treinador'] ?? null;

        if (!$idItem || !$idTreinador) {
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(400);
                echo json_encode(['ok' => false, 'erro' => 'Dados inválidos.']);
                exit;
            }
            header("Location: ?modulo=loja&acao=verLoja");
            exit;
        }

        $itemModel = new Item($this->pdo);
        $dadosItem = $itemModel->buscarPorId($idItem);

        if (!$dadosItem) {
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(404);
                echo json_encode(['ok' => false, 'erro' => 'Item não encontrado.']);
                exit;
            }
            header("Location: ?modulo=loja&acao=verLoja");
            exit;
        }

        // TM é reutilizável: não deixa comprar o mesmo duas vezes
        $treinadorItem = new TreinadorItem($this->pdo);
        $ehTM = ItemEfeito::ehTM((int) $idItem);
        if ($ehTM && $treinadorItem->itemExiste($idTreinador, $idItem)) {
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(400);
                echo json_encode(['ok' => false, 'erro' => 'Você já possui esse TM.']);
                exit;
            }
            header("Location: ?modulo=loja&acao=verLoja");
            exit;
        }

        $preco = (int)($dadosItem['preco'] ?? 0);
        $saldoAtual = (int)($_SESSION['usuario_logado']['dinheiro'] ?? 0);

        if ($saldoAtual < $preco) {
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(400);
                echo json_encode(['ok' => false, 'erro' => 'Saldo insuficiente.']);
                exit;
            }
            header("Location: ?modulo=loja&acao=verLoja");
            exit;
        }

        // Realiza a compra
        $novoSaldo = $saldoAtual - $preco;
        $treinador = new Treinador($this->pdo);
        $treinador->setIdTreinador($idTreinador);
        $treinador->setDinheiro($novoSaldo);
        $treinador->salvar();
        $treinador->recarregarSessao($idTreinador);

        $treinadorItem->adicionarItem($idTreinador, $idItem, 1);

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'ok' => true,
                'id_item' => (int) $idItem,
                'eh_tm' => $ehTM,
                'novoSaldo' => $novoSaldo,
                'novoSaldoFormatado' => number_format($novoSaldo, 2, ',', '.')
            ]);
            exit;
        }

        header("Location: ?modulo=loja&acao=verLoja");
        exit;
    }
}