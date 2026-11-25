<?php
require 'verifica_login.php';
require 'conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID de produto inválido.');
}

$stmt = $conn->prepare('SELECT * FROM produtos WHERE id = ?');
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$produto) {
    http_response_code(404);
    exit('Produto não encontrado.');
}
$categorias = $conn->query('SELECT id, nome FROM categorias ORDER BY nome')
    ->fetchAll(PDO::FETCH_ASSOC);
$imagemAtual = basename((string) ($produto['imagem'] ?? ''));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar produto | StockEasy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>
<main class="container py-4">
    <div class="row justify-content-center"><div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header"><h1 class="h5 mb-0">Editar produto</h1></div>
            <div class="card-body">
                <form action="atualizar.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= (int) $produto['id'] ?>">
                    <div class="mb-3"><label class="form-label" for="nome">Nome</label><input class="form-control" id="nome" name="nome" maxlength="150" required value="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>"></div>
                    <div class="mb-3"><label class="form-label" for="codigo">Código</label><input class="form-control" id="codigo" name="codigo" maxlength="50" required value="<?= htmlspecialchars($produto['codigo'], ENT_QUOTES, 'UTF-8') ?>"></div>
                    <div class="mb-3"><label class="form-label" for="descricao">Descrição</label><textarea class="form-control" id="descricao" name="descricao" rows="3"><?= htmlspecialchars($produto['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label" for="quantidade">Quantidade</label><input class="form-control" type="number" id="quantidade" name="quantidade" min="0" required value="<?= (int) $produto['quantidade'] ?>"></div>
                        <div class="col-md-6 mb-3"><label class="form-label" for="estoque_minimo">Estoque mínimo</label><input class="form-control" type="number" id="estoque_minimo" name="estoque_minimo" min="0" required value="<?= (int) $produto['estoque_minimo'] ?>"></div>
                    </div>
                    <div class="mb-3"><label class="form-label" for="categoria_id">Categoria</label>
                        <select class="form-select" id="categoria_id" name="categoria_id" required>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= (int) $categoria['id'] ?>" <?= (int) $produto['categoria_id'] === (int) $categoria['id'] ? 'selected' : '' ?>><?= htmlspecialchars($categoria['nome'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="imagem">Nova imagem (JPG, PNG ou WebP, até 2 MB)</label>
                        <input class="form-control" type="file" id="imagem" name="imagem"
                               accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        <?php if ($imagemAtual !== '' && is_file(__DIR__ . '/uploads/produtos/' . $imagemAtual)): ?>
                            <div class="mt-2"><img src="uploads/produtos/<?= rawurlencode($imagemAtual) ?>" alt="Imagem atual do produto" style="max-width:160px;max-height:120px"></div>
                        <?php endif; ?>
                    </div>
                    <button class="btn btn-success" type="submit">Salvar alterações</button>
                    <a class="btn btn-outline-secondary" href="index.php">Voltar</a>
                </form>
            </div>
        </div>
    </div></div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
