<?php

class SeedControlador {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function rodar() {
        $caminhoSeeds = __DIR__ . "/../../database/seeds";
        $arquivos = glob($caminhoSeeds . "/*.sql");

        if (empty($arquivos)) {
            echo "<p>Nenhum arquivo de seed (.sql) encontrado em <code>database/seeds/</code>.</p>";
            echo "<p><a href='index.php'>Voltar para o início</a></p>";
            return;
        }

        foreach ($arquivos as $arquivo) {
            $nomeArquivo = basename($arquivo);
            $sql = file_get_contents($arquivo);

            if (empty(trim($sql))) {
                continue;
            }

            try {
                $this->pdo->exec($sql);
            } catch (PDOException $e) {
                echo "<p style='color: red;'> Erro ao executar <strong>{$nomeArquivo}</strong>: " . $e->getMessage() . "</p>";
            }
        }
    }

    public function executar() {
        $this->rodar();
    }

    public function verificarTriggers() {
        try {
            $stmt = $this->pdo->query("SHOW TRIGGERS LIKE 'trg_treinador_dinheiro_update'");
            return $stmt && $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function rodarTriggers() {
        $arquivoTriggers = __DIR__ . "/../../database/triggers.sql";
        if (!file_exists($arquivoTriggers)) {
            return;
        }

        $sql = file_get_contents($arquivoTriggers);
        // remove os DELIMITER que são apenas do terminal
        $sqlLimpo = preg_replace('/DELIMITER\s*(\$\$|;)?/i', '', $sql);
        // aqui separa cada trigger pelo delimitador $$ do arquivo
        $triggers = explode('$$', $sqlLimpo);

        foreach ($triggers as $trigger) {
            $cmd = trim($trigger);
            if (!empty($cmd)) {
                try {
                    $this->pdo->exec($cmd);
                } catch (PDOException $e) {
                    echo "<p style='color: red;'>Erro ao executar trigger: " . htmlspecialchars($e->getMessage()) . "</p>";
                }
            }
        }
    }

    public function triggers() {
        $this->rodarTriggers();
    }
}
