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
                "SELECT pokemon.id_pokemon, pokemon.id_pokedex, pokemon.nome, pokemon.hp_atual, pokedex.hp AS hp_maximo
                FROM pokemon
                INNER JOIN pokedex ON pokedex.id_pokedex = pokemon.id_pokedex
                WHERE pokemon.id_treinador = :id_treinador
                ORDER BY pokemon.id_pokemon"
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
            "UPDATE pokemon
            INNER JOIN pokedex ON pokedex.id_pokedex = pokemon.id_pokedex
            SET pokemon.hp_atual = pokedex.hp,
                pokemon.status = NULL
            WHERE pokemon.id_treinador = :id_treinador"
        );
        $stmt->execute(['id_treinador' => $idTreinador]);

        header("Location: ?modulo=pokemon&acao=sus");
        exit;
    }
}