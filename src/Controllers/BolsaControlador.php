<?php

class BolsaControlador
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function listarItens()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idTreinador = $_SESSION['usuario_logado']['id_treinador'] ?? null;
        $itensTreinador = [];
        $equipeBolsa = [];

        if ($idTreinador) {
            require_once __DIR__ . "/../Models/TreinadorItem.php";
            $treinadorItem = new TreinadorItem($this->pdo);
            $itensTreinador = $treinadorItem->buscarItensTreinador($idTreinador);
            // Equipe para escolher o alvo ao usar um item (cura, TM, Evolution Stone)
            $equipeBolsa = array_values(array_filter($this->carregarSlotsDetalhados((int) $idTreinador)));
        }

        require_once __DIR__ . "/../Views/bolsa/itens.php";
    }

    public function listarInsignias()
    {
        $idTreinador = $_SESSION['usuario_logado']['id_treinador'] ?? null;
        if (!$idTreinador) {
            header("Location: ?modulo=treinador&acao=cadastroLogin");
            exit;
        }

        require_once __DIR__ . '/../Models/Treinador.php';
        $treinador = new Treinador($this->pdo);
        $vitorias = $treinador->buscarVitorias((int) $idTreinador);
        $iconesInsignias = [
            1 => 'pedra',
            2 => 'lutador',
            3 => 'eletrico',
            4 => 'fogo',
            5 => 'normal',
            6 => 'voador',
            7 => 'psiquico',
            8 => 'agua',
        ];
        $insignias = [];

        foreach ($iconesInsignias as $numeroGinasio => $icone) {
            $ginasio = Treinador::GINASIOS[$numeroGinasio];
            $insignias[] = [
                'nome' => $ginasio['insignia'],
                'cidade' => $ginasio['cidade'],
                'icone' => $icone,
                'conquistada' => $treinador->ginasioConcluido((int) $idTreinador, $numeroGinasio, $vitorias),
            ];
        }

        $mapaRetorno = $_SESSION['mapa'] ?? 'mapaInicio';
        $mapaValido = is_string($mapaRetorno)
            && (preg_match('/^(mapa|ginasio)([1-8])$/', $mapaRetorno)
                || in_array($mapaRetorno, ['mapaInicio', 'mapaCampeao', 'ginasioCampeao'], true));
        if (!$mapaValido) {
            $mapaRetorno = 'mapaInicio';
        }
        $linkVoltar = '?modulo=mapa&acao=' . rawurlencode($mapaRetorno);

        require_once __DIR__ . '/../Views/bolsa/insignias.php';
    }

    public function listarTodosPokemons(){
        $idTreinador = $_SESSION['usuario_logado']['id_treinador'] ?? null;
        if (!$idTreinador) {
            header("Location: ?modulo=treinador&acao=cadastroLogin");
            exit;
        }

        $mapaRetorno = $_SESSION['mapa'] ?? 'mapaInicio';
        $slots = $this->carregarSlotsDetalhados((int) $idTreinador);
        $pokemons = $this->carregarPokemonsTreinador((int) $idTreinador, $slots);

        require_once __DIR__ . '/../Views/bolsa/pokemons.php';
    }

    public function carregarPokemonsTreinador($id_treinador, array $slots = []){
        // Coleta os IDs de pokemons que já estão nos slots para excluí-los do box
        $idsNosSlots = [];
        foreach ($slots as $s) {
            if (!empty($s['id_pokemon'])) {
                $idsNosSlots[] = (int) $s['id_pokemon'];
            }
        }

        $stmt = $this->pdo->prepare(
            "SELECT
                p.id_pokemon,
                p.id_pokedex,
                p.nome,
                p.hp_atual,
                p.sexo,
                p.status,
                pd.hp AS hp_maximo,
                pd.atk,
                pd.def,
                pd.spa,
                pd.spd,
                pd.spe,
                pd.fase,
                pd.id_evolucao
             FROM pokemon p
             INNER JOIN pokedex pd ON pd.id_pokedex = p.id_pokedex
             WHERE p.id_treinador = :id_treinador
             ORDER BY p.id_pokemon ASC"
        );
        $stmt->execute(['id_treinador' => $id_treinador]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $foraDosSlots = [];
        foreach ($rows as $p) {
            if (!in_array((int) $p['id_pokemon'], $idsNosSlots, true)) {
                $foraDosSlots[] = $this->enriquecerPokemon($p);
            }
        }

        return array_values($foraDosSlots);
    }

    public function atribuirSlot()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'erro' => 'Método não permitido.']);
            return;
        }

        $idTreinador = $_SESSION['usuario_logado']['id_treinador'] ?? null;
        if (!$idTreinador) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'erro' => 'Treinador não autenticado.']);
            return;
        }

        $raw = file_get_contents('php://input');
        $payload = $raw ? json_decode($raw, true) : [];
        if (!is_array($payload)) $payload = [];

        $slotNum  = (int)($payload['slot']       ?? 0);
        $idPokemon = (int)($payload['id_pokemon'] ?? 0);

        if ($slotNum < 1 || $slotNum > 6 || !$idPokemon) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => 'Parâmetros inválidos.']);
            return;
        }

        // Verifica que o pokémon pertence ao treinador
        $stmtCheck = $this->pdo->prepare(
            "SELECT id_pokemon FROM pokemon WHERE id_pokemon = :id AND id_treinador = :tr"
        );
        $stmtCheck->execute(['id' => $idPokemon, 'tr' => $idTreinador]);
        if (!$stmtCheck->fetch()) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'erro' => 'Pokémon não encontrado.']);
            return;
        }

        // Busca slots atuais e substitui apenas o slot desejado
        $slotsAtuais = $this->carregarSlotsDetalhados((int)$idTreinador);
        $ids = [];
        for ($i = 1; $i <= 6; $i++) {
            $ids[$i] = $slotsAtuais[$i - 1]['id_pokemon'] ?? null;
        }
        // Se o pokémon já estava em outro slot, limpa o slot anterior
        for ($i = 1; $i <= 6; $i++) {
            if ($ids[$i] === $idPokemon) {
                $ids[$i] = null;
            }
        }
        $ids[$slotNum] = $idPokemon;

        $stmt = $this->pdo->prepare(
            "UPDATE treinador
             SET slot1 = :slot1, slot2 = :slot2, slot3 = :slot3,
                 slot4 = :slot4, slot5 = :slot5, slot6 = :slot6
             WHERE id_treinador = :id_treinador"
        );
        $stmt->execute([
            'slot1' => $ids[1], 'slot2' => $ids[2], 'slot3' => $ids[3],
            'slot4' => $ids[4], 'slot5' => $ids[5], 'slot6' => $ids[6],
            'id_treinador' => (int)$idTreinador,
        ]);

        require_once __DIR__ . '/../Models/Treinador.php';
        $treinador = new Treinador($this->pdo);
        $treinador->recarregarSessao((int)$idTreinador);

        echo json_encode([
            'ok'    => true,
            'slots' => $this->carregarSlotsDetalhados((int)$idTreinador),
        ]);
    }

    public function trocarSlot()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'erro' => 'Método não permitido.']);
            return;
        }

        $idTreinador = $_SESSION['usuario_logado']['id_treinador'] ?? null;
        if (!$idTreinador) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'erro' => 'Treinador não autenticado.']);
            return;
        }

        $raw = file_get_contents('php://input');
        $payload = $raw ? json_decode($raw, true) : $_POST;
        if (!is_array($payload)) {
            $payload = [];
        }

        $slotA = isset($payload['slot_a']) ? (int) $payload['slot_a'] : 0;
        $slotB = isset($payload['slot_b']) ? (int) $payload['slot_b'] : 0;

        if ($slotA < 1 || $slotA > 6 || $slotB < 1 || $slotB > 6 || $slotA === $slotB) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => 'Slots inválidos para troca.']);
            return;
        }

        // Garante que os slots do treinador estejam sincronizados antes de trocar
        $slotsAtuais = $this->carregarSlotsDetalhados((int) $idTreinador);
        $ids = [];
        for ($i = 1; $i <= 6; $i++) {
            $ids[$i] = $slotsAtuais[$i - 1]['id_pokemon'] ?? null;
        }

        $temp = $ids[$slotA];
        $ids[$slotA] = $ids[$slotB];
        $ids[$slotB] = $temp;

        $stmt = $this->pdo->prepare(
            "UPDATE treinador
             SET slot1 = :slot1, slot2 = :slot2, slot3 = :slot3,
                 slot4 = :slot4, slot5 = :slot5, slot6 = :slot6
             WHERE id_treinador = :id_treinador"
        );
        $stmt->execute([
            'slot1' => $ids[1],
            'slot2' => $ids[2],
            'slot3' => $ids[3],
            'slot4' => $ids[4],
            'slot5' => $ids[5],
            'slot6' => $ids[6],
            'id_treinador' => (int) $idTreinador,
        ]);

        // Atualiza a sessão do treinador
        require_once __DIR__ . '/../Models/Treinador.php';
        $treinador = new Treinador($this->pdo);
        $treinador->recarregarSessao((int) $idTreinador);

        echo json_encode([
            'ok' => true,
            'slots' => $this->carregarSlotsDetalhados((int) $idTreinador),
        ]);
    }

    private function carregarSlotsDetalhados(int $idTreinador): array
    {
        $stmtTreinador = $this->pdo->prepare(
            "SELECT slot1, slot2, slot3, slot4, slot5, slot6 FROM treinador WHERE id_treinador = :id_treinador"
        );
        $stmtTreinador->execute(['id_treinador' => $idTreinador]);
        $slotsRow = $stmtTreinador->fetch(PDO::FETCH_ASSOC) ?: [];

        $stmtPokemons = $this->pdo->prepare(
            "SELECT p.id_pokemon, p.id_pokedex, p.nome, p.hp_atual, p.sexo, p.status,
                    pd.hp AS hp_maximo, pd.atk, pd.def, pd.spa, pd.spd, pd.spe, pd.fase, pd.id_evolucao
             FROM pokemon p
             INNER JOIN pokedex pd ON pd.id_pokedex = p.id_pokedex
             WHERE p.id_treinador = :id_treinador
             ORDER BY p.id_pokemon ASC"
        );
        $stmtPokemons->execute(['id_treinador' => $idTreinador]);
        $todosPokemons = $stmtPokemons->fetchAll(PDO::FETCH_ASSOC);

        $slots = array_fill(0, 6, null);
        if (empty($todosPokemons)) {
            return $slots;
        }

        $pokemonsPorId = [];
        foreach ($todosPokemons as $poke) {
            $pokemonsPorId[(int) $poke['id_pokemon']] = $poke;
        }

        $usados = [];
        $temAlgumSlotPreenchido = false;

        for ($i = 1; $i <= 6; $i++) {
            $valorSlot = $slotsRow["slot{$i}"] ?? null;
            if (empty($valorSlot) || !is_numeric($valorSlot)) {
                continue;
            }
            $idSlot = (int) $valorSlot;
            if (isset($pokemonsPorId[$idSlot]) && !isset($usados[$idSlot])) {
                $usados[$idSlot] = true;
                $slots[$i - 1] = $this->enriquecerPokemon($pokemonsPorId[$idSlot]);
                $temAlgumSlotPreenchido = true;
            } else {
                // Compatibilidade com contas antigas que salvaram id_pokedex no slot
                foreach ($todosPokemons as $poke) {
                    $idPoke = (int) $poke['id_pokemon'];
                    if (!isset($usados[$idPoke]) && (int) $poke['id_pokedex'] === $idSlot) {
                        $usados[$idPoke] = true;
                        $slots[$i - 1] = $this->enriquecerPokemon($poke);
                        $temAlgumSlotPreenchido = true;
                        break;
                    }
                }
            }
        }

        if (!$temAlgumSlotPreenchido) {
            foreach (array_slice($todosPokemons, 0, 6) as $idx => $poke) {
                $slots[$idx] = $this->enriquecerPokemon($poke);
            }
        }

        return $slots;
    }

    private function enriquecerPokemon(array $poke): array
    {
        $idPokemon = (int) $poke['id_pokemon'];
        $idPokedex = (int) $poke['id_pokedex'];
        $hpMax = max(1, (int) $poke['hp_maximo']);
        $hpAtual = max(0, min((int) $poke['hp_atual'], $hpMax));

        return [
            'id_pokemon' => $idPokemon,
            'id_pokedex' => $idPokedex,
            'nome' => strtoupper($poke['nome']),
            'hp_atual' => $hpAtual,
            'hp_maximo' => $hpMax,
            'atk' => (int) $poke['atk'],
            'def' => (int) $poke['def'],
            'spa' => (int) $poke['spa'],
            'spd' => (int) $poke['spd'],
            'spe' => (int) $poke['spe'],
            'fase' => (int) $poke['fase'],
            'pode_evoluir' => !empty($poke['id_evolucao']),
            'sexo' => strtolower((string) $poke['sexo']),
            'status' => !empty($poke['status']) ? strtolower((string) $poke['status']) : null,
            'tipos' => $this->buscarTipos($idPokedex),
            'movimentos' => $this->garantirMovimentos($idPokemon),
        ];
    }

    private function buscarTipos(int $idPokedex): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT t.id_tipo, t.nome
             FROM pokedex_tipo pt
             INNER JOIN tipo t ON t.id_tipo = pt.id_tipo
             WHERE pt.id_pokedex = :id_pokedex"
        );
        $stmt->execute(['id_pokedex' => $idPokedex]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($r) => strtolower((string) $r['nome']), $rows);
    }

    private function garantirMovimentos(int $idPokemon): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT m.id_movimento, m.nome, m.precisao, m.prioridade, m.poder, m.tipo,
                    t.nome AS tipo_nome,
                    e.nome AS efeito_nome, e.chance AS efeito_chance
             FROM pokemon_movimento pm
             INNER JOIN movimento m ON m.id_movimento = pm.id_movimento
             INNER JOIN tipo t ON t.id_tipo = m.id_tipo
             LEFT JOIN efeito e ON e.id_efeito = m.id_efeito
             WHERE pm.id_pokemon = :id_pokemon
             LIMIT 4"
        );
        $stmt->execute(['id_pokemon' => $idPokemon]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) < 4) {
            $existentes = array_map(fn($m) => (int) $m['id_movimento'], $rows);
            $faltam = 4 - count($rows);

            if (!empty($existentes)) {
                $placeholders = implode(',', array_fill(0, count($existentes), '?'));
                $stmtRand = $this->pdo->prepare(
                    "SELECT id_movimento FROM movimento WHERE id_movimento NOT IN ({$placeholders}) ORDER BY RAND() LIMIT {$faltam}"
                );
                $stmtRand->execute($existentes);
            } else {
                $stmtRand = $this->pdo->query("SELECT id_movimento FROM movimento ORDER BY RAND() LIMIT {$faltam}");
            }

            $novosIds = $stmtRand->fetchAll(PDO::FETCH_COLUMN);
            if (!empty($novosIds)) {
                $stmtInsert = $this->pdo->prepare(
                    "INSERT INTO pokemon_movimento (id_pokemon, id_movimento) VALUES (:id_pokemon, :id_movimento)"
                );
                foreach ($novosIds as $idMov) {
                    $stmtInsert->execute([
                        'id_pokemon' => $idPokemon,
                        'id_movimento' => (int) $idMov,
                    ]);
                }

                $stmt->execute(['id_pokemon' => $idPokemon]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        $movimentos = [];
        foreach ($rows as $row) {
            $movimentos[] = [
                'id_movimento' => (int) $row['id_movimento'],
                'nome' => $row['nome'],
                'precisao' => (int) $row['precisao'],
                'prioridade' => (int) $row['prioridade'],
                'poder' => (int) $row['poder'],
                'categoria' => strtolower((string) $row['tipo']),
                'tipo_nome' => strtolower((string) $row['tipo_nome']),
                'efeito_nome' => $row['efeito_nome'] !== null ? strtolower((string) $row['efeito_nome']) : null,
                'efeito_chance' => $row['efeito_chance'] !== null ? (int) $row['efeito_chance'] : 0,
            ];
        }

        return $movimentos;
    }
}
