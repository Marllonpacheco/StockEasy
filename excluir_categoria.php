
<?php
require 'verifica_login.php';
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Categoria inválida.');
}

$stmt = $conn->prepare(
    "SELECT COUNT(*) FROM produtos WHERE categoria_id = ?"
);
$stmt->execute([$id]);

if ((int) $stmt->fetchColumn() > 0) {
    http_response_code(409);
    exit('Não é possível excluir uma categoria que possui produtos.');
}

try {
    $stmt = $conn->prepare(
        "DELETE FROM categorias WHERE id = ?"
    );

    $stmt->execute([$id]);

    header('Location: categorias.php');
    exit;

} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        http_response_code(409);
        exit('Categoria possui registros vinculados.');
    }

    throw $e;
}
