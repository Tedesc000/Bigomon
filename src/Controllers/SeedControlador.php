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
                require_once __DIR__ . "/../Views/treinador/home.php";
            } catch (PDOException $e) {
                echo "<p style='color: red;'> Erro ao executar <strong>{$nomeArquivo}</strong>: " . $e->getMessage() . "</p>";
            }
        }
    }

    public function executar() {
        $this->rodar();
    }
}
