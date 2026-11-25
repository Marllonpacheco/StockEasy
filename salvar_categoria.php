
<?php
require 'verifica_login.php';
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: categorias.php');
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');

if ($nome === '' || mb_strlen($nome, 'UTF-8') > 100) {
    http_response_code(400);
    exit('Nome da categoria inválido.');
}

try {
    $sql = "INSERT INTO categorias (nome, descricao)
            VALUES (?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $nome,
        $descricao !== '' ? $descricao : null
    ]);

    header('Location: categorias.php');
    exit;

} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        http_response_code(409);
        exit('Esta categoria já está cadastrada.');
    }

    throw $e;
}
