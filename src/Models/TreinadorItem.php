<?php
class TreinadorItem {
    private $id_treinador;
    private $id_item;
    private $quantidade;
    private $pdo;
    private $camposAlterados = [];

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function getIdTreinador(){
        return $this->id_treinador;
    }
    public function getIdItem(){
        return $this->id_item;
    }
    public function getQuantidade(){
        return $this->quantidade;
    }

    public function setIdTreinador($id_treinador){
        $this->id_treinador = $id_treinador;
    }
    public function setIdItem($id_item){
        $this->id_item = $id_item;
    }
    public function setQuantidade($quantidade){
        $this->quantidade = $quantidade;
        $this->camposAlterados['quantidade'] = $quantidade;
    }

    public function itemExiste($id_treinador, $id_item){
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM treinador_item WHERE id_treinador = :id_treinador AND id_item = :id_item");
        $stmt->execute([
            'id_treinador' => $id_treinador,
            'id_item' => $id_item
        ]);
        return $stmt->fetchColumn() > 0;
    }

    public function buscarPorId($id_treinador, $id_item){
        $stmt = $this->pdo->prepare("SELECT * FROM treinador_item WHERE id_treinador = :id_treinador AND id_item = :id_item");
        $stmt->execute([
            'id_treinador' => $id_treinador,
            'id_item' => $id_item
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function carregarPorId($id_treinador, $id_item){
        $dados = $this->buscarPorId($id_treinador, $id_item);
        if ($dados){
            $this->id_treinador = $dados['id_treinador'] ?? null;
            $this->id_item = $dados['id_item'] ?? null;
            $this->quantidade = $dados['quantidade'] ?? 0;
            $this->camposAlterados = [];
            return true;
        }
        return false;
    }

    public function salvar($id_item = null){
        if ($id_item !== null) {
            $this->id_item = $id_item;
        }

        if (!$this->id_treinador || !$this->id_item) {
            return false;
        }

        // Se o registro já existe no banco, executa UPDATE apenas nos campos alterados
        if ($this->itemExiste($this->id_treinador, $this->id_item)){
            if (empty($this->camposAlterados)){
                return true;
            }

            $setClauses = [];
            $params = [
                'id_treinador' => $this->id_treinador,
                'id_item' => $this->id_item
            ];

            foreach ($this->camposAlterados as $campo => $valor) {
                $setClauses[] = "$campo = :$campo";
                $params[$campo] = $valor;
            }

            try {
                $sql = "UPDATE treinador_item SET " . implode(', ', $setClauses) . " WHERE id_treinador = :id_treinador AND id_item = :id_item";
                $stmt = $this->pdo->prepare($sql);
                $ok = $stmt->execute($params);

                if ($ok) {
                    $this->camposAlterados = [];
                }

                return $ok;
            } catch (PDOException $e) {
                return false;
            }
        } else {
            // Se não existe, executa INSERT (novo item no inventário)
            try {
                $sql = "INSERT INTO treinador_item (id_treinador, id_item, quantidade) VALUES (:id_treinador, :id_item, :quantidade)";
                $stmt = $this->pdo->prepare($sql);
                $ok = $stmt->execute([
                    'id_treinador' => $this->id_treinador,
                    'id_item' => $this->id_item,
                    'quantidade' => $this->quantidade ?? 1
                ]);

                if ($ok) {
                    $this->camposAlterados = [];
                }

                return $ok;
            } catch (PDOException $e) {
                return false;
            }
        }
    }

    public function deletar(){
        if (!$this->id_treinador || !$this->id_item){
            return false;
        }
        $stmt = $this->pdo->prepare("DELETE FROM treinador_item WHERE id_treinador = :id_treinador AND id_item = :id_item");
        return $stmt->execute([
            'id_treinador' => $this->id_treinador,
            'id_item' => $this->id_item
        ]);
    }

    public function adicionarItem($id_treinador, $id_item, $quantidade = 1){
        if ($this->carregarPorId($id_treinador, $id_item)){
            $this->setQuantidade($this->quantidade + $quantidade);
        } else {
            $this->setIdTreinador($id_treinador);
            $this->setIdItem($id_item);
            $this->setQuantidade($quantidade);
        }
        return $this->salvar();
    }

    public function removerItem($id_treinador, $id_item, $quantidade = 1){
        if ($this->carregarPorId($id_treinador, $id_item)){
            $novaQuantidade = max(0, $this->quantidade - $quantidade);
            if ($novaQuantidade === 0) {
                return $this->deletar();
            } else {
                $this->setQuantidade($novaQuantidade);
                return $this->salvar();
            }
        }
        return false;
    }

    public function buscarItensTreinador($id_treinador){
        $sql = "SELECT ti.*, i.nome, i.descricao, i.preco, i.id_categoria
                FROM treinador_item ti
                JOIN item i ON ti.id_item = i.id_item
                WHERE ti.id_treinador = :id_treinador AND (ti.quantidade IS NULL OR ti.quantidade > 0)
                ORDER BY i.id_categoria ASC, i.id_item ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_treinador' => $id_treinador]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
