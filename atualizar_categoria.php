
<?php
require 'verifica_login.php';
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: categorias.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nome = trim($_POST['nome'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');

if (!$id || $id < 1 || $nome === '' ||
    mb_strlen($nome, 'UTF-8') > 100) {
    http_response_code(400);
    exit('Dados inválidos.');
}

try {
    $stmt = $conn->prepare(
        "UPDATE categorias SET nome = ?, descricao = ? WHERE id = ?"
    );

    $stmt->execute([
        $nome,
        $descricao !== '' ? $descricao : null,
        $id
    ]);

    header('Location: categorias.php');
    exit;

} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        http_response_code(409);
        exit('Já existe uma categoria com este nome.');
    }

    throw $e;
}
