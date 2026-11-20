<?php
class Treinador {
    private $id_treinador;
    private $nome;
    private $email;
    private $senha;
    private $sexo;
    private $foto;
    private $dinheiro;
    private $slot1;
    private $slot2;
    private $slot3;
    private $slot4;
    private $slot5;
    private $slot6;
    private $pdo;
    private $camposAlterados = [];

    //o private pdo e essa função vai em todos
    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }
    public function getIdTreinador(){
        return $this->id_treinador;
    }
    public function getNome(){
        return $this->nome;
    }
    public function getEmail(){
        return $this->email;
    }
    public function getSenha(){
        return $this->senha;
    }
    public function getSexo(){
        return $this->sexo;
    }
    public function getFoto(){
        return $this->foto;
    }
    public function getDinheiro(){
        return $this->dinheiro;
    }
    public function getSlot1(){
        return $this->slot1;
    }
    public function getSlot2(){
        return $this->slot2;
    }
    public function getSlot3(){
        return $this->slot3;
    }
    public function getSlot4(){
        return $this->slot4;
    }
    public function getSlot5(){
        return $this->slot5;
    }
    public function getSlot6(){
        return $this->slot6;
    }
    public function setIdTreinador($id_treinador){
        $this->id_treinador = $id_treinador; 
    }
    public function setNome($nome){
        $this->nome = $nome; 
        $this->camposAlterados['nome'] = $nome;
    }
    public function setEmail($email){
        $this->email = $email; 
        $this->camposAlterados['email'] = $email;
    }
    public function setSenha($senha){
        $this->senha = $senha; 
        $this->camposAlterados['senha'] = $senha;
    }
    public function setSexo($sexo){
        $this->sexo = $sexo; 
        $this->camposAlterados['sexo'] = $sexo;
    }
    public function setFoto($foto){
        $this->foto = $foto; 
        $this->camposAlterados['foto'] = $foto;
    }
    public function setDinheiro($dinheiro){
        $this->dinheiro = $dinheiro; 
        $this->camposAlterados['dinheiro'] = $dinheiro;
    }
    public function setSlot1($slot1){
        $this->slot1 = $slot1; 
        $this->camposAlterados['slot1'] = $slot1;
    }
    public function setSlot2($slot2){
        $this->slot2 = $slot2; 
        $this->camposAlterados['slot2'] = $slot2;
    }
    public function setSlot3($slot3){
        $this->slot3 = $slot3; 
        $this->camposAlterados['slot3'] = $slot3;
    }
    public function setSlot4($slot4){
        $this->slot4 = $slot4; 
        $this->camposAlterados['slot4'] = $slot4;
    }
    public function setSlot5($slot5){
        $this->slot5 = $slot5; 
        $this->camposAlterados['slot5'] = $slot5;
    }
    public function setSlot6($slot6){
        $this->slot6 = $slot6; 
        $this->camposAlterados['slot6'] = $slot6;
    }

    public function salvar(){
        if ($this->id_treinador){
            // Se nenhum campo foi alterado via setter, não há nada para atualizar
            if (empty($this->camposAlterados)){
                return true;
            }

            $setClauses = [];
            $params = ['id_treinador' => $this->id_treinador];

            foreach ($this->camposAlterados as $campo => $valor) {
                $setClauses[] = "$campo = :$campo";
                $params[$campo] = $valor;
            }

            try {
                $sql = "UPDATE treinador SET " . implode(', ', $setClauses) . " WHERE id_treinador = :id_treinador";
                $stmt = $this->pdo->prepare($sql);
                $ok = $stmt->execute($params);

                if ($ok) {
                    $this->camposAlterados = []; // limpa campos alterados após salvar com sucesso
                }

                return $ok;
            } catch (PDOException $e) {
                return false;
            }
        }else{
            try {
                $sql = "INSERT INTO treinador (nome, email, senha, sexo, foto, dinheiro, slot1, slot2, slot3, slot4, slot5, slot6) VALUES (:nome, :email, :senha, :sexo, :foto, :dinheiro, :slot1, :slot2, :slot3, :slot4, :slot5, :slot6)";
                $stmt = $this->pdo->prepare($sql);
                $ok = $stmt->execute([
                    'nome' => $this->nome,
                    'email' => $this->email,
                    'senha' => $this->senha,
                    'sexo' => $this->sexo,
                    'foto' => $this->foto ?? 'fotoPerfilDefault.webp',
                    'dinheiro' => $this->dinheiro ?? 0,
                    'slot1' => $this->slot1,
                    'slot2' => $this->slot2,
                    'slot3' => $this->slot3,
                    'slot4' => $this->slot4,
                    'slot5' => $this->slot5,
                    'slot6' => $this->slot6
                ]);
                if ($ok){
                    $this->id_treinador = $this->pdo->lastInsertId();
                    $this->camposAlterados = [];
                }
                return $ok;
            } catch (PDOException $e) {
                return false;
            }
        }
    }

    public function emailExiste($email){
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM treinador WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    public function deletar(){
        if (!$this->id_treinador){
            return false;
        }
        $stmt = $this->pdo->prepare("DELETE FROM treinador WHERE id_treinador = :id_treinador");
        return $stmt->execute([
            'id_treinador' => $this->id_treinador
        ]);
    }

    public function atualizarSlot1($id_treinador, $slot1){
        $stmt = $this->pdo->prepare("UPDATE treinador SET slot1 = :slot1 WHERE id_treinador = :id_treinador");
        return $stmt->execute([
            'slot1' => $slot1,
            'id_treinador' => $id_treinador
        ]);
    }

    public function buscarPorId($id_treinador){
        $stmt = $this->pdo->prepare("SELECT * FROM treinador WHERE id_treinador = :id_treinador");
        $stmt->execute(['id_treinador' => $id_treinador]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function carregarPorId($id_treinador){
        $usuario = $this->buscarPorId($id_treinador);
        if ($usuario){
            $this->id_treinador = $usuario['id_treinador'] ?? null;
            $this->nome         = $usuario['nome'] ?? '';
            $this->email        = $usuario['email'] ?? '';
            $this->senha        = $usuario['senha'] ?? '';
            $this->sexo         = $usuario['sexo'] ?? '';
            $this->foto         = $usuario['foto'] ?? 'fotoPerfilDefault.webp';
            $this->dinheiro     = $usuario['dinheiro'] ?? 0;
            $this->slot1        = $usuario['slot1'] ?? null;
            $this->slot2        = $usuario['slot2'] ?? null;
            $this->slot3        = $usuario['slot3'] ?? null;
            $this->slot4        = $usuario['slot4'] ?? null;
            $this->slot5        = $usuario['slot5'] ?? null;
            $this->slot6        = $usuario['slot6'] ?? null;
            $this->camposAlterados = []; // reset: nenhum campo alterado ainda
            return true;
        }
        return false;
    }

    public function recarregarSessao($id_treinador){
        $usuario = $this->buscarPorId($id_treinador);
        if ($usuario){
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['logado'] = true;
            $_SESSION['usuario_logado'] = [
                'id_treinador' => $usuario['id_treinador'] ?? null,
                'nome'         => $usuario['nome'] ?? '',
                'email'        => $usuario['email'] ?? '',
                'senha'        => $usuario['senha'] ?? '',
                'sexo'         => $usuario['sexo'] ?? '',
                'foto'         => $usuario['foto'] ?? 'fotoPerfilDefault.webp',
                'dinheiro'     => $usuario['dinheiro'] ?? 0,
                'slot1'        => $usuario['slot1'] ?? null,
                'slot2'        => $usuario['slot2'] ?? null,
                'slot3'        => $usuario['slot3'] ?? null,
                'slot4'        => $usuario['slot4'] ?? null,
                'slot5'        => $usuario['slot5'] ?? null,
                'slot6'        => $usuario['slot6'] ?? null,
            ];
            return true;
        }
        return false;
    }

    public function logar($email, $senha){
        $stmt = $this->pdo->prepare("SELECT * FROM treinador WHERE email = :email AND senha = :senha AND email NOT LIKE 'npc-%@npc.invalid'");
        $stmt->execute([
            'email' => $email,
            'senha' => $senha
        ]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($usuario){
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['logado'] = true;
            $_SESSION['usuario_logado'] = [
                'id_treinador' => $usuario['id_treinador'] ?? null,
                'nome'         => $usuario['nome'] ?? '',
                'email'        => $usuario['email'] ?? '',
                'senha'        => $usuario['senha'] ?? '',
                'sexo'         => $usuario['sexo'] ?? '',
                'foto'         => $usuario['foto'] ?? 'fotoPerfilDefault.webp',
                'dinheiro'     => $usuario['dinheiro'] ?? 0,
                'slot1'        => $usuario['slot1'] ?? null,
                'slot2'        => $usuario['slot2'] ?? null,
                'slot3'        => $usuario['slot3'] ?? null,
                'slot4'        => $usuario['slot4'] ?? null,
                'slot5'        => $usuario['slot5'] ?? null,
                'slot6'        => $usuario['slot6'] ?? null,
            ];
            return true;
        }
        return false;
    }

    public function logout(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();
    }

    public function buscarPokemonsPorIds(array $slotIds, $idTreinador = null): array {
        $slots = array_fill(0, 6, null);

        // Coleta apenas IDs numéricos válidos dos 6 slots
        $idsParaBuscar = [];
        for ($i = 1; $i <= 6; $i++) {
            $val = $slotIds["slot$i"] ?? ($slotIds[$i - 1] ?? null);
            if (!empty($val) && is_numeric($val)) {
                $idsParaBuscar[] = (int)$val;
            }
        }

        // Se nenhum slot numérico estiver preenchido, mas temos o ID do treinador,
        // busca os pokémons do próprio treinador no banco
        if (empty($idsParaBuscar)) {
            if ($idTreinador) {
                $stmtFallback = $this->pdo->prepare("
                    SELECT p.id_pokemon, p.id_pokedex, p.nome, p.hp_atual, pd.hp AS hp_maximo
                    FROM pokemon p
                    INNER JOIN pokedex pd ON p.id_pokedex = pd.id_pokedex
                    WHERE p.id_treinador = :id_treinador
                    ORDER BY p.id_pokemon ASC
                    LIMIT 6
                ");
                $stmtFallback->execute(['id_treinador' => $idTreinador]);
                $pokemonsFallback = $stmtFallback->fetchAll(PDO::FETCH_ASSOC);
                foreach ($pokemonsFallback as $idx => $poke) {
                    $slots[$idx] = $poke;
                }
            }
            return $slots;
        }

        $idsUnicos = array_values(array_unique($idsParaBuscar));
        $placeholders = implode(',', array_fill(0, count($idsUnicos), '?'));

        $sql = "
            SELECT p.id_pokemon, p.id_pokedex, p.nome, p.hp_atual, pd.hp AS hp_maximo
            FROM pokemon p
            INNER JOIN pokedex pd ON p.id_pokedex = pd.id_pokedex
            WHERE p.id_pokemon IN ($placeholders)
               OR p.id_pokedex IN ($placeholders)
        ";
        $params = array_merge($idsUnicos, $idsUnicos);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $pokemonsMapById = [];
        $pokemonsMapByPokedex = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pokemonsMapById[$row['id_pokemon']] = $row;
            $pokemonsMapByPokedex[$row['id_pokedex']] = $row;
        }

        // Preenche cada posição de 0 a 5 com o Pokémon correspondente do slot
        for ($i = 1; $i <= 6; $i++) {
            $idPoke = $slotIds["slot$i"] ?? ($slotIds[$i - 1] ?? null);
            if (!empty($idPoke)) {
                $idInt = (int)$idPoke;
                if (isset($pokemonsMapById[$idInt])) {
                    $slots[$i - 1] = $pokemonsMapById[$idInt];
                } elseif (isset($pokemonsMapByPokedex[$idInt])) {
                    $slots[$i - 1] = $pokemonsMapByPokedex[$idInt];
                }
            }
        }

        return $slots;
    }
}