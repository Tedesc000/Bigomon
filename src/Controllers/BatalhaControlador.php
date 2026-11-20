<?php

class BatalhaControlador
{
    private $pdo;

    private const MAPA_PARA_MATINHO = [
        'mapaInicio' => 1,
        'mapa1' => 2,
        'mapa2' => 3,
        'mapa3' => 4,
        'mapa4' => 5,
        'mapa5' => 6,
        'mapa6' => 7,
        'mapa7' => 8,
        'mapa8' => 9,
    ];

    public function __construct($pdo)
    {
        $this->pdo = $pdo;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function batalhaMatinho()
    {
        $_SESSION['batalha'] = 'batalhaMatinho';
        $idTreinador = $_SESSION['usuario_logado']['id_treinador'] ?? null;
        if (!$idTreinador) {
            header("Location: ?modulo=treinador&acao=cadastroLogin");
            exit;
        }

        $mapaAtual = $_SESSION['mapa'] ?? 'mapaInicio';
        $idMatinho = self::MAPA_PARA_MATINHO[$mapaAtual] ?? 1;

        // Se já existe uma batalha ativa em andamento para este treinador, retoma a mesma luta ao recarregar
        $batalhaExistente = $_SESSION['batalha_atual'] ?? null;
        if (
            is_array($batalhaExistente)
            && empty($batalhaExistente['finalizada'])
            && ($batalhaExistente['tipo'] ?? null) === 'matinho'
            && (int) ($batalhaExistente['id_treinador'] ?? 0) === (int) $idTreinador
        ) {
            $estadoInicial = $this->montarEstadoPublico($batalhaExistente);
            require_once __DIR__ . '/../Views/batalha/batalhaMatinho.php';
            return;
        }

        $equipe = $this->carregarEquipeTreinador((int) $idTreinador);
        if (empty($equipe)) {
            header("Location: ?modulo=pokemon&acao=sus");
            exit;
        }

        $ativoIdx = $this->encontrarPrimeiroVivo($equipe);
        if ($ativoIdx === null) {
            header("Location: ?modulo=pokemon&acao=sus");
            exit;
        }

        $selvagem = $this->sortearPokemonSelvagem($idMatinho);
        if (!$selvagem) {
            header("Location: ?modulo=mapa&acao=" . urlencode($mapaAtual));
            exit;
        }

        $_SESSION['batalha_atual'] = [
            'tipo' => 'matinho',
            'id_treinador' => (int) $idTreinador,
            'mapa_retorno' => $mapaAtual,
            'id_matinho' => $idMatinho,
            'equipe' => $equipe,
            'ativo_idx' => $ativoIdx,
            'selvagem' => $selvagem,
            'tentativas_fuga' => 0,
            'troca_obrigatoria' => false,
            'finalizada' => false,
            'resultado' => null,
        ];

        $estadoInicial = $this->montarEstadoPublico($_SESSION['batalha_atual']);
        require_once __DIR__ . '/../Views/batalha/batalhaMatinho.php';
    }

    public function atacar()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'erro' => 'Método não permitido.']);
            return;
        }

        $batalha = $_SESSION['batalha_atual'] ?? null;
        if (!$batalha || !empty($batalha['finalizada'])) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => 'Nenhuma batalha ativa encontrada.']);
            return;
        }

        if (!empty($batalha['troca_obrigatoria'])) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => 'Você precisa escolher outro Pokémon antes de atacar.']);
            return;
        }

        $payload = $this->lerJsonBody();
        $idMovimento = isset($payload['id_movimento']) ? (int) $payload['id_movimento'] : 0;

        $ativoIdx = (int) $batalha['ativo_idx'];
        $jogador = &$batalha['equipe'][$ativoIdx];
        $selvagem = &$batalha['selvagem'];

        $movJogador = null;
        foreach ($jogador['movimentos'] as $mov) {
            if ((int) $mov['id_movimento'] === $idMovimento) {
                $movJogador = $mov;
                break;
            }
        }

        if (!$movJogador) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => 'Movimento inválido para este Pokémon.']);
            return;
        }

        $movSelvagem = $selvagem['movimentos'][array_rand($selvagem['movimentos'])];

        $eventos = [];
        $jogadorPrimeiro = $this->jogadorAgePrimeiro($jogador, $movJogador, $selvagem, $movSelvagem);

        if ($jogadorPrimeiro) {
            $this->executarAtaque($batalha, 'jogador', $movJogador, $eventos);
            if ($batalha['selvagem']['hp_atual'] > 0 && $batalha['equipe'][$ativoIdx]['hp_atual'] > 0) {
                $this->executarAtaque($batalha, 'selvagem', $movSelvagem, $eventos);
            }
        } else {
            $this->executarAtaque($batalha, 'selvagem', $movSelvagem, $eventos);
            if ($batalha['equipe'][$ativoIdx]['hp_atual'] > 0 && $batalha['selvagem']['hp_atual'] > 0) {
                $this->executarAtaque($batalha, 'jogador', $movJogador, $eventos);
            }
        }

        // Danos de status no fim do turno (veneno e queimadura)
        if ($batalha['equipe'][$ativoIdx]['hp_atual'] > 0 && $batalha['selvagem']['hp_atual'] > 0) {
            $this->aplicarDanoFimDeTurno($batalha, 'jogador', $eventos);
        }
        if ($batalha['equipe'][$ativoIdx]['hp_atual'] > 0 && $batalha['selvagem']['hp_atual'] > 0) {
            $this->aplicarDanoFimDeTurno($batalha, 'selvagem', $eventos);
        }

        $redirect = $this->finalizarVerificacoesTurno($batalha, $eventos);
        $this->sincronizarEquipeNoBanco($batalha['equipe']);

        if (!empty($batalha['finalizada'])) {
            unset($_SESSION['batalha_atual']);
        } else {
            $_SESSION['batalha_atual'] = $batalha;
        }

        echo json_encode([
            'ok' => true,
            'eventos' => $eventos,
            'estado' => $this->montarEstadoPublico($batalha),
            'redirect' => $redirect,
        ]);
    }

    public function trocar()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'erro' => 'Método não permitido.']);
            return;
        }

        $batalha = $_SESSION['batalha_atual'] ?? null;
        if (!$batalha || !empty($batalha['finalizada'])) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => 'Nenhuma batalha ativa encontrada.']);
            return;
        }

        $payload = $this->lerJsonBody();
        $idPokemonAlvo = isset($payload['id_pokemon']) ? (int) $payload['id_pokemon'] : 0;

        $novoIdx = null;
        foreach ($batalha['equipe'] as $idx => $poke) {
            if ((int) $poke['id_pokemon'] === $idPokemonAlvo) {
                $novoIdx = $idx;
                break;
            }
        }

        if ($novoIdx === null) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => 'Pokémon não encontrado na equipe.']);
            return;
        }

        if ($novoIdx === (int) $batalha['ativo_idx']) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => 'Este Pokémon já está em batalha.']);
            return;
        }

        if ((int) $batalha['equipe'][$novoIdx]['hp_atual'] <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => 'Este Pokémon está desmaiado e não pode lutar.']);
            return;
        }

        $eventos = [];
        $eraTrocaObrigatoria = !empty($batalha['troca_obrigatoria']);
        $antigoNome = strtoupper($batalha['equipe'][$batalha['ativo_idx']]['nome']);
        $novoNome = strtoupper($batalha['equipe'][$novoIdx]['nome']);

        if (!$eraTrocaObrigatoria) {
            $eventos[] = [
                'mensagem' => "{$antigoNome}, volte!",
                'animacao' => 'player-switch-out',
                'hud' => $this->montarSnapshotHud($batalha),
            ];
        }

        $batalha['ativo_idx'] = $novoIdx;
        $batalha['troca_obrigatoria'] = false;

        $eventos[] = [
            'mensagem' => "Vai, {$novoNome}!",
            'animacao' => 'player-switch-in',
            'hud' => $this->montarSnapshotHud($batalha),
        ];

        $redirect = null;

        // Troca voluntária consome o turno: o selvagem ataca
        if (!$eraTrocaObrigatoria && $batalha['selvagem']['hp_atual'] > 0) {
            $movSelvagem = $batalha['selvagem']['movimentos'][array_rand($batalha['selvagem']['movimentos'])];
            $this->executarAtaque($batalha, 'selvagem', $movSelvagem, $eventos);

            if ($batalha['equipe'][$novoIdx]['hp_atual'] > 0 && $batalha['selvagem']['hp_atual'] > 0) {
                $this->aplicarDanoFimDeTurno($batalha, 'jogador', $eventos);
            }
            if ($batalha['equipe'][$novoIdx]['hp_atual'] > 0 && $batalha['selvagem']['hp_atual'] > 0) {
                $this->aplicarDanoFimDeTurno($batalha, 'selvagem', $eventos);
            }

            $redirect = $this->finalizarVerificacoesTurno($batalha, $eventos);
            $this->sincronizarEquipeNoBanco($batalha['equipe']);
        }

        if (!empty($batalha['finalizada'])) {
            unset($_SESSION['batalha_atual']);
        } else {
            $_SESSION['batalha_atual'] = $batalha;
        }

        echo json_encode([
            'ok' => true,
            'eventos' => $eventos,
            'estado' => $this->montarEstadoPublico($batalha),
            'redirect' => $redirect,
        ]);
    }

    public function fugir()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'erro' => 'Método não permitido.']);
            return;
        }

        $batalha = $_SESSION['batalha_atual'] ?? null;
        if (!$batalha || !empty($batalha['finalizada'])) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => 'Nenhuma batalha ativa encontrada.']);
            return;
        }

        if (!empty($batalha['troca_obrigatoria'])) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => 'Escolha um Pokémon antes de tentar fugir.']);
            return;
        }

        $batalha['tentativas_fuga'] = (int) ($batalha['tentativas_fuga'] ?? 0) + 1;
        $ativoIdx = (int) $batalha['ativo_idx'];
        $jogador = $batalha['equipe'][$ativoIdx];
        $selvagem = $batalha['selvagem'];

        $speJogador = $this->calcularVelocidadeEfetiva($jogador);
        $speSelvagem = $this->calcularVelocidadeEfetiva($selvagem);

        // Fórmula oficial de fuga do Pokémon Emerald (Gen 3)
        if ($speJogador >= $speSelvagem) {
            $conseguiuFugir = true;
        } else {
            $chance = intdiv($speJogador * 128, max(1, $speSelvagem)) + (30 * $batalha['tentativas_fuga']);
            $conseguiuFugir = ($chance >= 256) || (random_int(0, 255) < $chance);
        }

        $eventos = [];
        $redirect = null;

        if ($conseguiuFugir) {
            $batalha['finalizada'] = true;
            $batalha['resultado'] = 'fuga';
            $mapaRetorno = $batalha['mapa_retorno'] ?? 'mapaInicio';
            $redirect = '?modulo=mapa&acao=' . urlencode($mapaRetorno);

            $eventos[] = [
                'mensagem' => 'Fugiu em segurança!',
                'animacao' => null,
                'hud' => $this->montarSnapshotHud($batalha),
            ];

            $this->sincronizarEquipeNoBanco($batalha['equipe']);
            unset($_SESSION['batalha_atual']);
        } else {
            $eventos[] = [
                'mensagem' => 'Não conseguiu fugir!',
                'animacao' => null,
                'hud' => $this->montarSnapshotHud($batalha),
            ];

            $movSelvagem = $selvagem['movimentos'][array_rand($selvagem['movimentos'])];
            $this->executarAtaque($batalha, 'selvagem', $movSelvagem, $eventos);

            if ($batalha['equipe'][$ativoIdx]['hp_atual'] > 0 && $batalha['selvagem']['hp_atual'] > 0) {
                $this->aplicarDanoFimDeTurno($batalha, 'jogador', $eventos);
            }
            if ($batalha['equipe'][$ativoIdx]['hp_atual'] > 0 && $batalha['selvagem']['hp_atual'] > 0) {
                $this->aplicarDanoFimDeTurno($batalha, 'selvagem', $eventos);
            }

            $redirect = $this->finalizarVerificacoesTurno($batalha, $eventos);
            $this->sincronizarEquipeNoBanco($batalha['equipe']);

            if (!empty($batalha['finalizada'])) {
                unset($_SESSION['batalha_atual']);
            } else {
                $_SESSION['batalha_atual'] = $batalha;
            }
        }

        echo json_encode([
            'ok' => true,
            'eventos' => $eventos,
            'estado' => $this->montarEstadoPublico($batalha),
            'redirect' => $redirect,
        ]);
    }

    private function lerJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
        return $_POST;
    }

    private function carregarEquipeTreinador(int $idTreinador): array
    {
        $stmtTreinador = $this->pdo->prepare(
            "SELECT slot1, slot2, slot3, slot4, slot5, slot6 FROM treinador WHERE id_treinador = :id_treinador"
        );
        $stmtTreinador->execute(['id_treinador' => $idTreinador]);
        $slotsRow = $stmtTreinador->fetch(PDO::FETCH_ASSOC) ?: [];

        $stmtPokemons = $this->pdo->prepare(
            "SELECT p.id_pokemon, p.id_pokedex, p.nome, p.hp_atual, p.sexo, p.status,
                    pd.hp AS hp_maximo, pd.atk, pd.def, pd.spa, pd.spd, pd.spe
                    FROM pokemon p
                    INNER JOIN pokedex pd ON pd.id_pokedex = p.id_pokedex
                    WHERE p.id_treinador = :id_treinador
                    ORDER BY p.id_pokemon ASC"
        );
        $stmtPokemons->execute(['id_treinador' => $idTreinador]);
        $todosPokemons = $stmtPokemons->fetchAll(PDO::FETCH_ASSOC);

        if (empty($todosPokemons)) {
            return [];
        }

        // Indexa os Pokémon do treinador por id_pokemon e ordena conforme slot1..slot6
        $pokemonsPorId = [];
        foreach ($todosPokemons as $poke) {
            $pokemonsPorId[(int) $poke['id_pokemon']] = $poke;
        }

        $equipeOrdenada = [];
        $usados = [];

        for ($i = 1; $i <= 6; $i++) {
            $valorSlot = $slotsRow["slot{$i}"] ?? null;
            if (empty($valorSlot) || !is_numeric($valorSlot)) {
                continue;
            }
            $idSlot = (int) $valorSlot;

            if (isset($pokemonsPorId[$idSlot]) && !isset($usados[$idSlot])) {
                $usados[$idSlot] = true;
                $equipeOrdenada[] = $pokemonsPorId[$idSlot];
            }
        }

        // Fallback caso slots estejam vazios mas o treinador possua Pokémon
        if (empty($equipeOrdenada)) {
            $equipeOrdenada = array_slice($todosPokemons, 0, 6);
        }

        $equipeCompleta = [];
        foreach ($equipeOrdenada as $poke) {
            $idPokemon = (int) $poke['id_pokemon'];
            $idPokedex = (int) $poke['id_pokedex'];
            $hpMax = max(1, (int) $poke['hp_maximo']);
            $hpAtual = max(0, min((int) $poke['hp_atual'], $hpMax));

            $movimentos = $this->garantirMovimentosPokemon($idPokemon);
            $tipos = $this->buscarTiposPokedex($idPokedex);
            $status = !empty($poke['status']) ? strtolower((string) $poke['status']) : null;

            $equipeCompleta[] = [
                'id_pokemon' => $idPokemon,
                'id_pokedex' => $idPokedex,
                'nome' => $poke['nome'],
                'hp_atual' => $hpAtual,
                'hp_maximo' => $hpMax,
                'atk' => max(1, (int) $poke['atk']),
                'def' => max(1, (int) $poke['def']),
                'spa' => max(1, (int) $poke['spa']),
                'spd' => max(1, (int) $poke['spd']),
                'spe' => max(1, (int) $poke['spe']),
                'sexo' => $poke['sexo'],
                'status' => $status,
                'turnos_sono' => $status === 'sono' ? random_int(1, 3) : 0,
                'tipos' => $tipos,
                'movimentos' => $movimentos,
            ];
        }

        return $equipeCompleta;
    }

    private function garantirMovimentosPokemon(int $idPokemon): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT m.id_movimento, m.nome, m.precisao, m.prioridade, m.poder, m.tipo,
                    m.id_tipo, t.nome AS tipo_nome,
                    m.id_efeito, e.nome AS efeito_nome, e.chance AS efeito_chance
             FROM pokemon_movimento pm
             INNER JOIN movimento m ON m.id_movimento = pm.id_movimento
             INNER JOIN tipo t ON t.id_tipo = m.id_tipo
             LEFT JOIN efeito e ON e.id_efeito = m.id_efeito
             WHERE pm.id_pokemon = :id_pokemon
             LIMIT 4"
        );
        $stmt->execute(['id_pokemon' => $idPokemon]);
        $movimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($movimentos) < 4) {
            $existentes = array_map(fn($m) => (int) $m['id_movimento'], $movimentos);
            $faltam = 4 - count($movimentos);

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
                $movimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        return $this->normalizarMovimentos($movimentos);
    }

    private function sortearPokemonSelvagem(int $idMatinho): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT pd.id_pokedex, pd.nome, pd.hp, pd.atk, pd.def, pd.spa, pd.spd, pd.spe
             FROM matinho_pokedex mp
             INNER JOIN pokedex pd ON pd.id_pokedex = mp.id_pokedex
             WHERE mp.id_matinho = :id_matinho
             ORDER BY RAND()
             LIMIT 1"
        );
        $stmt->execute(['id_matinho' => $idMatinho]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            $stmtFallback = $this->pdo->query(
                "SELECT id_pokedex, nome, hp, atk, def, spa, spd, spe FROM pokedex ORDER BY RAND() LIMIT 1"
            );
            $dados = $stmtFallback->fetch(PDO::FETCH_ASSOC);
        }

        if (!$dados) {
            return null;
        }

        $stmtMov = $this->pdo->query(
            "SELECT m.id_movimento, m.nome, m.precisao, m.prioridade, m.poder, m.tipo,
                    m.id_tipo, t.nome AS tipo_nome,
                    m.id_efeito, e.nome AS efeito_nome, e.chance AS efeito_chance
             FROM movimento m
             INNER JOIN tipo t ON t.id_tipo = m.id_tipo
             LEFT JOIN efeito e ON e.id_efeito = m.id_efeito
             ORDER BY RAND()
             LIMIT 4"
        );
        $movimentos = $this->normalizarMovimentos($stmtMov->fetchAll(PDO::FETCH_ASSOC));
        $idPokedex = (int) $dados['id_pokedex'];
        $hpMax = max(1, (int) $dados['hp']);

        return [
            'id_pokedex' => $idPokedex,
            'nome' => $dados['nome'],
            'hp_atual' => $hpMax,
            'hp_maximo' => $hpMax,
            'atk' => max(1, (int) $dados['atk']),
            'def' => max(1, (int) $dados['def']),
            'spa' => max(1, (int) $dados['spa']),
            'spd' => max(1, (int) $dados['spd']),
            'spe' => max(1, (int) $dados['spe']),
            'status' => null,
            'turnos_sono' => 0,
            'tipos' => $this->buscarTiposPokedex($idPokedex),
            'movimentos' => $movimentos,
        ];
    }

    private function normalizarMovimentos(array $rows): array
    {
        $lista = [];
        foreach ($rows as $row) {
            $lista[] = [
                'id_movimento' => (int) $row['id_movimento'],
                'nome' => $row['nome'],
                'precisao' => (int) $row['precisao'],
                'prioridade' => (int) $row['prioridade'],
                'poder' => (int) $row['poder'],
                'categoria' => strtolower((string) $row['tipo']),
                'id_tipo' => (int) $row['id_tipo'],
                'tipo_nome' => strtolower((string) $row['tipo_nome']),
                'id_efeito' => $row['id_efeito'] !== null ? (int) $row['id_efeito'] : null,
                'efeito_nome' => $row['efeito_nome'] !== null ? strtolower((string) $row['efeito_nome']) : null,
                'efeito_chance' => $row['efeito_chance'] !== null ? (int) $row['efeito_chance'] : 0,
            ];
        }
        return $lista;
    }

    private function buscarTiposPokedex(int $idPokedex): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT t.id_tipo, t.nome, t.res_d, t.res_q, t.frq_d, t.frq_q, t.imunidade
             FROM pokedex_tipo pt
             INNER JOIN tipo t ON t.id_tipo = pt.id_tipo
             WHERE pt.id_pokedex = :id_pokedex"
        );
        $stmt->execute(['id_pokedex' => $idPokedex]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tipos = [];
        foreach ($rows as $row) {
            $tipos[] = [
                'id_tipo' => (int) $row['id_tipo'],
                'nome' => strtolower((string) $row['nome']),
                'res_d' => $this->parseListaIds($row['res_d'] ?? ''),
                'res_q' => $this->parseListaIds($row['res_q'] ?? ''),
                'frq_d' => $this->parseListaIds($row['frq_d'] ?? ''),
                'frq_q' => $this->parseListaIds($row['frq_q'] ?? ''),
                'imunidade' => $this->parseListaIds($row['imunidade'] ?? ''),
            ];
        }
        return $tipos;
    }

    private function parseListaIds(?string $valor): array
    {
        if (!$valor) {
            return [];
        }
        $partes = array_filter(array_map('trim', explode(',', $valor)), fn($v) => $v !== '' && is_numeric($v));
        return array_map('intval', $partes);
    }

    private function encontrarPrimeiroVivo(array $equipe): ?int
    {
        foreach ($equipe as $idx => $poke) {
            if ((int) $poke['hp_atual'] > 0) {
                return $idx;
            }
        }
        return null;
    }

    private function calcularVelocidadeEfetiva(array $poke): int
    {
        $spe = max(1, (int) $poke['spe']);
        if (($poke['status'] ?? null) === 'paralisia') {
            $spe = max(1, intdiv($spe, 2));
        }
        return $spe;
    }

    private function jogadorAgePrimeiro(array $jogador, array $movJ, array $selvagem, array $movS): bool
    {
        if ((int) $movJ['prioridade'] !== (int) $movS['prioridade']) {
            return (int) $movJ['prioridade'] > (int) $movS['prioridade'];
        }

        $speJ = $this->calcularVelocidadeEfetiva($jogador);
        $speS = $this->calcularVelocidadeEfetiva($selvagem);

        if ($speJ !== $speS) {
            return $speJ > $speS;
        }

        return random_int(0, 1) === 1;
    }

    private function executarAtaque(array &$batalha, string $ladoAtacante, array $movimento, array &$eventos): void
    {
        $ativoIdx = (int) $batalha['ativo_idx'];
        if ($ladoAtacante === 'jogador') {
            $atacante = &$batalha['equipe'][$ativoIdx];
            $defensor = &$batalha['selvagem'];
            $nomeAtacante = strtoupper($atacante['nome']);
            $nomeDefensor = strtoupper($defensor['nome']) . ' selvagem';
            $animAtaque = 'player-attack';
            $animHit = 'wild-hit';
        } else {
            $atacante = &$batalha['selvagem'];
            $defensor = &$batalha['equipe'][$ativoIdx];
            $nomeAtacante = strtoupper($atacante['nome']) . ' selvagem';
            $nomeDefensor = strtoupper($defensor['nome']);
            $animAtaque = 'wild-attack';
            $animHit = 'player-hit';
        }

        if ((int) $atacante['hp_atual'] <= 0 || (int) $defensor['hp_atual'] <= 0) {
            return;
        }

        // Verifica status que impedem ação (sono, congelamento, paralisia)
        $statusAtacante = $atacante['status'] ?? null;
        if ($statusAtacante === 'sono') {
            $turnos = (int) ($atacante['turnos_sono'] ?? 1);
            if ($turnos <= 1) {
                $atacante['status'] = null;
                $atacante['turnos_sono'] = 0;
                $eventos[] = [
                    'mensagem' => "{$nomeAtacante} acordou!",
                    'animacao' => null,
                    'hud' => $this->montarSnapshotHud($batalha),
                ];
            } else {
                $atacante['turnos_sono'] = $turnos - 1;
                $eventos[] = [
                    'mensagem' => "{$nomeAtacante} está dormindo profundamente!",
                    'animacao' => null,
                    'hud' => $this->montarSnapshotHud($batalha),
                ];
                return;
            }
        } elseif ($statusAtacante === 'congelamento') {
            if (random_int(1, 100) <= 20) {
                $atacante['status'] = null;
                $eventos[] = [
                    'mensagem' => "{$nomeAtacante} descongelou!",
                    'animacao' => null,
                    'hud' => $this->montarSnapshotHud($batalha),
                ];
            } else {
                $eventos[] = [
                    'mensagem' => "{$nomeAtacante} está congelado!",
                    'animacao' => null,
                    'hud' => $this->montarSnapshotHud($batalha),
                ];
                return;
            }
        } elseif ($statusAtacante === 'paralisia') {
            if (random_int(1, 100) <= 25) {
                $eventos[] = [
                    'mensagem' => "{$nomeAtacante} está paralisado! Não conseguiu se mover!",
                    'animacao' => null,
                    'hud' => $this->montarSnapshotHud($batalha),
                ];
                return;
            }
        }

        // Anuncia o golpe
        $eventos[] = [
            'mensagem' => "{$nomeAtacante} usou {$movimento['nome']}!",
            'animacao' => $animAtaque,
            'hud' => $this->montarSnapshotHud($batalha),
        ];

        // Verifica precisão
        $precisao = max(1, min(100, (int) $movimento['precisao']));
        if (random_int(1, 100) > $precisao) {
            $eventos[] = [
                'mensagem' => "O ataque de {$nomeAtacante} errou!",
                'animacao' => null,
                'hud' => $this->montarSnapshotHud($batalha),
            ];
            return;
        }

        // Calcula eficácia de tipos
        $multiplicadorTipo = $this->calcularEficaciaTipo((int) $movimento['id_tipo'], $defensor['tipos']);
        if ($multiplicadorTipo === 0.0) {
            $eventos[] = [
                'mensagem' => "Não afeta {$nomeDefensor}...",
                'animacao' => null,
                'hud' => $this->montarSnapshotHud($batalha),
            ];
            return;
        }

        // Calcula dano (fórmula adaptada do Emerald com nível base 25 para escalar com HP base da Pokédex e crítico 2x)
        $ehFisico = $movimento['categoria'] === 'fis';
        $statAtaque = $ehFisico ? (int) $atacante['atk'] : (int) $atacante['spa'];
        $statDefesa = $ehFisico ? (int) $defensor['def'] : (int) $defensor['spd'];

        $critico = random_int(1, 16) === 1;
        $nivelEquivalente = 25;
        $base = (((2 * $nivelEquivalente / 5 + 2) * (int) $movimento['poder'] * ($statAtaque / max(1, $statDefesa))) / 50) + 2;

        // Queimadura reduz dano físico pela metade (a menos que seja crítico no Emerald, mas aplicamos redução padrão)
        if ($ehFisico && ($atacante['status'] ?? null) === 'queimadura' && !$critico) {
            $base *= 0.5;
        }

        // STAB (1.5x)
        $stab = 1.0;
        foreach ($atacante['tipos'] as $tipoAtacante) {
            if ((int) $tipoAtacante['id_tipo'] === (int) $movimento['id_tipo']) {
                $stab = 1.5;
                break;
            }
        }

        // Crítico dá DOBRO de dano (2.0x, fiel ao Emerald)
        $multCritico = $critico ? 2.0 : 1.0;
        $variacao = random_int(85, 100) / 100;

        $dano = (int) floor($base * $stab * $multiplicadorTipo * $multCritico * $variacao);
        $dano = max(1, $dano);

        $defensor['hp_atual'] = max(0, (int) $defensor['hp_atual'] - $dano);

        // Golpe de fogo descongela o alvo
        $descongelouPorFogo = false;
        if ($defensor['hp_atual'] > 0 && ($defensor['status'] ?? null) === 'congelamento' && (int) $movimento['id_tipo'] === 10) {
            $defensor['status'] = null;
            $descongelouPorFogo = true;
        }

        $mensagensDano = [];
        if ($critico) {
            $mensagensDano[] = 'Acerto crítico!';
        }
        if ($multiplicadorTipo > 1.0) {
            $mensagensDano[] = 'É super efetivo!';
        } elseif ($multiplicadorTipo < 1.0) {
            $mensagensDano[] = 'Não é muito efetivo...';
        }

        if (!empty($mensagensDano)) {
            $eventos[] = [
                'mensagem' => implode(' ', $mensagensDano),
                'animacao' => $animHit,
                'hud' => $this->montarSnapshotHud($batalha),
            ];
        } else {
            // Evento visual de dano sem mensagem extra (atualiza barra de HP e pisca o sprite)
            $eventos[] = [
                'mensagem' => null,
                'animacao' => $animHit,
                'hud' => $this->montarSnapshotHud($batalha),
            ];
        }

        if ($descongelouPorFogo) {
            $eventos[] = [
                'mensagem' => "{$nomeDefensor} descongelou!",
                'animacao' => null,
                'hud' => $this->montarSnapshotHud($batalha),
            ];
        }

        // Efeito secundário do golpe (se o alvo ainda estiver vivo e sem status)
        if (
            $defensor['hp_atual'] > 0
            && empty($defensor['status'])
            && !empty($movimento['efeito_nome'])
            && (int) $movimento['efeito_chance'] > 0
        ) {
            if (random_int(1, 100) <= (int) $movimento['efeito_chance']) {
                $efeito = $movimento['efeito_nome'];
                $defensor['status'] = $efeito;
                if ($efeito === 'sono') {
                    $defensor['turnos_sono'] = random_int(1, 3);
                }

                $msgStatus = match ($efeito) {
                    'veneno' => "{$nomeDefensor} foi envenenado!",
                    'paralisia' => "{$nomeDefensor} ficou paralisado!",
                    'queimadura' => "{$nomeDefensor} sofreu uma queimadura!",
                    'sono' => "{$nomeDefensor} pegou no sono!",
                    'congelamento' => "{$nomeDefensor} foi congelado!",
                    default => "{$nomeDefensor} sofreu {$efeito}!",
                };

                $eventos[] = [
                    'mensagem' => $msgStatus,
                    'animacao' => null,
                    'hud' => $this->montarSnapshotHud($batalha),
                ];
            }
        }
    }

    private function aplicarDanoFimDeTurno(array &$batalha, string $lado, array &$eventos): void
    {
        $ativoIdx = (int) $batalha['ativo_idx'];
        if ($lado === 'jogador') {
            $poke = &$batalha['equipe'][$ativoIdx];
            $nome = strtoupper($poke['nome']);
            $anim = 'player-hit';
        } else {
            $poke = &$batalha['selvagem'];
            $nome = strtoupper($poke['nome']) . ' selvagem';
            $anim = 'wild-hit';
        }

        if ((int) $poke['hp_atual'] <= 0) {
            return;
        }

        $status = $poke['status'] ?? null;
        if ($status !== 'veneno' && $status !== 'queimadura') {
            return;
        }

        $dano = max(1, intdiv((int) $poke['hp_maximo'], 8));
        $poke['hp_atual'] = max(0, (int) $poke['hp_atual'] - $dano);

        $msg = $status === 'veneno'
            ? "{$nome} sofreu dano do veneno!"
            : "{$nome} sofreu dano da queimadura!";

        $eventos[] = [
            'mensagem' => $msg,
            'animacao' => $anim,
            'hud' => $this->montarSnapshotHud($batalha),
        ];
    }

    private function finalizarVerificacoesTurno(array &$batalha, array &$eventos): ?string
    {
        $ativoIdx = (int) $batalha['ativo_idx'];
        $jogador = &$batalha['equipe'][$ativoIdx];
        $selvagem = &$batalha['selvagem'];

        if ((int) $selvagem['hp_atual'] <= 0) {
            $batalha['finalizada'] = true;
            $batalha['resultado'] = 'vitoria';
            $nomeSelvagem = strtoupper($selvagem['nome']);
            $eventos[] = [
                'mensagem' => "{$nomeSelvagem} selvagem desmaiou!",
                'animacao' => 'wild-faint',
                'hud' => $this->montarSnapshotHud($batalha),
            ];
            $mapaRetorno = $batalha['mapa_retorno'] ?? 'mapaInicio';
            return '?modulo=mapa&acao=' . urlencode($mapaRetorno);
        }

        if ((int) $jogador['hp_atual'] <= 0) {
            $jogador['status'] = null;
            $jogador['turnos_sono'] = 0;
            $nomeJogador = strtoupper($jogador['nome']);
            $eventos[] = [
                'mensagem' => "{$nomeJogador} desmaiou!",
                'animacao' => 'player-faint',
                'hud' => $this->montarSnapshotHud($batalha),
            ];

            $proximoVivo = $this->encontrarPrimeiroVivo($batalha['equipe']);
            if ($proximoVivo === null) {
                $batalha['finalizada'] = true;
                $batalha['resultado'] = 'derrota';
                $eventos[] = [
                    'mensagem' => 'Você ficou sem Pokémon utilizáveis! Correndo para o Centro Pokémon...',
                    'animacao' => null,
                    'hud' => $this->montarSnapshotHud($batalha),
                ];
                return '?modulo=pokemon&acao=sus';
            }

            $batalha['troca_obrigatoria'] = true;
        }

        return null;
    }

    private function calcularEficaciaTipo(int $idTipoAtaque, array $tiposDefensor): float
    {
        $multiplicador = 1.0;

        foreach ($tiposDefensor as $tipoDef) {
            if (in_array($idTipoAtaque, $tipoDef['imunidade'], true)) {
                return 0.0;
            }
            if (in_array($idTipoAtaque, $tipoDef['frq_q'], true)) {
                $multiplicador *= 4.0;
            } elseif (in_array($idTipoAtaque, $tipoDef['frq_d'], true)) {
                $multiplicador *= 2.0;
            } elseif (in_array($idTipoAtaque, $tipoDef['res_q'], true)) {
                $multiplicador *= 0.25;
            } elseif (in_array($idTipoAtaque, $tipoDef['res_d'], true)) {
                $multiplicador *= 0.5;
            }
        }

        return $multiplicador;
    }

    private function sincronizarEquipeNoBanco(array $equipe): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE pokemon SET hp_atual = :hp_atual, status = :status WHERE id_pokemon = :id_pokemon"
        );
        foreach ($equipe as $poke) {
            $stmt->execute([
                'hp_atual' => max(0, (int) $poke['hp_atual']),
                'status' => $poke['status'] ?: null,
                'id_pokemon' => (int) $poke['id_pokemon'],
            ]);
        }
    }

    private function montarSnapshotHud(array $batalha): array
    {
        $ativo = $batalha['equipe'][$batalha['ativo_idx']];
        $selvagem = $batalha['selvagem'];

        return [
            'jogador' => [
                'id_pokemon' => $ativo['id_pokemon'],
                'id_pokedex' => $ativo['id_pokedex'],
                'nome' => strtoupper($ativo['nome']),
                'hp_atual' => $ativo['hp_atual'],
                'hp_maximo' => $ativo['hp_maximo'],
                'status' => $ativo['status'],
            ],
            'selvagem' => [
                'id_pokedex' => $selvagem['id_pokedex'],
                'nome' => strtoupper($selvagem['nome']),
                'hp_atual' => $selvagem['hp_atual'],
                'hp_maximo' => $selvagem['hp_maximo'],
                'status' => $selvagem['status'],
            ],
        ];
    }

    private function montarEstadoPublico(array $batalha): array
    {
        $ativoIdx = (int) $batalha['ativo_idx'];
        $ativo = $batalha['equipe'][$ativoIdx];
        $selvagem = $batalha['selvagem'];

        $equipePublica = [];
        foreach ($batalha['equipe'] as $idx => $poke) {
            $equipePublica[] = [
                'id_pokemon' => $poke['id_pokemon'],
                'id_pokedex' => $poke['id_pokedex'],
                'nome' => strtoupper($poke['nome']),
                'hp_atual' => $poke['hp_atual'],
                'hp_maximo' => $poke['hp_maximo'],
                'status' => $poke['status'],
                'ativo' => $idx === $ativoIdx,
            ];
        }

        return [
            'mapa_retorno' => $batalha['mapa_retorno'] ?? 'mapaInicio',
            'troca_obrigatoria' => !empty($batalha['troca_obrigatoria']),
            'finalizada' => !empty($batalha['finalizada']),
            'resultado' => $batalha['resultado'] ?? null,
            'jogador' => [
                'id_pokemon' => $ativo['id_pokemon'],
                'id_pokedex' => $ativo['id_pokedex'],
                'nome' => strtoupper($ativo['nome']),
                'hp_atual' => $ativo['hp_atual'],
                'hp_maximo' => $ativo['hp_maximo'],
                'status' => $ativo['status'],
                'movimentos' => $ativo['movimentos'],
            ],
            'selvagem' => [
                'id_pokedex' => $selvagem['id_pokedex'],
                'nome' => strtoupper($selvagem['nome']),
                'hp_atual' => $selvagem['hp_atual'],
                'hp_maximo' => $selvagem['hp_maximo'],
                'status' => $selvagem['status'],
            ],
            'equipe' => $equipePublica,
        ];
    }
}
