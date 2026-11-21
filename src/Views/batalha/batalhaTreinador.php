<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/batalha.css">
    <?php
        $numeroGinasio = (int) ($_SESSION['ginasio_padrao_atual'] ?? 1);
        $numeroTreinador = (int) ($_SESSION['treinador_padrao_atual'] ?? 1);
        $nomesLideres = [
            1 => 'Roxanne',
            2 => 'Brawly',
            3 => 'Wattson',
            4 => 'Flannery',
            5 => 'Norman',
            6 => 'Winona',
            7 => 'Tate',
            8 => 'Juan',
        ];
        if ($numeroTreinador === 3) {
            $nomeTreinador = $nomesLideres[$numeroGinasio] ?? 'Líder do Ginásio';
        } else {
            $nomeTreinador = "Treinador Padrão {$numeroTreinador}";
        }
    ?>
    <title>Bigomon - Ginásio <?= $numeroGinasio ?> - <?= htmlspecialchars($nomeTreinador, ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body id="trainerBody" data-ginasio="<?= $numeroGinasio ?>" data-treinador-padrao="<?= $numeroTreinador ?>">
    <div id="treinadorPadraoLuta">Ginásio <?= $numeroGinasio ?> - <?= htmlspecialchars($nomeTreinador, ENT_QUOTES, 'UTF-8') ?></div>
</body>
</html>