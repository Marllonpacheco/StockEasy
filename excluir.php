<?php
require 'verifica_login.php';
require 'conexao.php';
require_once 'upload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido.');
}

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID de produto inválido.');
}

try {
    $stmtImagem = $conn->prepare('SELECT imagem FROM produtos WHERE id = ?');
    $stmtImagem->execute([$id]);
    $imagem = $stmtImagem->fetchColumn();

    $stmt = $conn->prepare('DELETE FROM produtos WHERE id = ?');
    $stmt->execute([$id]);
    if ($stmt->rowCount() > 0) {
        removerImagemProduto($imagem === false ? null : (string) $imagem);
    }
    header('Location: index.php?msg=excluido');
    exit;
} catch (PDOException $e) {
    // A FK de itens_movimentacao impede apagar produtos já movimentados.
    error_log($e->getMessage());
    http_response_code(409);
    exit('O produto não pode ser excluído porque está relacionado a uma movimentação.');
}
