<?php
/**
 * Classe base de todos os models: guarda a conexão PDO e oferece
 * o UPDATE dinâmico dos campos alterados via setter.
 */
abstract class Modelo {
    protected $pdo;
    protected $camposAlterados = [];

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    // Atualiza só os campos marcados em $camposAlterados. $where = ['coluna' => valor, ...]
    protected function atualizarCampos(string $tabela, array $where): bool {
        if (empty($this->camposAlterados)){
            return true;
        }

        $setClauses = [];
        $params = $where;
        foreach ($this->camposAlterados as $campo => $valor) {
            $setClauses[] = "$campo = :$campo";
            $params[$campo] = $valor;
        }

        $condicoes = [];
        foreach (array_keys($where) as $coluna) {
            $condicoes[] = "$coluna = :$coluna";
        }

        $sql = "UPDATE $tabela SET " . implode(', ', $setClauses) . " WHERE " . implode(' AND ', $condicoes);
        $ok = $this->pdo->prepare($sql)->execute($params);

        if ($ok) {
            $this->camposAlterados = [];
        }
        return $ok;
    }
}
