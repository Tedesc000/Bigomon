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
            $treinadorItem = new TreinadorItem($this->pdo);
            $treinadorItem->adicionarItem($idTreinador, $idItem, 1);
        }

        header("Location: ?modulo=loja&acao=verLoja");
        exit;
    }
}