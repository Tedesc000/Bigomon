<?php
$nomeAdversario = $estadoInicial['adversario']['nome'] ?? 'Treinador';
$tituloPagina = 'Bigomon - Batalha contra ' . $nomeAdversario;
$idBody = 'trainerBody';
require __DIR__ . '/telaBatalha.php';
