<?php

require_once __DIR__ . '/../Utils/tmImagens.php';

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
        'mapa9' => 10,
    ];

    // Parâmetro ?treinador= dos links dos mapas -> papel do adversário no ginásio
    private const PAPEL_POR_NUMERO = [
        1 => 'interno1',
        2 => 'interno2',
        3 => 'lider',
        4 => 'externo',
    ];

    private const PAPEL_ELITE_POR_NUMERO = [
        1 => 'interno1',
        2 => 'interno2',
        3 => 'interno3',
        4 => 'interno4',
    ];

    public function __construct($pdo)
    {
        $this->pdo = $pdo;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function batalhaTreinador()
    {
        $_SESSION['batalha'] = 'batalhaTreinador';
        $idTreinador = $_SESSION['usuario_logado']['id_treinador'] ?? null;
        if (!$idTreinador) {
            header("Location: ?modulo=treinador&acao=cadastroLogin");
            exit;
        }

        // Se já existe uma batalha contra treinador em andamento, retoma a mesma luta ao recarregar
        $batalhaExistente = $this->obterBatalhaAtiva();
        if (
            $batalhaExistente
            && $batalhaExistente['tipo'] === 'treinador'
            && (int) $batalhaExistente['id_treinador'] === (int) $idTreinador
        ) {
            $estadoInicial = $this->montarEstadoPublico($batalhaExistente);
            require_once __DIR__ . '/../Views/batalha/batalhaTreinador.php';
            return;
        }

        $numeroGinasio = (int) ($_GET['ginasio'] ?? 0);
        if ($numeroGinasio === 9 && isset($_GET['elite'])) {
            $papel = self::PAPEL_ELITE_POR_NUMERO[(int) $_GET['elite']] ?? null;
        } else {
            $papel = self::PAPEL_POR_NUMERO[(int) ($_GET['treinador'] ?? 0)] ?? null;
        }
        $ginasio = Treinador::GINASIOS[$numeroGinasio] ?? null;
        if (!$ginasio || !$papel) {
            header("Location: ?modulo=mapa&acao=mapaInicio");
            exit;
        }

        $desafioElite = $numeroGinasio === 9 && in_array(
            $papel,
            ['interno1', 'interno2', 'interno3', 'interno4'],
            true
        );
        if ($papel === 'externo' || $desafioElite) {
            $mapaRetorno = $ginasio['mapa'];
        } else {
            $mapaRetorno = $ginasio['ginasio'];
        }

        // Progressão: só pode desafiar quem está liberado e ainda não foi vencido (exceto o externo)
        $treinadorModel = new Treinador($this->pdo);
        if ($treinadorModel->statusAdversario($idTreinador, $numeroGinasio, $papel) !== 'disponivel') {
            header("Location: ?modulo=mapa&acao=" . urlencode($mapaRetorno));
            exit;
        }

        $idAdversario = Treinador::idAdversario($numeroGinasio, $papel);
        if ($idAdversario === null) {
            header("Location: ?modulo=mapa&acao=" . urlencode($mapaRetorno));
            exit;
        }

        $dadosAdversario = $treinadorModel->buscarPorId($idAdversario);
        if (!$dadosAdversario) {
            header("Location: ?modulo=mapa&acao=" . urlencode($mapaRetorno));
            exit;
        }

        $equipe = $this->carregarEquipeTreinador((int) $idTreinador);
        $ativoIdx = empty($equipe) ? null : $this->encontrarPrimeiroVivo($equipe);
        if ($ativoIdx === null) {
            header("Location: ?modulo=pokemon&acao=sus");
            exit;
        }

        // O time do NPC sempre começa curado e sem status; nunca é gravado no banco
        $equipeOponente = $this->carregarEquipeTreinador($idAdversario);
        if (empty($equipeOponente)) {
            header("Location: ?modulo=mapa&acao=" . urlencode($mapaRetorno));
            exit;
        }
        foreach ($equipeOponente as &$pokeOponente) {
            $pokeOponente['hp_atual'] = $pokeOponente['hp_maximo'];
            $pokeOponente['status'] = null;
            $pokeOponente['turnos_sono'] = 0;
        }
        unset($pokeOponente);

        $nomeExibicao = strtoupper($dadosAdversario['nome']);
        if ($papel === 'lider' && $numeroGinasio === 9) {
            $nomeExibicao = 'CAMPEÃO ' . $nomeExibicao;
        } elseif ($papel === 'lider') {
            $nomeExibicao = 'LÍDER ' . $nomeExibicao;
        }

        $batalha = [
            'tipo' => 'treinador',
            'id_treinador' => (int) $idTreinador,
            'mapa_retorno' => $mapaRetorno,
            'equipe' => $equipe,
            'ativo_idx' => $ativoIdx,
            'equipe_oponente' => $equipeOponente,
            'oponente_idx' => 0,
            'adversario' => [
                'id' => (int) $idAdversario,
                'nome' => $dadosAdversario['nome'],
                'nome_exibicao' => $nomeExibicao,
                'recompensa' => max(0, (int) $dadosAdversario['dinheiro']),
                'ginasio' => $numeroGinasio,
                'papel' => $papel,
            ],
            'tentativas_fuga' => 0,
            'troca_obrigatoria' => false,
            'finalizada' => false,
            'resultado' => null,
        ];
        $_SESSION['batalha_atual'] = $batalha;

        $estadoInicial = $this->montarEstadoPublico($batalha);
        $estadoInicial['eventos_iniciais'] = [
            [
                'mensagem' => "{$nomeExibicao} quer batalhar!",
                'animacao' => null,
                'hud' => $this->montarSnapshotHud($batalha),
            ],
            [
                'mensagem' => "{$nomeExibicao} enviou " . strtoupper($equipeOponente[0]['nome']) . '!',
                'animacao' => null,
                'hud' => $this->montarSnapshotHud($batalha),
            ],
        ];

        require_once __DIR__ . '/../Views/batalha/batalhaTreinador.php';
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
        $batalhaExistente = $this->obterBatalhaAtiva();
        if (
            $batalhaExistente
            && $batalhaExistente['tipo'] === 'matinho'
            && (int) $batalhaExistente['id_treinador'] === (int) $idTreinador
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
            'equipe_oponente' => [$selvagem],
            'oponente_idx' => 0,
            'adversario' => null,
            'tentativas_fuga' => 0,
            'troca_obrigatoria' => false,
            'finalizada' => false,
            'resultado' => null,
        ];

        $estadoInicial = $this->montarEstadoPublico($_SESSION['batalha_atual']);
        require_once __DIR__ . '/../Views/batalha/batalhaMatinho.php';
    }

    // Usa um item da bolsa durante a batalha (cura/reviver em qualquer Pokémon da equipe, Pokébola só no matinho).
    // Gasta o turno: depois do item, o oponente ataca.
    public function usarItem()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'erro' => 'Método não permitido.']);
            return;
        }

        $batalha = $this->obterBatalhaAtiva();
        if (!$batalha) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => 'Nenhuma batalha ativa encontrada.']);
            return;
        }

        if (!empty($batalha['troca_obrigatoria'])) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => 'Escolha um Pokémon antes de usar um item.']);
            return;
        }

        $payload = $this->lerJsonBody();
        $idItem = (int) ($payload['id_item'] ?? 0);
        $idTreinador = (int) $batalha['id_treinador'];

        $item = $this->buscarItemDoTreinador($idTreinador, $idItem);
        if (!$item) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => 'Você não tem esse item.']);
            return;
        }

        if (ItemEfeito::ehPokebola($idItem) && $batalha['tipo'] !== 'matinho') {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => 'Não dá pra capturar o Pokémon de outro treinador!']);
            return;
        }

        if (!ItemEfeito::permitidoNaBatalha($idItem, $batalha['tipo'])) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => 'Esse item não pode ser usado em batalha.']);
            return;
        }

        $nomeItem = strtoupper($item['nome']);
        $eventos = [];
        $redirect = null;

        if (ItemEfeito::ehPokebola($idItem)) {
            $this->consumirItem($idTreinador, $idItem);
            $redirect = $this->arremessarPokebola($batalha, $idItem, $nomeItem, $eventos);
        } else {
            $idPokemonAlvo = (int) ($payload['id_pokemon'] ?? 0);
            $alvoIdx = null;
            foreach ($batalha['equipe'] as $idx => $poke) {
                if ((int) $poke['id_pokemon'] === $idPokemonAlvo) {
                    $alvoIdx = $idx;
                    break;
                }
            }
            if ($alvoIdx === null) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'erro' => 'Escolha um Pokémon da sua equipe.']);
                return;
            }

            $resultado = ItemEfeito::aplicarConsumivel($batalha['equipe'][$alvoIdx], $idItem);
            if (!$resultado['ok']) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'erro' => $resultado['erro']]);
                return;
            }

            $this->consumirItem($idTreinador, $idItem);

            $nomeAlvo = strtoupper($batalha['equipe'][$alvoIdx]['nome']);
            $eventos[] = [
                'mensagem' => "Você usou {$nomeItem} em {$nomeAlvo}!",
                'animacao' => null,
                'hud' => $this->montarSnapshotHud($batalha),
            ];
            foreach ($resultado['mensagens'] as $msg) {
                $eventos[] = [
                    'mensagem' => $msg,
                    'animacao' => null,
                    'hud' => $this->montarSnapshotHud($batalha),
                ];
            }

            $this->turnoDoOponente($batalha, $eventos, $redirect);
        }

        $this->sincronizarEquipeNoBanco($batalha['equipe']);
        $this->salvarBatalha($batalha);

        echo json_encode([
            'ok' => true,
            'eventos' => $eventos,
            'estado' => $this->montarEstadoPublico($batalha),
            'redirect' => $redirect,
        ]);
    }

    // Oponente ataca + dano de fim de turno + desmaios (usado quando o jogador gastou o turno sem atacar)
    private function turnoDoOponente(array &$batalha, array &$eventos, ?string &$redirect): void
    {
        if ((int) $this->oponenteAtivo($batalha)['hp_atual'] <= 0) {
            return;
        }
        $movOponente = $this->escolherMovimentoOponente($this->oponenteAtivo($batalha));
        $this->executarAtaque($batalha, 'oponente', $movOponente, $eventos);
        $this->aplicarFimDeTurno($batalha, $eventos);
        $redirect = $this->finalizarVerificacoesTurno($batalha, $eventos);
    }

    // Arremessa a Pokébola no selvagem; retorna o redirect se capturou (ou se a batalha acabou no contra-ataque)
    private function arremessarPokebola(array &$batalha, int $idItem, string $nomeItem, array &$eventos): ?string
    {
        $selvagem = $this->oponenteAtivo($batalha);
        $nomeSelvagem = $this->nomeOponente($batalha);
        $resultado = ItemEfeito::tentarCaptura($selvagem, $idItem);
        $bolaUrl = urlImagemItem($idItem, $nomeItem);

        $eventos[] = [
            'mensagem' => "Você arremessou uma {$nomeItem}!",
            'animacao' => 'ball-throw',
            'bola_url' => $bolaUrl,
            'hud' => $this->montarSnapshotHud($batalha),
        ];
        for ($i = 0; $i < $resultado['balancadas']; $i++) {
            $eventos[] = [
                'mensagem' => null,
                'animacao' => 'ball-shake',
                'hud' => $this->montarSnapshotHud($batalha),
            ];
        }

        if (!$resultado['capturou']) {
            $mensagem = match ($resultado['balancadas']) {
                0 => 'Ah não! O Pokémon escapou!',
                1 => 'Aaah! Parecia que tinha pegado!',
                2 => 'Argh! Quase!',
                default => 'Droga! Foi por pouco!',
            };
            $eventos[] = [
                'mensagem' => $mensagem,
                'animacao' => 'ball-break',
                'hud' => $this->montarSnapshotHud($batalha),
            ];

            $redirect = null;
            $this->turnoDoOponente($batalha, $eventos, $redirect);
            return $redirect;
        }

        $eventos[] = [
            'mensagem' => "Gotcha! " . strtoupper($selvagem['nome']) . " foi capturado!",
            'animacao' => 'capture-ok',
            'hud' => $this->montarSnapshotHud($batalha),
        ];

        $foiParaEquipe = $this->registrarCaptura((int) $batalha['id_treinador'], $selvagem);
        if (!$foiParaEquipe) {
            $eventos[] = [
                'mensagem' => strtoupper($selvagem['nome']) . ' foi enviado para o PC.',
                'animacao' => null,
                'hud' => $this->montarSnapshotHud($batalha),
            ];
        }

        $batalha['finalizada'] = true;
        $batalha['resultado'] = 'captura';
        return '?modulo=mapa&acao=' . urlencode($batalha['mapa_retorno'] ?? 'mapaInicio');
    }

    // Grava o Pokémon capturado (HP/status atuais e os golpes que ele usava). Retorna true se entrou na equipe, false se foi pro box
    private function registrarCaptura(int $idTreinador, array $selvagem): bool
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO pokemon (id_treinador, id_pokedex, nome, hp_atual, sexo, status)
                 VALUES (:id_treinador, :id_pokedex, :nome, :hp_atual, :sexo, :status)"
            );
            $stmt->execute([
                'id_treinador' => $idTreinador,
                'id_pokedex' => (int) $selvagem['id_pokedex'],
                'nome' => $selvagem['nome'],
                'hp_atual' => max(1, (int) $selvagem['hp_atual']),
                'sexo' => random_int(0, 1) === 0 ? 'Masculino' : 'Feminino',
                'status' => $selvagem['status'] ?: null,
            ]);
            $idPokemon = (int) $this->pdo->lastInsertId();

            $stmtMov = $this->pdo->prepare(
                "INSERT INTO pokemon_movimento (id_pokemon, id_movimento) VALUES (:id_pokemon, :id_movimento)"
            );
            foreach ($selvagem['movimentos'] as $mov) {
                $stmtMov->execute(['id_pokemon' => $idPokemon, 'id_movimento' => (int) $mov['id_movimento']]);
            }

            $stmtSlots = $this->pdo->prepare(
                "SELECT slot1, slot2, slot3, slot4, slot5, slot6 FROM treinador WHERE id_treinador = :id FOR UPDATE"
            );
            $stmtSlots->execute(['id' => $idTreinador]);
            $slots = $stmtSlots->fetch(PDO::FETCH_ASSOC) ?: [];

            $slotLivre = null;
            for ($i = 1; $i <= 6; $i++) {
                if (empty($slots["slot{$i}"])) {
                    $slotLivre = $i;
                    break;
                }
            }

            if ($slotLivre !== null) {
                $this->pdo->prepare("UPDATE treinador SET slot{$slotLivre} = :id_pokemon WHERE id_treinador = :id")
                    ->execute(['id_pokemon' => $idPokemon, 'id' => $idTreinador]);
            }

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        (new Treinador($this->pdo))->recarregarSessao($idTreinador);
        return $slotLivre !== null;
    }

    private function buscarItemDoTreinador(int $idTreinador, int $idItem): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT i.id_item, i.nome, i.descricao, i.id_categoria, ti.quantidade
             FROM treinador_item ti
             INNER JOIN item i ON i.id_item = ti.id_item
             WHERE ti.id_treinador = :id_treinador AND ti.id_item = :id_item AND ti.quantidade > 0"
        );
        $stmt->execute(['id_treinador' => $idTreinador, 'id_item' => $idItem]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function consumirItem(int $idTreinador, int $idItem): void
    {
        (new TreinadorItem($this->pdo))->removerItem($idTreinador, $idItem, 1);
    }

    // Itens que aparecem na bolsa da batalha (cura, reviver e Pokébolas), com flag se podem ser usados nesta batalha
    private function listarItensBatalha(array $batalha): array
    {
        $itens = (new TreinadorItem($this->pdo))->buscarItensTreinador((int) $batalha['id_treinador']);
        $lista = [];
        foreach ($itens as $item) {
            $idItem = (int) $item['id_item'];
            if (!ItemEfeito::usavelEmBatalha($idItem)) {
                continue;
            }
            $efeito = ItemEfeito::efeito($idItem);
            $lista[] = [
                'id_item' => $idItem,
                'nome' => $item['nome'],
                'descricao' => $item['descricao'],
                'quantidade' => (int) $item['quantidade'],
                'tipo' => $efeito['tipo'],
                'imagem' => urlImagemItem($idItem, (string) $item['nome']),
                'permitido' => ItemEfeito::permitidoNaBatalha($idItem, $batalha['tipo']),
            ];
        }
        return $lista;
    }

    public function atacar()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'erro' => 'Método não permitido.']);
            return;
        }

        try {
            $batalha = $this->obterBatalhaAtiva();
            if (!$batalha) {
                throw new BatalhaException('Nenhuma batalha ativa encontrada.');
            }

            if (!empty($batalha['troca_obrigatoria'])) {
                throw new BatalhaException('Você precisa escolher outro Pokémon antes de atacar.');
            }

            $payload = $this->lerJsonBody();
            $idMovimento = isset($payload['id_movimento']) ? (int) $payload['id_movimento'] : 0;

            $ativoIdx = (int) $batalha['ativo_idx'];
            $jogador = $batalha['equipe'][$ativoIdx];
            $oponente = $this->oponenteAtivo($batalha);

            $movJogador = null;
            foreach ($jogador['movimentos'] as $mov) {
                if ((int) $mov['id_movimento'] === $idMovimento) {
                    $movJogador = $mov;
                    break;
                }
            }

            if (!$movJogador) {
                throw new BatalhaException('Movimento inválido para este Pokémon.');
            }

            $movOponente = $this->escolherMovimentoOponente($oponente);

            $eventos = [];
            $jogadorPrimeiro = $this->jogadorAgePrimeiro($jogador, $movJogador, $oponente, $movOponente);

            if ($jogadorPrimeiro) {
                $this->executarAtaque($batalha, 'jogador', $movJogador, $eventos);
                if ($this->ambosVivos($batalha)) {
                    $this->executarAtaque($batalha, 'oponente', $movOponente, $eventos);
                }
            } else {
                $this->executarAtaque($batalha, 'oponente', $movOponente, $eventos);
                if ($this->ambosVivos($batalha)) {
                    $this->executarAtaque($batalha, 'jogador', $movJogador, $eventos);
                }
            }

            $this->aplicarFimDeTurno($batalha, $eventos);

            $redirect = $this->finalizarVerificacoesTurno($batalha, $eventos);
            $this->sincronizarEquipeNoBanco($batalha['equipe']);
            $this->salvarBatalha($batalha);

            echo json_encode([
                'ok' => true,
                'eventos' => $eventos,
                'estado' => $this->montarEstadoPublico($batalha),
                'redirect' => $redirect,
            ]);
        } catch (BatalhaException $e) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function registrarLogBatalha()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'erro' => 'Método não permitido.']);
            return;
        }

        $idTreinador = (int) ($_SESSION['usuario_logado']['id_treinador'] ?? 0);
        if ($idTreinador <= 0) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'erro' => 'Não autenticado.']);
            return;
        }

        $payload = $this->lerJsonBody();
        $adversario = trim($payload['adversario'] ?? '');
        $pokemonAtacante = trim($payload['pokemon_atacante'] ?? '');
        $pokemonDefensor = trim($payload['pokemon_defensor'] ?? '');
        $ataque = trim($payload['ataque'] ?? '');
        $efeito = trim($payload['efeito'] ?? '');

        if ($adversario === '' || $pokemonAtacante === '' || $ataque === '') {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => 'Dados insuficientes para log.']);
            return;
        }

        $texto = "Batalha vs {$adversario}: {$pokemonAtacante} usou {$ataque}";
        if ($pokemonDefensor !== '') {
            $texto .= " contra {$pokemonDefensor}";
        }
        if ($efeito !== '') {
            $texto .= " (Efeito: {$efeito})";
        }

        // Garante limite de 250 caracteres do campo log.texto
        if (mb_strlen($texto, 'UTF-8') > 250) {
            $texto = mb_substr($texto, 0, 247, 'UTF-8') . '...';
        }

        try {
            $stmt = $this->pdo->prepare("INSERT INTO log (id_treinador, texto, data) VALUES (:id_treinador, :texto, NOW())");
            $stmt->execute([
                'id_treinador' => $idTreinador,
                'texto' => $texto,
            ]);
            echo json_encode(['ok' => true]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function trocar()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'erro' => 'Método não permitido.']);
            return;
        }

        try {
            $batalha = $this->obterBatalhaAtiva();
            if (!$batalha) {
                throw new BatalhaException('Nenhuma batalha ativa encontrada.');
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
                throw new BatalhaException('Pokémon não encontrado na equipe.');
            }

            if ($novoIdx === (int) $batalha['ativo_idx']) {
                throw new BatalhaException('Este Pokémon já está em batalha.');
            }

            if ((int) $batalha['equipe'][$novoIdx]['hp_atual'] <= 0) {
                throw new BatalhaException('Este Pokémon está desmaiado e não pode lutar.');
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

            // Troca voluntária consome o turno: o oponente ataca
            if (!$eraTrocaObrigatoria && $this->oponenteAtivo($batalha)['hp_atual'] > 0) {
                $movOponente = $this->escolherMovimentoOponente($this->oponenteAtivo($batalha));
                $this->executarAtaque($batalha, 'oponente', $movOponente, $eventos);
                $this->aplicarFimDeTurno($batalha, $eventos);

                $redirect = $this->finalizarVerificacoesTurno($batalha, $eventos);
                $this->sincronizarEquipeNoBanco($batalha['equipe']);
            }

            $this->salvarBatalha($batalha);

            echo json_encode([
                'ok' => true,
                'eventos' => $eventos,
                'estado' => $this->montarEstadoPublico($batalha),
                'redirect' => $redirect,
            ]);
        } catch (BatalhaException $e) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function fugir()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'erro' => 'Método não permitido.']);
            return;
        }

        $batalha = $this->obterBatalhaAtiva();
        if (!$batalha) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => 'Nenhuma batalha ativa encontrada.']);
            return;
        }

        // Não existe fuga em batalha contra treinador (e não gasta o turno)
        if ($batalha['tipo'] === 'treinador') {
            echo json_encode([
                'ok' => true,
                'eventos' => [[
                    'mensagem' => 'Não dá pra fugir de uma batalha contra treinador!',
                    'animacao' => null,
                    'hud' => $this->montarSnapshotHud($batalha),
                ]],
                'estado' => $this->montarEstadoPublico($batalha),
                'redirect' => null,
            ]);
            return;
        }

        if (!empty($batalha['troca_obrigatoria'])) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'erro' => 'Escolha um Pokémon antes de tentar fugir.']);
            return;
        }

        $batalha['tentativas_fuga'] = (int) ($batalha['tentativas_fuga'] ?? 0) + 1;
        $jogador = $batalha['equipe'][(int) $batalha['ativo_idx']];
        $oponente = $this->oponenteAtivo($batalha);

        $speJogador = $this->calcularVelocidadeEfetiva($jogador);
        $speOponente = $this->calcularVelocidadeEfetiva($oponente);

        // Fórmula oficial de fuga do Pokémon Emerald (Gen 3)
        if ($speJogador >= $speOponente) {
            $conseguiuFugir = true;
        } else {
            $chance = intdiv($speJogador * 128, max(1, $speOponente)) + (30 * $batalha['tentativas_fuga']);
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
        } else {
            $eventos[] = [
                'mensagem' => 'Não conseguiu fugir!',
                'animacao' => null,
                'hud' => $this->montarSnapshotHud($batalha),
            ];

            $this->executarAtaque($batalha, 'oponente', $this->escolherMovimentoOponente($oponente), $eventos);
            $this->aplicarFimDeTurno($batalha, $eventos);

            $redirect = $this->finalizarVerificacoesTurno($batalha, $eventos);
            $this->sincronizarEquipeNoBanco($batalha['equipe']);
        }

        $this->salvarBatalha($batalha);

        echo json_encode([
            'ok' => true,
            'eventos' => $eventos,
            'estado' => $this->montarEstadoPublico($batalha),
            'redirect' => $redirect,
        ]);
    }

    // ------------------------------------------------------------------
    // Estado da batalha
    // ------------------------------------------------------------------

    // Retorna a batalha em andamento da sessão (ou null). Batalhas salvas no formato antigo são descartadas.
    private function obterBatalhaAtiva(): ?array
    {
        $batalha = $_SESSION['batalha_atual'] ?? null;
        if (
            !is_array($batalha)
            || !empty($batalha['finalizada'])
            || empty($batalha['equipe_oponente'])
            || !isset($batalha['tipo'])
        ) {
            return null;
        }
        return $batalha;
    }

    private function salvarBatalha(array $batalha): void
    {
        if (!empty($batalha['finalizada'])) {
            unset($_SESSION['batalha_atual']);
        } else {
            $_SESSION['batalha_atual'] = $batalha;
        }
    }

    // Pokémon do oponente que está em campo (por referência, para poder alterar HP/status)
    private function &oponenteAtivo(array &$batalha): array
    {
        $idx = (int) $batalha['oponente_idx'];
        return $batalha['equipe_oponente'][$idx];
    }

    // "MUDKIP selvagem" no matinho, "MUDKIP inimigo" contra treinador
    private function nomeOponente(array $batalha): string
    {
        $nome = strtoupper($batalha['equipe_oponente'][(int) $batalha['oponente_idx']]['nome']);
        return $batalha['tipo'] === 'treinador' ? "{$nome} inimigo" : "{$nome} selvagem";
    }

    private function ambosVivos(array $batalha): bool
    {
        return (int) $batalha['equipe'][(int) $batalha['ativo_idx']]['hp_atual'] > 0
            && (int) $batalha['equipe_oponente'][(int) $batalha['oponente_idx']]['hp_atual'] > 0;
    }

    private function contarVivos(array $equipe): int
    {
        $vivos = 0;
        foreach ($equipe as $poke) {
            if ((int) $poke['hp_atual'] > 0) {
                $vivos++;
            }
        }
        return $vivos;
    }

    private function escolherMovimentoOponente(array $oponente): array
    {
        return $oponente['movimentos'][array_rand($oponente['movimentos'])];
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

    // ------------------------------------------------------------------
    // Carregamento de dados
    // ------------------------------------------------------------------

    // Serve tanto para o jogador quanto para os NPCs (o time vem dos slots do treinador)
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
            "SELECT pd.id_pokedex, pd.nome, pd.fase, pd.hp, pd.atk, pd.def, pd.spa, pd.spd, pd.spe
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
                "SELECT id_pokedex, nome, fase, hp, atk, def, spa, spd, spe FROM pokedex ORDER BY RAND() LIMIT 1"
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
            'fase' => (int) $dados['fase'],
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

    // ------------------------------------------------------------------
    // Combate
    // ------------------------------------------------------------------

    private function calcularVelocidadeEfetiva(array $poke): int
    {
        $spe = max(1, (int) $poke['spe']);
        if (($poke['status'] ?? null) === 'paralisia') {
            $spe = max(1, intdiv($spe, 2));
        }
        return $spe;
    }

    private function jogadorAgePrimeiro(array $jogador, array $movJ, array $oponente, array $movO): bool
    {
        if ((int) $movJ['prioridade'] !== (int) $movO['prioridade']) {
            return (int) $movJ['prioridade'] > (int) $movO['prioridade'];
        }

        $speJ = $this->calcularVelocidadeEfetiva($jogador);
        $speO = $this->calcularVelocidadeEfetiva($oponente);

        if ($speJ !== $speO) {
            return $speJ > $speO;
        }

        return random_int(0, 1) === 1;
    }

    private function executarAtaque(array &$batalha, string $ladoAtacante, array $movimento, array &$eventos): void
    {
        $ativoIdx = (int) $batalha['ativo_idx'];
        if ($ladoAtacante === 'jogador') {
            $atacante = &$batalha['equipe'][$ativoIdx];
            $defensor = &$this->oponenteAtivo($batalha);
            $nomeAtacante = strtoupper($atacante['nome']);
            $nomeDefensor = $this->nomeOponente($batalha);
            $animAtaque = 'player-attack';
            $animHit = 'wild-hit';
        } else {
            $atacante = &$this->oponenteAtivo($batalha);
            $defensor = &$batalha['equipe'][$ativoIdx];
            $nomeAtacante = $this->nomeOponente($batalha);
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

        // Evento visual de dano (atualiza barra de HP e pisca o sprite), com mensagem extra se houver
        $eventos[] = [
            'mensagem' => !empty($mensagensDano) ? implode(' ', $mensagensDano) : null,
            'animacao' => $animHit,
            'tipo_efeito' => $movimento['tipo_nome'] ?? null,
            'hud' => $this->montarSnapshotHud($batalha),
        ];

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

        // Retorna ação de ataque nos eventos para processamento e log no front-end
        $adversarioNome = $batalha['tipo'] === 'treinador'
            ? ($batalha['adversario']['nome_exibicao'] ?? $batalha['adversario']['nome'] ?? 'Treinador')
            : ($this->nomeOponente($batalha) . ' Selvagem');
        $efeitoNome = !empty($movimento['efeito_nome']) ? (string) $movimento['efeito_nome'] : null;

        $eventos[] = [
            'tipo_evento' => 'log_ataque',
            'log_dados' => [
                'adversario' => (string) $adversarioNome,
                'pokemon_atacante' => (string) $nomeAtacante,
                'pokemon_defensor' => (string) $nomeDefensor,
                'ataque' => (string) $movimento['nome'],
                'efeito' => $efeitoNome,
            ],
            'hud' => null,
            'animacao' => null,
            'mensagem' => null,
        ];
    }

    // Danos de status no fim do turno (veneno e queimadura), primeiro jogador e depois oponente
    private function aplicarFimDeTurno(array &$batalha, array &$eventos): void
    {
        if ($this->ambosVivos($batalha)) {
            $this->aplicarDanoFimDeTurno($batalha, 'jogador', $eventos);
        }
        if ($this->ambosVivos($batalha)) {
            $this->aplicarDanoFimDeTurno($batalha, 'oponente', $eventos);
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
            $poke = &$this->oponenteAtivo($batalha);
            $nome = $this->nomeOponente($batalha);
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

    // Verifica desmaios dos dois lados ao fim da ação; retorna a URL de redirect se a batalha acabou
    private function finalizarVerificacoesTurno(array &$batalha, array &$eventos): ?string
    {
        $ativoIdx = (int) $batalha['ativo_idx'];
        $ehTreinador = $batalha['tipo'] === 'treinador';
        $proximoOponente = null;

        // 1) Pokémon do oponente desmaiou
        if ((int) $this->oponenteAtivo($batalha)['hp_atual'] <= 0) {
            $eventos[] = [
                'mensagem' => $this->nomeOponente($batalha) . ' desmaiou!',
                'animacao' => 'wild-faint',
                'hud' => $this->montarSnapshotHud($batalha),
            ];

            $proximoOponente = $ehTreinador ? $this->encontrarPrimeiroVivo($batalha['equipe_oponente']) : null;
            if ($proximoOponente === null) {
                return $this->concluirVitoria($batalha, $eventos);
            }
        }

        // 2) Pokémon do jogador desmaiou
        if ((int) $batalha['equipe'][$ativoIdx]['hp_atual'] <= 0) {
            $batalha['equipe'][$ativoIdx]['status'] = null;
            $batalha['equipe'][$ativoIdx]['turnos_sono'] = 0;
            $nomeJogador = strtoupper($batalha['equipe'][$ativoIdx]['nome']);
            $eventos[] = [
                'mensagem' => "{$nomeJogador} desmaiou!",
                'animacao' => 'player-faint',
                'hud' => $this->montarSnapshotHud($batalha),
            ];

            if ($this->encontrarPrimeiroVivo($batalha['equipe']) === null) {
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

        // 3) Treinador manda o próximo Pokémon
        if ($proximoOponente !== null) {
            $batalha['oponente_idx'] = $proximoOponente;
            $nomeNovo = strtoupper($batalha['equipe_oponente'][$proximoOponente]['nome']);
            $eventos[] = [
                'mensagem' => "{$batalha['adversario']['nome_exibicao']} enviou {$nomeNovo}!",
                'animacao' => null,
                'hud' => $this->montarSnapshotHud($batalha),
            ];
        }

        return null;
    }

    private function concluirVitoria(array &$batalha, array &$eventos): string
    {
        $batalha['finalizada'] = true;
        $batalha['resultado'] = 'vitoria';
        $redirect = '?modulo=mapa&acao=' . urlencode($batalha['mapa_retorno'] ?? 'mapaInicio');

        if ($batalha['tipo'] !== 'treinador') {
            return $redirect;
        }

        $adversario = $batalha['adversario'];
        $idTreinador = (int) $batalha['id_treinador'];
        $treinadorModel = new Treinador($this->pdo);

        $treinadorModel->registrarVitoria($idTreinador, (int) $adversario['id'], (string) $adversario['nome']);
        $treinadorModel->adicionarDinheiro($idTreinador, (int) $adversario['recompensa']);
        $treinadorModel->recarregarSessao($idTreinador);

        $eventos[] = [
            'mensagem' => "Você derrotou {$adversario['nome_exibicao']}!",
            'animacao' => null,
            'hud' => $this->montarSnapshotHud($batalha),
        ];

        if ((int) $adversario['recompensa'] > 0) {
            $valor = number_format((int) $adversario['recompensa'], 0, ',', '.');
            $eventos[] = [
                'mensagem' => "Você ganhou R$ {$valor} pela vitória!",
                'animacao' => null,
                'hud' => $this->montarSnapshotHud($batalha),
            ];
        }

        if ($adversario['papel'] === 'lider') {
            if ((int) $adversario['ginasio'] === 9) {
                $mensagem = 'Você venceu o Campeão Wallace e conquistou a Liga Pokémon!';
            } else {
                $insignia = Treinador::GINASIOS[$adversario['ginasio']]['insignia'] ?? '';
                $mensagem = "Você recebeu a Insígnia {$insignia}! O caminho para a próxima cidade está livre!";
            }
            $eventos[] = [
                'mensagem' => $mensagem,
                'animacao' => null,
                'hud' => $this->montarSnapshotHud($batalha),
            ];
        }

        return $redirect;
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

    // Só a equipe do jogador é persistida; o time do NPC nunca é gravado
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

    // ------------------------------------------------------------------
    // Saída para o front
    // ------------------------------------------------------------------

    private function montarSnapshotHud(array $batalha): array
    {
        $ativo = $batalha['equipe'][$batalha['ativo_idx']];
        $oponente = $batalha['equipe_oponente'][(int) $batalha['oponente_idx']];

        return [
            'jogador' => [
                'id_pokemon' => $ativo['id_pokemon'],
                'id_pokedex' => $ativo['id_pokedex'],
                'nome' => strtoupper($ativo['nome']),
                'hp_atual' => $ativo['hp_atual'],
                'hp_maximo' => $ativo['hp_maximo'],
                'status' => $ativo['status'],
            ],
            'oponente' => [
                'id_pokedex' => $oponente['id_pokedex'],
                'nome' => strtoupper($oponente['nome']),
                'hp_atual' => $oponente['hp_atual'],
                'hp_maximo' => $oponente['hp_maximo'],
                'status' => $oponente['status'],
            ],
            'oponente_restantes' => $this->contarVivos($batalha['equipe_oponente']),
        ];
    }

    private function montarEstadoPublico(array $batalha): array
    {
        $ativoIdx = (int) $batalha['ativo_idx'];
        $ativo = $batalha['equipe'][$ativoIdx];
        $oponente = $batalha['equipe_oponente'][(int) $batalha['oponente_idx']];

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

        $adversario = null;
        if ($batalha['tipo'] === 'treinador' && !empty($batalha['adversario'])) {
            $adversario = [
                'nome' => $batalha['adversario']['nome_exibicao'],
                'total' => count($batalha['equipe_oponente']),
                'restantes' => $this->contarVivos($batalha['equipe_oponente']),
            ];
        }

        return [
            'tipo' => $batalha['tipo'],
            'mapa_retorno' => $batalha['mapa_retorno'] ?? 'mapaInicio',
            'troca_obrigatoria' => !empty($batalha['troca_obrigatoria']),
            'finalizada' => !empty($batalha['finalizada']),
            'resultado' => $batalha['resultado'] ?? null,
            'adversario' => $adversario,
            'eventos_iniciais' => [],
            'jogador' => [
                'id_pokemon' => $ativo['id_pokemon'],
                'id_pokedex' => $ativo['id_pokedex'],
                'nome' => strtoupper($ativo['nome']),
                'hp_atual' => $ativo['hp_atual'],
                'hp_maximo' => $ativo['hp_maximo'],
                'status' => $ativo['status'],
                'movimentos' => $ativo['movimentos'],
            ],
            'oponente' => [
                'id_pokedex' => $oponente['id_pokedex'],
                'nome' => strtoupper($oponente['nome']),
                'hp_atual' => $oponente['hp_atual'],
                'hp_maximo' => $oponente['hp_maximo'],
                'status' => $oponente['status'],
            ],
            'equipe' => $equipePublica,
            'itens' => $this->listarItensBatalha($batalha),
        ];
    }
}
