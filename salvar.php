<?php
require 'verifica_login.php';
require 'conexao.php';
require_once 'upload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: novo.php');
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$codigo = trim($_POST['codigo'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$quantidade = filter_var($_POST['quantidade'] ?? null, FILTER_VALIDATE_INT);
$estoqueMinimo = filter_var($_POST['estoque_minimo'] ?? null, FILTER_VALIDATE_INT);
$categoriaId = filter_var($_POST['categoria_id'] ?? null, FILTER_VALIDATE_INT);

if ($nome === '' || $codigo === '' || $quantidade === false || $quantidade < 0
    || $estoqueMinimo === false || $estoqueMinimo < 0 || $categoriaId === false || $categoriaId < 1) {
    http_response_code(422);
    exit('Confira os campos obrigatórios e informe quantidades iguais ou maiores que zero.');
}

$resultadoImagem = salvarImagemProduto($_FILES['imagem'] ?? []);
if (!$resultadoImagem['ok']) {
    http_response_code(422);
    exit(htmlspecialchars($resultadoImagem['erro'], ENT_QUOTES, 'UTF-8'));
}
$imagem = $resultadoImagem['arquivo'];

try {
    $sql = "INSERT INTO produtos (nome, codigo, descricao, quantidade, estoque_minimo, categoria_id, imagem)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nome, $codigo, $descricao !== '' ? $descricao : null,
        $quantidade, $estoqueMinimo, $categoriaId, $imagem]);
    header('Location: index.php?msg=criado');
    exit;
} catch (PDOException $e) {
    removerImagemProduto($imagem);
    error_log($e->getMessage());
    http_response_code(400);
    exit('Não foi possível salvar. Verifique se o código já está cadastrado e se a categoria existe.');
}
