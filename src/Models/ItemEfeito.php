<?php

// Catálogo de efeitos dos itens e regras puras (sem banco).
// Trabalha sobre o array do Pokémon usado na batalha e na bolsa: hp_atual, hp_maximo, status, turnos_sono.
class ItemEfeito
{
    public const CATEGORIA_CONSUMIVEL = 1;
    public const CATEGORIA_POKEBOLA = 2;
    public const CATEGORIA_TM = 3;

    public const ID_EVOLUTION_STONE = 814;
    public const OFFSET_TM = 1000; // id_item do TM = 1000 + id_movimento

    // hp: pontos curados ('max' = tudo) | status: lista de status curados ('todos' = qualquer um)
    // reviver: fração do HP máximo ao reviver | bola: multiplicador de captura ('master' = sempre captura)
    public const EFEITOS = [
        1  => ['tipo' => 'pokebola', 'bola' => 'master'], // Master Ball
        2  => ['tipo' => 'pokebola', 'bola' => 2.0],      // Ultra Ball
        3  => ['tipo' => 'pokebola', 'bola' => 1.5],      // Great Ball
        4  => ['tipo' => 'pokebola', 'bola' => 1.0],      // Poké Ball
        17 => ['tipo' => 'cura', 'hp' => 20],             // Potion
        18 => ['tipo' => 'cura', 'status' => ['veneno']],       // Antidote
        19 => ['tipo' => 'cura', 'status' => ['queimadura']],   // Burn Heal
        20 => ['tipo' => 'cura', 'status' => ['congelamento']], // Ice Heal
        21 => ['tipo' => 'cura', 'status' => ['sono']],         // Awakening
        22 => ['tipo' => 'cura', 'status' => ['paralisia']],    // Paralyze Heal
        23 => ['tipo' => 'cura', 'hp' => 'max', 'status' => 'todos'], // Full Restore
        24 => ['tipo' => 'cura', 'hp' => 'max'],          // Max Potion
        25 => ['tipo' => 'cura', 'hp' => 200],            // Hyper Potion
        26 => ['tipo' => 'cura', 'hp' => 50],             // Super Potion
        27 => ['tipo' => 'cura', 'status' => 'todos'],    // Full Heal
        28 => ['tipo' => 'reviver', 'reviver' => 0.5],    // Revive
        29 => ['tipo' => 'reviver', 'reviver' => 1.0],    // Max Revive
        self::ID_EVOLUTION_STONE => ['tipo' => 'evolucao'],
    ];

    // Taxa base de captura pela fase evolutiva (não existe catch rate na pokédex)
    private const TAXA_CAPTURA_POR_FASE = [1 => 190, 2 => 90, 3 => 45];

    public static function efeito(int $idItem): ?array
    {
        if (isset(self::EFEITOS[$idItem])) {
            return self::EFEITOS[$idItem];
        }
        if ($idItem > self::OFFSET_TM) {
            return ['tipo' => 'tm', 'id_movimento' => $idItem - self::OFFSET_TM];
        }
        return null;
    }

    public static function ehTM(int $idItem): bool
    {
        return (self::efeito($idItem)['tipo'] ?? null) === 'tm';
    }

    public static function ehPokebola(int $idItem): bool
    {
        return (self::efeito($idItem)['tipo'] ?? null) === 'pokebola';
    }

    // Itens que fazem algo dentro da batalha: cura/reviver sempre, Pokébola só no matinho
    public static function usavelEmBatalha(int $idItem): bool
    {
        $tipo = self::efeito($idItem)['tipo'] ?? null;
        return in_array($tipo, ['cura', 'reviver', 'pokebola'], true);
    }

    public static function permitidoNaBatalha(int $idItem, string $tipoBatalha): bool
    {
        if (!self::usavelEmBatalha($idItem)) {
            return false;
        }
        return !self::ehPokebola($idItem) || $tipoBatalha === 'matinho';
    }

    // Fora da batalha: cura, reviver, TM e pedra de evolução (Pokébola não)
    public static function usavelForaDeBatalha(int $idItem): bool
    {
        $tipo = self::efeito($idItem)['tipo'] ?? null;
        return in_array($tipo, ['cura', 'reviver', 'tm', 'evolucao'], true);
    }

