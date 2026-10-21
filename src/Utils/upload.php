<?php
//reutilizei bastante código da aula do bigolin sobre upload
//Realiza o upload da foto de perfil.
function uploadFotoPerfil(array $arquivo, bool $obrigatorio = false){
    // Se nenhum arquivo foi enviado
    if (!$arquivo || $arquivo['error'] === UPLOAD_ERR_NO_FILE) {
        if ($obrigatorio) {
            return [
                'sucesso' => false,
                'nomeArquivo' => null,
                'erro' => 'Você precisa selecionar uma imagem.'
            ];
        }
        // No cadastro opcional, define a foto padrao
        return [
            'sucesso' => true,
            'nomeArquivo' => 'fotoPerfilDefault.webp',
            'erro' => null
        ];
    }

    // Erros de upload
    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        return [
            'sucesso' => false,
            'nomeArquivo' => null,
            'erro' => 'Erro ao enviar o arquivo (código: ' . $arquivo['error'] . ').'
        ];
    }

    //Validação de tamanho
    $tamanhoMaximo = 2 * 1024 * 1024;
    if ($arquivo['size'] > $tamanhoMaximo) {
        return [
            'sucesso' => false,
            'nomeArquivo' => null,
            'erro' => 'A imagem é muito grande. O tamanho máximo permitido é 2MB.'
        ];
    }
    //Validação da extensão
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($extensao, $extensoesPermitidas, true)) {
        return [
            'sucesso' => false,
            'nomeArquivo' => null,
            'erro' => 'Formato não permitido. Escolha JPG, PNG, GIF ou WEBP.'
        ];
    }

    //Gera o nome único com md5 e uniqid
    $nomeArquivo = 'perfil_' . md5(uniqid((string)rand(), true)) . '.' . $extensao;

    // Caminho da pasta no servidor
    $diretorioDestino = __DIR__ . '/../../public/assets/img/fotoPerfil/';
    $caminhoFinal = $diretorioDestino . $nomeArquivo;

    // Move o arquivo temporário
    if (move_uploaded_file($arquivo['tmp_name'], $caminhoFinal)) {
        return [
            'sucesso' => true,
            'nomeArquivo' => $nomeArquivo,
            'erro' => null
        ];
    }

    return [
        'sucesso' => false,
        'nomeArquivo' => null,
        'erro' => 'Erro ao salvar a imagem no servidor.'
    ];
}

//Retorna o caminho relativo correto para exibir a foto de perfil na view
function getCaminhoFoto(string $nomeFoto){
    if (empty($nomeFoto) || $nomeFoto === 'fotoPerfilDefault.webp') {
        return 'assets/img/fotoPerfil/fotoPerfilDefault.webp';
    }
    return 'assets/img/fotoPerfil/' . $nomeFoto;
}
