
<?php
require 'verifica_login.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nova categoria | StockEasy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>

<?php include 'navbar.php'; ?>

<main class="container py-4">
    <div class="card shadow-sm mx-auto" style="max-width:600px">

        <div class="card-header">
            <h1 class="h5 mb-0">Nova categoria</h1>
        </div>

        <div class="card-body">
            <form action="salvar_categoria.php" method="POST">

                <div class="mb-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" id="nome" name="nome"
                           class="form-control" maxlength="100" required>
                </div>

                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea id="descricao" name="descricao"
                              class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-success">
                    Salvar
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
