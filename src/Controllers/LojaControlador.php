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

        $idItem = $_POST['id_item'] ?? null;
        $idTreinador = $_SESSION['usuario_logado']['id_treinador'] ?? null;

        if (!$idItem || !$idTreinador) {
            header("Location: ?modulo=loja&acao=verLoja");
            exit;
        }

        $itemModel = new Item($this->pdo);
        $dadosItem = $itemModel->buscarPorId($idItem);

        if (!$dadosItem) {
            header("Location: ?modulo=loja&acao=verLoja");
            exit;
        }

        // TM é reutilizável: não deixa comprar o mesmo duas vezes
        $treinadorItem = new TreinadorItem($this->pdo);
        if (ItemEfeito::ehTM((int) $idItem) && $treinadorItem->itemExiste($idTreinador, $idItem)) {
            header("Location: ?modulo=loja&acao=verLoja");
            exit;
        }

        $preco = (int)($dadosItem['preco'] ?? 0);
        $saldoAtual = (int)($_SESSION['usuario_logado']['dinheiro'] ?? 0);

        // Verifica se o treinador tem saldo suficiente
        if ($saldoAtual >= $preco) {
            // atualiza o saldo usando o Treinador
            $treinador = new Treinador($this->pdo);
            $treinador->setIdTreinador($idTreinador);
            $treinador->setDinheiro($saldoAtual - $preco);
            $treinador->salvar();
            $treinador->recarregarSessao($idTreinador);

            // adiciona o item ao inventário
            $treinadorItem->adicionarItem($idTreinador, $idItem, 1);
        }

        header("Location: ?modulo=loja&acao=verLoja");
        exit;
    }
}