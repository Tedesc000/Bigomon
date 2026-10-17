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
        <form action="?modulo=treinador&acao=roleta" method="POST" id="formCadastro">
            <div id="camposCadastro">
                <div id="inputFoto">
                    <input type="file" name="foto" id="foto" placeholder="Foto">
                    <label for="foto" id="labelFoto" class="botaoEscolherFoto">Escolha sua foto</label>
                </div>
                <div id="inputCadastro">
                    <input type="text" name="nome" id="nome" placeholder="Nome">
                    <div id="inputSexo">
                        <label for="sexo">Sexo:</label>
                        <input type="radio" name="sexo" id="masculino" value="masculino">
                        <label for="masculino">Masculino</label>
                        <input type="radio" name="sexo" id="feminino" value="feminino">
                        <label for="feminino">Feminino</label>
                    </div>
                    <input type="email" name="email" id="email" placeholder="Email">
                    <input type="password" name="senha" id="senha" placeholder="Senha">
                </div>
            </div>
            <input type="submit" value="Cadastrar" class="botaoCadastrar">
        </form>
    </div>
</body>
</html>