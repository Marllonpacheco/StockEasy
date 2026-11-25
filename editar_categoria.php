
<?php
require 'verifica_login.php';
require 'conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID inválido.');
}

$stmt = $conn->prepare(
    "SELECT id, nome, descricao FROM categorias WHERE id = ?"
);
$stmt->execute([$id]);

$categoria = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$categoria) {
    http_response_code(404);
    exit('Categoria não encontrada.');
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar categoria | StockEasy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>

<?php include 'navbar.php'; ?>

<main class="container py-4">
    <div class="card shadow-sm mx-auto" style="max-width:600px">
        <div class="card-header">
            <h1 class="h5 mb-0">Editar categoria</h1>
        </div>

        <div class="card-body">
            <form action="atualizar_categoria.php" method="POST">

                <input type="hidden" name="id"
                       value="<?= (int) $categoria['id'] ?>">

                <div class="mb-3">
                    <label class="form-label" for="nome">Nome</label>
                    <input type="text" id="nome" name="nome"
                           class="form-control"
                           maxlength="100"
                           value="<?= htmlspecialchars($categoria['nome'], ENT_QUOTES, 'UTF-8') ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao"
                              class="form-control"
                              rows="3"><?= htmlspecialchars($categoria['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <button type="submit" class="btn btn-success">
                    Atualizar
                </button>

                <a href="categorias.php" class="btn btn-outline-secondary">
                    Voltar
                </a>
            </form>
        </div>
    </div>
</main>

</body>
</html>
