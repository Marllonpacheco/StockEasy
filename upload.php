<?php

/** Salva uma imagem de produto validando o conteúdo recebido pelo PHP. */
function salvarImagemProduto(array $arquivo): array
{
    $limiteBytes = 2 * 1024 * 1024;
    $extensoesPorMime = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'image/webp' => ['webp'],
    ];

    if (!$arquivo || ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['ok' => true, 'arquivo' => null];
    }
    if (!isset($arquivo['error']) || is_array($arquivo['error'])) {
        return ['ok' => false, 'erro' => 'Dados de upload inválidos.'];
    }
    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'erro' => 'O envio da imagem falhou. Verifique o limite de upload do PHP.'];
    }
    if (empty($arquivo['tmp_name']) || !is_uploaded_file($arquivo['tmp_name'])) {
        return ['ok' => false, 'erro' => 'Arquivo temporário inválido.'];
    }

    $tamanho = filesize($arquivo['tmp_name']);
    if ($tamanho === false || $tamanho < 1 || $tamanho > $limiteBytes) {
        return ['ok' => false, 'erro' => 'A imagem deve ter até 2 MB.'];
    }

    // Detecta MIME pelos bytes do arquivo, e não pelo nome informado pelo navegador.
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);
    if (!isset($extensoesPorMime[$mime])) {
        return ['ok' => false, 'erro' => 'Envie uma imagem JPG, PNG ou WebP.'];
    }
    $extensaoEnviada = strtolower(pathinfo($arquivo['name'] ?? '', PATHINFO_EXTENSION));
    if (!in_array($extensaoEnviada, $extensoesPorMime[$mime], true)) {
        return ['ok' => false, 'erro' => 'A extensão não corresponde ao conteúdo da imagem.'];
    }
    $dadosImagem = @getimagesize($arquivo['tmp_name']);
    if ($dadosImagem === false || ($dadosImagem['mime'] ?? '') !== $mime) {
        return ['ok' => false, 'erro' => 'O arquivo enviado não é uma imagem válida.'];
    }

    $pasta = __DIR__ . '/uploads/produtos/';
    if (!is_dir($pasta) && !mkdir($pasta, 0755, true) && !is_dir($pasta)) {
        return ['ok' => false, 'erro' => 'Não foi possível criar a pasta de imagens.'];
    }
    if (!is_writable($pasta)) {
        return ['ok' => false, 'erro' => 'A pasta de imagens não permite escrita.'];
    }

    // A regra também está versionada em uploads/produtos/.htaccess.
    $protecao = $pasta . '.htaccess';
    if (!is_file($protecao)) {
        $conteudoProtecao = "Options -ExecCGI\n"
            . "RemoveHandler .php .phtml .phar\n"
            . "<FilesMatch \"\\.(php|phtml|phar)$\">\n"
            . "Require all denied\n"
            . "</FilesMatch>\n";
        if (file_put_contents($protecao, $conteudoProtecao, LOCK_EX) === false) {
            return ['ok' => false, 'erro' => 'Não foi possível proteger a pasta de imagens.'];
        }
    }

    $extensao = $mime === 'image/jpeg' ? 'jpg' : $extensoesPorMime[$mime][0];
    $nomeFinal = bin2hex(random_bytes(16)) . '.' . $extensao;
    if (!move_uploaded_file($arquivo['tmp_name'], $pasta . $nomeFinal)) {
        error_log('Falha ao mover imagem de produto para a pasta de uploads.');
        return ['ok' => false, 'erro' => 'Não foi possível salvar a imagem no servidor.'];
    }

    return ['ok' => true, 'arquivo' => $nomeFinal];
}

/** Remove apenas nomes gerados pelo sistema, sempre dentro da pasta de produtos. */
function removerImagemProduto(?string $nomeArquivo): void
{
    $nome = basename((string) $nomeArquivo);
    if (!preg_match('/^[a-f0-9]{32}\.(jpg|png|webp)$/', $nome)) {
        return;
    }
    $caminho = __DIR__ . '/uploads/produtos/' . $nome;
    if (is_file($caminho)) {
        unlink($caminho);
    }
}