    // Aplica cura/reviver no Pokémon. Retorna ['ok' => bool, 'mensagens' => [...]] (ou 'erro' se não puder usar)
    public static function aplicarConsumivel(array &$poke, int $idItem): array
    {
        $efeito = self::efeito($idItem);
        $nome = strtoupper((string) $poke['nome']);
        $hpMax = max(1, (int) $poke['hp_maximo']);
        $hpAtual = max(0, (int) $poke['hp_atual']);
        $status = !empty($poke['status']) ? strtolower((string) $poke['status']) : null;

        if (!$efeito || !in_array($efeito['tipo'], ['cura', 'reviver'], true)) {
            return ['ok' => false, 'erro' => 'Esse item não pode ser usado em um Pokémon.'];
        }

        if ($efeito['tipo'] === 'reviver') {
            if ($hpAtual > 0) {
                return ['ok' => false, 'erro' => "{$nome} não está desmaiado!"];
            }
            $poke['hp_atual'] = max(1, (int) floor($hpMax * $efeito['reviver']));
            $poke['status'] = null;
            $poke['turnos_sono'] = 0;
            return ['ok' => true, 'mensagens' => ["{$nome} foi revivido!"]];
        }

        if ($hpAtual <= 0) {
            return ['ok' => false, 'erro' => "{$nome} está desmaiado! Use um Revive."];
        }

        $curaHp = $efeito['hp'] ?? null;
        $curaStatus = $efeito['status'] ?? null;

        $precisaHp = $curaHp !== null && $hpAtual < $hpMax;
        $precisaStatus = $status !== null && $curaStatus !== null
            && ($curaStatus === 'todos' || in_array($status, $curaStatus, true));

        if (!$precisaHp && !$precisaStatus) {
            return ['ok' => false, 'erro' => 'Não terá nenhum efeito.'];
        }

        $mensagens = [];
        if ($precisaHp) {
            $novoHp = $curaHp === 'max' ? $hpMax : min($hpMax, $hpAtual + (int) $curaHp);
            $poke['hp_atual'] = $novoHp;
            $mensagens[] = "O HP de {$nome} foi restaurado em " . ($novoHp - $hpAtual) . ' pontos!';
        }
        if ($precisaStatus) {
            $poke['status'] = null;
            $poke['turnos_sono'] = 0;
            $mensagens[] = match ($status) {
                'veneno' => "{$nome} foi curado do envenenamento!",
                'paralisia' => "{$nome} foi curado da paralisia!",
                'queimadura' => "A queimadura de {$nome} foi curada!",
                'sono' => "{$nome} acordou!",
                'congelamento' => "{$nome} descongelou!",
                default => "{$nome} foi curado!",
            };
        }

        return ['ok' => true, 'mensagens' => $mensagens];
    }

    // Fórmula de captura da Gen 3. Retorna ['capturou' => bool, 'balancadas' => 0..3]
    public static function tentarCaptura(array $selvagem, int $idItem): array
    {
        $bola = self::efeito($idItem)['bola'] ?? 1.0;
        if ($bola === 'master') {
            return ['capturou' => true, 'balancadas' => 3];
        }

        $hpMax = max(1, (int) $selvagem['hp_maximo']);
        $hpAtual = max(1, (int) $selvagem['hp_atual']);
        $taxa = self::TAXA_CAPTURA_POR_FASE[(int) ($selvagem['fase'] ?? 1)] ?? 190;

        $status = $selvagem['status'] ?? null;
        $bonusStatus = match ($status) {
            'sono', 'congelamento' => 2.0,
            'veneno', 'paralisia', 'queimadura' => 1.5,
            default => 1.0,
        };

        $a = (int) floor((((3 * $hpMax) - (2 * $hpAtual)) * $taxa * (float) $bola) / (3 * $hpMax) * $bonusStatus);
        if ($a >= 255) {
            return ['capturou' => true, 'balancadas' => 3];
        }

        $a = max(1, $a);
        $b = (int) floor(1048560 / sqrt(sqrt(16711680 / $a)));

        $balancadas = 0;
        for ($i = 0; $i < 4; $i++) {
            if (random_int(0, 65535) >= $b) {
                return ['capturou' => false, 'balancadas' => min(3, $balancadas)];
            }
            $balancadas++;
        }

        return ['capturou' => true, 'balancadas' => 3];
    }
}
