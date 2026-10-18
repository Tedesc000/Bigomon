<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/inicioLogin.css">
    <title>Cadastro</title>
</head>
<body id="cadastro">
    <div id="container">
        <div id="tituloContainerCadastro">
            <span id="tituloCadastro">Bigomon</span>
        </div>
        <form action="?modulo=treinador&acao=salvar" method="POST" enctype="multipart/form-data" id="formCadastro">
            <div id="camposCadastro">
                <div id="inputFoto">
                    <label for="campoArquivoFoto" style="cursor: pointer;">
                        <img src="assets/img/fotoPerfil/fotoPerfilDefault.webp" alt="Foto Perfil" id="foto" title="Clique para escolher sua foto">
                    </label>
                    <input type="file" name="foto" id="campoArquivoFoto" onchange="mostrarFoto()">
                    <label for="campoArquivoFoto" id="labelFoto" class="botaoEscolherFoto">Escolha sua foto</label>
                </div>
                <div id="inputCadastro">
                    <input type="text" name="nome" id="nome" placeholder="Nome" required>
                    <div id="inputSexo">
                        <label for="sexo">Sexo:</label>
                        <input type="radio" name="sexo" id="masculino" value="masculino" required>
                        <label for="masculino">Masculino</label>
                        <input type="radio" name="sexo" id="feminino" value="feminino" required>
                        <label for="feminino">Feminino</label>
                    </div>
                    <input type="email" name="email" id="email" placeholder="Email" required>
                    <input type="password" name="senha" id="senha" placeholder="Senha" required>
                </div>
            </div>
            <input type="submit" value="Cadastrar" class="botaoCadastrar">
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