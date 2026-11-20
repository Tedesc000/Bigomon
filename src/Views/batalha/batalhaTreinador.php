<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/batalha.css">
    <?php
        $numeroGinasio = (int) ($_SESSION['ginasio_padrao_atual'] ?? 1);
        $numeroTreinador = (int) ($_SESSION['treinador_padrao_atual'] ?? 1);
    ?>
    <title>Bigomon - Ginásio <?= $numeroGinasio ?> - Treinador <?= $numeroTreinador ?></title>
</head>
<body id="trainerBody" data-ginasio="<?= $numeroGinasio ?>" data-treinador-padrao="<?= $numeroTreinador ?>">
    <div id="treinadorPadraoLuta">Ginásio <?= $numeroGinasio ?> - Treinador Padrão <?= $numeroTreinador ?></div>
</body>
</html>