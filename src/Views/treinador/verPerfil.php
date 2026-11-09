<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/inicioLogin.css">
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if(isset($_SESSION['usuario_logado'])) {
        $isLogado = isset($_SESSION['usuario_logado']);
        $usuario = $_SESSION['usuario_logado'] ?? [];
    } else {
        header("Location: ?modulo=treinador&acao=cadastroLogin");
    }

    $nome = $usuario['nome'] ?? '';
    $email = $usuario['email'] ?? '';
    $sexo = $usuario['sexo'] ?? 'masculino';
    $foto = (!empty($usuario['foto'])) ? $usuario['foto'] : 'fotoPerfilDefault.webp';
    $dinheiro = $usuario['dinheiro'] ?? 0;
    $slot1 = $usuario['slot1'] ?? null;
    $slot2 = $usuario['slot2'] ?? null;
    $slot3 = $usuario['slot3'] ?? null;
    $slot4 = $usuario['slot4'] ?? null;
    $slot5 = $usuario['slot5'] ?? null;
    $slot6 = $usuario['slot6'] ?? null;
    $mapa = $_SESSION['mapa'] ?? 'mapaInicio';
    ?>
    <title>Ver Perfil</title>
</head>
<body id="home">
    <div id="container">
        <div id="tituloContainer">
            <span id="titulo">BIGOMON</span>
        </div>
        <div id="formCadastro">
            <div id="camposLogin" class="row">
                <div id="inputFoto">
                    <label for="campoArquivoFoto" style="cursor: pointer;">
                        <img src="assets/img/fotoPerfil/<?= htmlspecialchars($foto) ?>" alt="Foto Perfil" id="foto" title="Foto de Perfil">
                    </label>
                </div>
                <div id="inputCadastro">
                    <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($nome) ?>"  readonly>
                    <div id="inputSexo">
                        <div class="labelSexo">
                            <label for="masculino">MASCULINO: </label>
                            <input type="radio" name="sexo" id="masculino" value="masculino" <?= ($sexo === 'masculino') ? 'checked' : '' ?>readonly>
                        </div>
                        <div class="labelSexo">
                            <label for="feminino">FEMININO: </label>
                            <input type="radio" name="sexo" id="feminino" value="feminino" <?= ($sexo === 'feminino') ? 'checked' : '' ?>readonly>
                        </div>
                    </div>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($email) ?>"readonly>
                    <input type="text" name="dinheiro" id="dinheiro" value="<?= htmlspecialchars($dinheiro) ?>"readonly>
                </div>
            </div>
            <a href="?modulo=treinador&acao=logout" class="botaoInicio">
                <span class="hoverText">▸</span>
                <span class="defaultText">DESCONECTAR</span>
            </a>
            <a href="?modulo=mapa&acao=<?= htmlspecialchars($mapa) ?>" class="botaoInicio">
                <span class="hoverText">▸</span>
                <span class="defaultText">VOLTAR</span>
            </a>
        </div> 
    </div>
</body>
</html>
