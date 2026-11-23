<?php

// Uso de itens fora da batalha (página da bolsa): cura, reviver, TMs e Evolution Stone
class ItemControlador
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function usar()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'erro' => 'Método não permitido.']);
            return;
        }

        $idTreinador = (int) ($_SESSION['usuario_logado']['id_treinador'] ?? 0);
        if (!$idTreinador) {
            $this->erro(401, 'Treinador não autenticado.');
            return;
        }

        // A batalha guarda uma cópia da equipe e regravaria o HP antigo por cima
        $batalha = $_SESSION['batalha_atual'] ?? null;
        if (is_array($batalha) && empty($batalha['finalizada']) && !empty($batalha['equipe_oponente'])) {
            $this->erro(409, 'Você tem uma batalha em andamento! Termine ela primeiro.');
            return;
        }

        $raw = file_get_contents('php://input');
        $payload = $raw ? json_decode($raw, true) : $_POST;
        if (!is_array($payload)) {
            $payload = [];
        }

        $idItem = (int) ($payload['id_item'] ?? 0);
        $idPokemon = (int) ($payload['id_pokemon'] ?? 0);

        try {
            $item = $this->buscarItemDoTreinador($idTreinador, $idItem);
            if (!$item) {
                throw new ItemException('Você não tem esse item.');
            }

            if (!ItemEfeito::usavelForaDeBatalha($idItem)) {
                $mensagem = ItemEfeito::ehPokebola($idItem)
                    ? 'Pokébolas só podem ser usadas em batalhas no matinho.'
                    : 'Esse item não pode ser usado agora.';
                throw new ItemException($mensagem);
            }

            $poke = $this->buscarPokemonDoTreinador($idTreinador, $idPokemon);
            if (!$poke) {
                throw new ItemException('Escolha um dos seus Pokémon.');
            }

            $tipo = ItemEfeito::efeito($idItem)['tipo'];

            $this->pdo->beginTransaction();
            $resultado = match ($tipo) {
                'tm' => $this->ensinarTM($poke, $idItem, (int) ($payload['id_movimento_esquecer'] ?? 0)),
                'evolucao' => $this->evoluir($poke),
                default => $this->usarConsumivel($poke, $idItem),
            };

            if (!$resultado['ok']) {
                $this->pdo->rollBack();
                throw new ItemException($resultado['erro'] ?? 'Não foi possível usar o item.');
            }

            // TM é reutilizável: só os outros itens são gastos
            if ($tipo !== 'tm') {
                (new TreinadorItem($this->pdo))->removerItem($idTreinador, $idItem, 1);
            }

            $this->pdo->commit();

            echo json_encode([
                'ok' => true,
                'mensagens' => $resultado['mensagens'],
                'id_pokedex' => $resultado['id_pokedex'] ?? (int) $poke['id_pokedex'],
            ]);
        } catch (ItemException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $this->erro(400, $e->getMessage());
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $this->erro(500, 'Não foi possível usar o item.');
        }
    }

    private function usarConsumivel(array $poke, int $idItem): array
    {
        $resultado = ItemEfeito::aplicarConsumivel($poke, $idItem);
        if (!$resultado['ok']) {
            return $resultado;
        }

        $this->pdo->prepare("UPDATE pokemon SET hp_atual = :hp, status = :status WHERE id_pokemon = :id")
            ->execute([
                'hp' => (int) $poke['hp_atual'],
                'status' => $poke['status'] ?: null,
                'id' => (int) $poke['id_pokemon'],
            ]);

        return $resultado;
    }

    private function ensinarTM(array $poke, int $idItem, int $idEsquecer): array
    {
        $idNovo = ItemEfeito::efeito($idItem)['id_movimento'];
        $nome = strtoupper($poke['nome']);

        $stmt = $this->pdo->prepare(
            "SELECT pm.id_movimento, m.nome
             FROM pokemon_movimento pm
             INNER JOIN movimento m ON m.id_movimento = pm.id_movimento
             WHERE pm.id_pokemon = :id"
        );
        $stmt->execute(['id' => (int) $poke['id_pokemon']]);
        $atuais = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $atuais[(int) $row['id_movimento']] = $row['nome'];
        }

        $stmtNovo = $this->pdo->prepare("SELECT nome FROM movimento WHERE id_movimento = :id");
        $stmtNovo->execute(['id' => $idNovo]);
        $nomeNovo = $stmtNovo->fetchColumn();
        if ($nomeNovo === false) {
            return ['ok' => false, 'erro' => 'Esse TM não ensina nenhum ataque.'];
        }

        if (isset($atuais[$idNovo])) {
            return ['ok' => false, 'erro' => "{$nome} já conhece {$nomeNovo}!"];
        }

        // Ainda tem espaço (menos de 4 golpes): aprende sem esquecer nada
        if (count($atuais) < 4) {
            $this->pdo->prepare("INSERT INTO pokemon_movimento (id_pokemon, id_movimento) VALUES (:p, :m)")
                ->execute(['p' => (int) $poke['id_pokemon'], 'm' => $idNovo]);
            return ['ok' => true, 'mensagens' => ["{$nome} aprendeu {$nomeNovo}!"]];
        }

        if (!isset($atuais[$idEsquecer])) {
            return ['ok' => false, 'erro' => 'Escolha qual ataque esquecer.'];
        }

        $this->pdo->prepare(
            "UPDATE pokemon_movimento SET id_movimento = :novo
             WHERE id_pokemon = :p AND id_movimento = :velho
             LIMIT 1"
        )->execute(['novo' => $idNovo, 'p' => (int) $poke['id_pokemon'], 'velho' => $idEsquecer]);

        return [
            'ok' => true,
            'mensagens' => ["{$nome} esqueceu {$atuais[$idEsquecer]} e aprendeu {$nomeNovo}!"],
        ];
    }

    private function evoluir(array $poke): array
    {
        $nome = strtoupper($poke['nome']);
        if (empty($poke['id_evolucao'])) {
            return ['ok' => false, 'erro' => "{$nome} não tem uma evolução disponível."];
        }

        $stmt = $this->pdo->prepare("SELECT id_pokedex, nome, hp FROM pokedex WHERE id_pokedex = :id");
        $stmt->execute(['id' => (int) $poke['id_evolucao']]);
        $evolucao = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$evolucao) {
            return ['ok' => false, 'erro' => "{$nome} não tem uma evolução disponível."];
        }

        // Mantém o dano já sofrido; desmaiado continua desmaiado
        $hpAtual = (int) $poke['hp_atual'];
        $danoSofrido = max(0, (int) $poke['hp_maximo'] - $hpAtual);
        $novoMax = max(1, (int) $evolucao['hp']);
        $novoHp = $hpAtual <= 0 ? 0 : max(1, $novoMax - $danoSofrido);

        // Sem apelido (nome igual ao da espécie): passa a ter o nome da evolução
        $novoNome = strcasecmp(trim($poke['nome']), trim($poke['especie'])) === 0
            ? $evolucao['nome']
            : $poke['nome'];

        $this->pdo->prepare(
            "UPDATE pokemon SET id_pokedex = :pokedex, nome = :nome, hp_atual = :hp WHERE id_pokemon = :id"
        )->execute([
            'pokedex' => (int) $evolucao['id_pokedex'],
            'nome' => $novoNome,
            'hp' => $novoHp,
            'id' => (int) $poke['id_pokemon'],
        ]);

        return [
            'ok' => true,
            'mensagens' => ["Parabéns! {$nome} evoluiu para " . strtoupper($evolucao['nome']) . '!'],
            'id_pokedex' => (int) $evolucao['id_pokedex'],
        ];
    }

    private function buscarItemDoTreinador(int $idTreinador, int $idItem): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT i.id_item, i.nome, i.id_categoria, ti.quantidade
             FROM treinador_item ti
             INNER JOIN item i ON i.id_item = ti.id_item
             WHERE ti.id_treinador = :t AND ti.id_item = :i AND ti.quantidade > 0"
        );
        $stmt->execute(['t' => $idTreinador, 'i' => $idItem]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function buscarPokemonDoTreinador(int $idTreinador, int $idPokemon): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.id_pokemon, p.id_pokedex, p.nome, p.hp_atual, p.status,
                    pd.nome AS especie, pd.hp AS hp_maximo, pd.id_evolucao
             FROM pokemon p
             INNER JOIN pokedex pd ON pd.id_pokedex = p.id_pokedex
             WHERE p.id_pokemon = :p AND p.id_treinador = :t"
        );
        $stmt->execute(['p' => $idPokemon, 't' => $idTreinador]);
        $poke = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$poke) {
            return null;
        }
        $poke['hp_atual'] = max(0, min((int) $poke['hp_atual'], (int) $poke['hp_maximo']));
        $poke['status'] = !empty($poke['status']) ? strtolower((string) $poke['status']) : null;
        return $poke;
    }

    private function erro(int $codigo, string $mensagem): void
    {
        http_response_code($codigo);
        echo json_encode(['ok' => false, 'erro' => $mensagem]);
    }
}
