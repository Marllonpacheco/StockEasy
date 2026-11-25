
<?php
require 'verifica_login.php';
require 'conexao.php';

// Consulta as categorias e conta os produtos vinculados
$sql = "SELECT c.id, c.nome, c.descricao,
               COUNT(p.id) AS total_produtos
        FROM categorias c
        LEFT JOIN produtos p
            ON p.categoria_id = c.id
        GROUP BY c.id, c.nome, c.descricao
        ORDER BY c.nome";

$categorias = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Categorias | StockEasy</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>

<?php include 'navbar.php'; ?>

<main class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Categorias</h1>

        <a href="nova_categoria.php" class="btn btn-primary">
            + Nova categoria
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Produtos</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($categorias as $categoria): ?>
                    <tr>
                        <td><?= (int) $categoria['id'] ?></td>

                        <td><?= htmlspecialchars($categoria['nome'], ENT_QUOTES, 'UTF-8') ?></td>

                        <td><?= htmlspecialchars($categoria['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>

                        <td><?= (int) $categoria['total_produtos'] ?></td>

                        <td>
                            <a href="editar_categoria.php?id=<?= (int) $categoria['id'] ?>"
                               class="btn btn-sm btn-outline-primary">
                                Editar
                            </a>

                            <?php if ((int) $categoria['total_produtos'] === 0): ?>
                                <form action="excluir_categoria.php"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Deseja excluir esta categoria?')">

                                    <input type="hidden" name="id"
                                           value="<?= (int) $categoria['id'] ?>">

                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger">
                                        Excluir
                                    </button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-secondary"
                                        disabled
                                        title="Categoria possui produtos vinculados">
                                    Excluir
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (!$categorias): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Nenhuma categoria cadastrada.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="index.php" class="btn btn-outline-secondary">
        Voltar aos produtos
    </a>

</main>

</body>
</html>
