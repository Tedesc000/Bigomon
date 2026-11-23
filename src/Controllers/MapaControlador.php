<?php
class MapaControlador {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function carregarSlotsEquipe(): array {
        require_once __DIR__ . "/TreinadorControlador.php";
        $treinadorCtrl = new TreinadorControlador($this->pdo);
        return $treinadorCtrl->buscarPokemonsDosSlots();
    }

    // Monta e exibe um mapa de cidade (mapaN) ou ginásio (ginasioN), aplicando a progressão:
    // o mapaN e o ginasioN só ficam acessíveis depois de vencer o líder do ginásio N-1
    private function exibirCidade(int $numero, bool $ehGinasio) {
        $idTreinador = $_SESSION['usuario_logado']['id_treinador'] ?? null;
        $treinador = new Treinador($this->pdo);
        $vitorias = $treinador->buscarVitorias($idTreinador);

        if (!$treinador->podeAcessarMapa($idTreinador, $numero, $vitorias)) {
            // volta para o último mapa liberado
            $ultimoLiberado = 1;
            for ($i = $numero - 1; $i >= 1; $i--) {
                if ($treinador->podeAcessarMapa($idTreinador, $i, $vitorias)) {
                    $ultimoLiberado = $i;
                    break;
                }
            }
            header("Location: ?modulo=mapa&acao=mapa{$ultimoLiberado}");
            exit;
        }

        if ($ehGinasio) {
            $_SESSION['mapa'] = "ginasio{$numero}";
        } else {
            $_SESSION['mapa'] = "mapa{$numero}";
            $_SESSION['id_loja'] = $numero + 1;
        }

        // As views dos mapas e ginásios dependem destes valores.
        $ginasioConcluido = $treinador->ginasioConcluido($idTreinador, $numero, $vitorias);
        $statusAdversarios = $treinador->statusAdversariosGinasio($idTreinador, $numero, $vitorias);
        $slotsEquipe = $this->carregarSlotsEquipe();

        $view = $ehGinasio ? "ginasio{$numero}" : "mapa{$numero}";
        require_once __DIR__ . "/../Views/mapas/{$view}.php";
    }

    public function mapaInicio() {
        $_SESSION['mapa'] = 'mapaInicio';
        $_SESSION['id_loja'] = 1;
        $slotsEquipe = $this->carregarSlotsEquipe();
        require_once __DIR__ . "/../Views/mapas/mapaInicio.php";
    }

    public function mapa1()    { $this->exibirCidade(1, false); }
    public function ginasio1() { $this->exibirCidade(1, true); }

    public function mapa2()    { $this->exibirCidade(2, false); }
    public function ginasio2() { $this->exibirCidade(2, true); }

    public function mapa3()    { $this->exibirCidade(3, false); }
    public function ginasio3() { $this->exibirCidade(3, true); }

    public function mapa4()    { $this->exibirCidade(4, false); }
    public function ginasio4() { $this->exibirCidade(4, true); }

    public function mapa5()    { $this->exibirCidade(5, false); }
    public function ginasio5() { $this->exibirCidade(5, true); }

    public function mapa6()    { $this->exibirCidade(6, false); }
    public function ginasio6() { $this->exibirCidade(6, true); }

    public function mapa7()    { $this->exibirCidade(7, false); }
    public function ginasio7() { $this->exibirCidade(7, true); }

    public function mapa8()    { $this->exibirCidade(8, false); }
    public function ginasio8() { $this->exibirCidade(8, true); }
    public function mapa9()    { $this->exibirCidade(9, false); }

    private function exibirCampeonato(bool $ehGinasio): void {
        $idTreinador = $_SESSION['usuario_logado']['id_treinador'] ?? null;
        $treinador = new Treinador($this->pdo);
        $vitorias = $treinador->buscarVitorias($idTreinador);

        if (!$treinador->podeAcessarMapa($idTreinador, 9, $vitorias)) {
            header("Location: ?modulo=mapa&acao=mapa8");
            exit;
        }

        $statusAdversarios = $treinador->statusAdversariosGinasio($idTreinador, 9, $vitorias);
        if ($ehGinasio && $statusAdversarios['lider'] === 'bloqueado') {
            header("Location: ?modulo=mapa&acao=mapaCampeao");
            exit;
        }

        $_SESSION['mapa'] = $ehGinasio ? 'ginasioCampeao' : 'mapaCampeao';
        $ginasioConcluido = $treinador->ginasioConcluido($idTreinador, 9, $vitorias);
        $slotsEquipe = $this->carregarSlotsEquipe();

        $view = $ehGinasio ? 'ginasioCampeao' : 'mapaCampeao';
        require_once __DIR__ . "/../Views/mapas/{$view}.php";
    }

    public function mapaCampeao() { $this->exibirCampeonato(false); }
    public function ginasioCampeao() { $this->exibirCampeonato(true); }
    public function campeao() { $this->mapaCampeao(); }
}
