<?php
class PokemonControlador {
    private $pdo;   

    public function __construct($pdo) {
        $this->pdo = $pdo;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function sus() {
        $_SESSION['sus'] = 'sus';
        $idTreinador = $_SESSION['usuario_logado']['id_treinador'] ?? null;
        $pokemons = [];

        if ($idTreinador !== null) {
            $stmt = $this->pdo->prepare(
                "SELECT p.id_pokemon, p.id_pokedex, p.nome, p.hp_atual, pd.hp AS hp_maximo
                FROM treinador t
                INNER JOIN pokemon p ON p.id_pokemon IN (
                    COALESCE(t.slot1, -1),
                    COALESCE(t.slot2, -1),
                    COALESCE(t.slot3, -1),
                    COALESCE(t.slot4, -1),
                    COALESCE(t.slot5, -1),
                    COALESCE(t.slot6, -1)
                )
                INNER JOIN pokedex pd ON pd.id_pokedex = p.id_pokedex
                WHERE t.id_treinador = :id_treinador
                ORDER BY FIELD(p.id_pokemon, t.slot1, t.slot2, t.slot3, t.slot4, t.slot5, t.slot6)"
            );
            $stmt->execute(['id_treinador' => $idTreinador]);
            $pokemons = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        require_once __DIR__ . "/../Views/mapas/sus.php";
    }

    public function curar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?modulo=pokemon&acao=sus");
            exit;
        }

        $idTreinador = $_SESSION['usuario_logado']['id_treinador'] ?? null;
        if ($idTreinador === null) {
            header("Location: ?modulo=treinador&acao=cadastroLogin");
            exit;
        }

        $stmt = $this->pdo->prepare(
            "UPDATE pokemon p
            INNER JOIN pokedex pd ON pd.id_pokedex = p.id_pokedex
            INNER JOIN treinador t ON t.id_treinador = p.id_treinador
            SET p.hp_atual = pd.hp,
                p.status = NULL
            WHERE p.id_treinador = :id_treinador
              AND p.id_pokemon IN (
                    COALESCE(t.slot1, -1),
                    COALESCE(t.slot2, -1),
                    COALESCE(t.slot3, -1),
                    COALESCE(t.slot4, -1),
                    COALESCE(t.slot5, -1),
                    COALESCE(t.slot6, -1)
              )"
        );
        $stmt->execute(['id_treinador' => $idTreinador]);

        header("Location: ?modulo=pokemon&acao=sus");
        exit;
    }
}