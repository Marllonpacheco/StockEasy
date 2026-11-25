<?php
require 'verifica_login.php';
require 'conexao.php';

$categorias = $conn->query('SELECT id, nome FROM categorias ORDER BY nome')
    ->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Novo produto | StockEasy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>
<main class="container py-4">
    <div class="row justify-content-center"><div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header"><h1 class="h5 mb-0">＋ Novo produto</h1></div>
            <div class="card-body">
                <?php if (!$categorias): ?>
                    <div class="alert alert-warning">Cadastre uma categoria antes de incluir produtos.</div>
                <?php endif; ?>
                <form action="salvar.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3"><label class="form-label" for="nome">Nome</label><input class="form-control" id="nome" name="nome" maxlength="150" required></div>
                    <div class="mb-3"><label class="form-label" for="codigo">Código</label><input class="form-control" id="codigo" name="codigo" maxlength="50" required></div>
                    <div class="mb-3"><label class="form-label" for="descricao">Descrição</label><textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label" for="quantidade">Quantidade</label><input class="form-control" type="number" id="quantidade" name="quantidade" min="0" value="0" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label" for="estoque_minimo">Estoque mínimo</label><input class="form-control" type="number" id="estoque_minimo" name="estoque_minimo" min="0" value="0" required></div>
                    </div>
                    <div class="mb-3"><label class="form-label" for="categoria_id">Categoria</label>
                        <select class="form-select" id="categoria_id" name="categoria_id" required>
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= (int) $categoria['id'] ?>"><?= htmlspecialchars($categoria['nome'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="imagem">Imagem (JPG, PNG ou WebP, até 2 MB)</label>
                        <input class="form-control" type="file" id="imagem" name="imagem"
                               accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                    </div>
                    <button class="btn btn-success" type="submit" <?= !$categorias ? 'disabled' : '' ?>>Salvar</button>
                    <a class="btn btn-outline-secondary" href="index.php">Voltar</a>
                </form>
            </div>
        </div>
    </div></div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
