<?php
class Log {
    private $id_log;
    private $id_treinador;
    private $texto;
    private $data;
    private $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function getIdLog(){
        return $this->id_log;
    }
    public function getIdTreinador(){
        return $this->id_treinador;
    }
    public function getTexto(){
        return $this->texto;
    }
    public function getData(){
        return $this->data;
    }

    public function setIdLog($id_log){
        $this->id_log = $id_log;
    }
    public function setIdTreinador($id_treinador){
        $this->id_treinador = $id_treinador;
    }
    public function setTexto($texto){
        $this->texto = $texto;
    }
    public function setData($data){
        $this->data = $data;
    }

    public function salvar(){
            try {
                $sql = "INSERT INTO log (id_treinador, texto, data) VALUES (:id_treinador, :texto, :data)";
                $stmt = $this->pdo->prepare($sql);
                $ok = $stmt->execute([
                    'id_treinador' => $this->id_treinador,
                    'texto' => $this->texto,
                    'data' => $this->data
                ]);
                if ($ok){
                    $this->id_log = $this->pdo->lastInsertId();
                }
                return $ok;
            } catch (PDOException $e) {
                return false;
            }
        }

    public function buscarLogsView($idTreinador, $categoria = 'todos', $periodo = 'todos'){
        $viewsPorCategoria = [
            'todos'     => 'view_logs_todos',
            'pokemon'   => 'view_logs_pokemon',
            'saldo'     => 'view_logs_saldo',
            'item'      => 'view_logs_item',
            'treinador' => 'view_logs_treinador'
        ];

        // Se categoria for 'todos' mas foi escolhido um período, usa as views de data diretamente
        if ($categoria === 'todos' && $periodo === 'hoje') {
            $viewEscolhida = 'view_logs_hoje';
        } elseif ($categoria === 'todos' && $periodo === '7dias') {
            $viewEscolhida = 'view_logs_semana';
        } elseif ($categoria === 'todos' && $periodo === '30dias') {
            $viewEscolhida = 'view_logs_mes';
        } else {
            $viewEscolhida = $viewsPorCategoria[$categoria] ?? 'view_logs_todos';
        }

        $sql = "SELECT id_log, id_treinador, texto, data FROM {$viewEscolhida} WHERE id_treinador = :idTreinador";
        $params = [':idTreinador' => $idTreinador];

        // Se uma categoria específica foi selecionada combinada com período
        if ($categoria !== 'todos') {
            if ($periodo === 'hoje') {
                $sql .= " AND DATE(data) = CURDATE()";
            } elseif ($periodo === '7dias') {
                $sql .= " AND data >= NOW() - INTERVAL 7 DAY";
            } elseif ($periodo === '30dias') {
                $sql .= " AND data >= NOW() - INTERVAL 30 DAY";
            }
        }

        // Limita aos 30 mais recentes
        $sql .= " ORDER BY data DESC, id_log DESC LIMIT 30";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function verLogs(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $idTreinador = $_SESSION['usuario_logado']['id_treinador'] ?? null;
        if (!$idTreinador) {
            return [];
        }
        return $this->buscarLogsView($idTreinador);
    }
}
