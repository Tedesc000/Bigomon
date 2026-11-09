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

    $isLogado = isset($_SESSION['usuario_logado']);
    $usuario = $_SESSION['usuario_logado'] ?? [];

    $nome = $usuario['nome'] ?? '';
    $email = $usuario['email'] ?? '';
    $sexo = $usuario['sexo'] ?? 'masculino';
    $foto = (!empty($usuario['foto'])) ? $usuario['foto'] : 'fotoPerfilDefault.webp';
    ?>
    <title><?= $isLogado ? 'Editar Perfil' : 'Cadastro' ?></title>
</head>
<body id="home">
    <div id="container">
        <div id="tituloContainer">
            <img src="assets/img/inicioLogin/LogoBigomon.png" alt="Bigomon" id="titulo" class="logoBigomon">
        </div>
        <form id="formCadastro" action="?modulo=treinador&acao=salvar" method="POST" enctype="multipart/form-data">
            <div id="camposLogin" class="row">
                <div id="inputFoto">
                    <label for="campoArquivoFoto" style="cursor: pointer;">
                        <img src="assets/img/fotoPerfil/<?= htmlspecialchars($foto) ?>" alt="Foto Perfil" id="foto" title="Clique para escolher sua foto">
                    </label>
                    <input type="file" name="foto" id="campoArquivoFoto" onchange="mostrarFoto()">
                    <label for="campoArquivoFoto" id="labelFoto" class="botaoInicio">
                        <span class="hoverText">▸</span>
                        <span class="defaultText">ESCOLHER</span>
                    </label>
                </div>
                <div id="inputCadastro">
                    <input type="text" name="nome" id="nome" placeholder="Nome" value="<?= htmlspecialchars($nome) ?>" required>
                    <div id="inputSexo">
                        <div class="labelSexo">
                            <label for="masculino">MASCULINO: </label>
                            <input type="radio" name="sexo" id="masculino" value="masculino" <?= ($sexo === 'masculino') ? 'checked' : '' ?> required>
                        </div>
                        <div class="labelSexo">
                            <label for="feminino">FEMININO: </label>
                            <input type="radio" name="sexo" id="feminino" value="feminino" <?= ($sexo === 'feminino') ? 'checked' : '' ?> required>
                        </div>
                    </div>
                    <input type="email" name="email" id="email" placeholder="Email" value="<?= htmlspecialchars($email) ?>" required>
                    <input type="password" name="senha" id="senha" placeholder="<?= $isLogado ? 'Nova senha (opcional)' : 'Senha' ?>" <?= $isLogado ? '' : 'required' ?>>
                </div>
            </div>
            <button type="submit" class="botaoInicio">
                <span class="hoverText">▸</span>
                <span class="defaultText"><?= $isLogado ? "ATUALIZAR" : "CADASTRAR" ?></span>
            </button>
            <a href="?modulo=treinador&acao=<?= $isLogado ? 'home' : 'cadastroLogin' ?>" class="botaoInicio">
                <span class="hoverText">▸</span>
                <span class="defaultText">VOLTAR</span>
            </a>
        </form> 
    </div>

    <script>
        const inputFoto = document.getElementById('campoArquivoFoto');
        const imgFoto = document.getElementById('foto');

        //coloca a url da ultima foto inserida em input file na imagem de perfil
        function mostrarFoto(){
            if (inputFoto.files && inputFoto.files[0]) {
                imgFoto.src = URL.createObjectURL(inputFoto.files[0]);
            }
        }
    </script>
</body>
</html>
