<?php
require 'verifica_login.php';
require 'conexao.php';

$busca = trim($_GET['busca'] ?? '');
$categoriaId = filter_var($_GET['categoria_id'] ?? '', FILTER_VALIDATE_INT);
$categoriaId = ($categoriaId && $categoriaId > 0) ? $categoriaId : null;
$porPagina = 10;
$pagina = max(1, (int) ($_GET['pagina'] ?? 1));
$categorias = $conn->query('SELECT id, nome FROM categorias ORDER BY nome')
    ->fetchAll(PDO::FETCH_ASSOC);

$condicoes = [];
$parametros = [];
if ($busca !== '') {
    $condicoes[] = '(p.nome LIKE ? OR p.codigo LIKE ?)';
    $parametros[] = '%' . $busca . '%';
    $parametros[] = '%' . $busca . '%';
}
if ($categoriaId !== null) {
    $condicoes[] = 'p.categoria_id = ?';
    $parametros[] = $categoriaId;
}
$where = $condicoes ? ' WHERE ' . implode(' AND ', $condicoes) : '';

$stmtTotal = $conn->prepare('SELECT COUNT(*) FROM produtos p' . $where);
$stmtTotal->execute($parametros);
$total = (int) $stmtTotal->fetchColumn();
$totalPaginas = (int) ceil($total / $porPagina);
$pagina = min($pagina, max(1, $totalPaginas));
$offset = ($pagina - 1) * $porPagina;

$sql = "SELECT p.id, p.nome, p.codigo, p.quantidade, p.estoque_minimo, p.imagem,
               c.nome AS categoria_nome
        FROM produtos p
        INNER JOIN categorias c ON c.id = p.categoria_id" . $where .
        ' ORDER BY p.id DESC LIMIT ? OFFSET ?';
$stmt = $conn->prepare($sql);
foreach ($parametros as $i => $valor) {
    $stmt->bindValue($i + 1, $valor, is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->bindValue(count($parametros) + 1, $porPagina, PDO::PARAM_INT);
$stmt->bindValue(count($parametros) + 2, $offset, PDO::PARAM_INT);
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

function linkPagina(string $busca, ?int $categoriaId, int $pagina): string
{
    return '?' . http_build_query([
        'busca' => $busca,
        'categoria_id' => $categoriaId,
        'pagina' => $pagina,
    ]);
}
$mensagens = [
    'criado' => 'Produto cadastrado com sucesso.',
    'atualizado' => 'Produto atualizado com sucesso.',
    'excluido' => 'Produto excluído com sucesso.',
];
$mensagem = $mensagens[$_GET['msg'] ?? ''] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Estoque | StockEasy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-1">Produtos em estoque</h1>
            <p class="text-muted mb-0">Olá, <?= htmlspecialchars($_SESSION['usuario_nome'], ENT_QUOTES, 'UTF-8') ?>.</p>
        </div>
        <a class="btn btn-primary" href="novo.php">＋ Novo produto</a>
    </div>

    <?php if ($mensagem): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <form class="row g-2 align-items-end mb-3" method="get" action="index.php">
        <div class="col-md-6">
            <label class="form-label" for="busca">Buscar por nome ou código</label>
            <input class="form-control" id="busca" name="busca" type="search"
                   value="<?= htmlspecialchars($busca, ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="categoria_id">Categoria</label>
            <select class="form-select" id="categoria_id" name="categoria_id">
                <option value="">Todas as categorias</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= (int) $categoria['id'] ?>" <?= $categoriaId === (int) $categoria['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($categoria['nome'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-outline-primary" type="submit">Buscar</button>
            <a class="btn btn-outline-secondary" href="index.php">Limpar</a>
        </div>
    </form>
    <p class="text-muted">Produtos encontrados: <?= $total ?></p>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-light">
            <tr>
                <th>Imagem</th><th>ID</th><th>Nome</th><th>Código</th><th>Quantidade</th>
                <th>Estoque mínimo</th><th>Categoria</th><th>Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td>
                        <?php $nomeImagem = basename((string) ($produto['imagem'] ?? '')); ?>
                        <?php if ($nomeImagem !== '' && is_file(__DIR__ . '/uploads/produtos/' . $nomeImagem)): ?>
                            <img src="uploads/produtos/<?= rawurlencode($nomeImagem) ?>" alt="Imagem de <?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>" style="width:52px;height:52px;object-fit:cover">
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= (int) $produto['id'] ?></td>
                    <td><?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($produto['codigo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int) $produto['quantidade'] ?></td>
                    <td><?= (int) $produto['estoque_minimo'] ?></td>
                    <td><?= htmlspecialchars($produto['categoria_nome'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-nowrap">
                        <a class="btn btn-sm btn-outline-primary" href="editar.php?id=<?= (int) $produto['id'] ?>">Editar</a>
                        <button class="btn btn-sm btn-outline-danger" type="button"
                                data-bs-toggle="modal" data-bs-target="#modalExcluir"
                                data-id="<?= (int) $produto['id'] ?>"
                                data-nome="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>">Excluir</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$produtos): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Nenhum produto cadastrado.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($totalPaginas > 1): ?>
        <nav aria-label="Paginação de produtos">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= htmlspecialchars(linkPagina($busca, $categoriaId, max(1, $pagina - 1)), ENT_QUOTES, 'UTF-8') ?>">Anterior</a>
                </li>
                <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
                    <li class="page-item <?= $p === $pagina ? 'active' : '' ?>">
                        <a class="page-link" href="<?= htmlspecialchars(linkPagina($busca, $categoriaId, $p), ENT_QUOTES, 'UTF-8') ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= htmlspecialchars(linkPagina($busca, $categoriaId, min($totalPaginas, $pagina + 1)), ENT_QUOTES, 'UTF-8') ?>">Próxima</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</main>

<div class="modal fade" id="modalExcluir" tabindex="-1" aria-labelledby="modalExcluirTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="modalExcluirTitulo">Excluir produto</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">Confirma a exclusão de <strong id="produtoExcluirNome"></strong>?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="excluir.php" method="post">
                    <input type="hidden" name="id" id="produtoExcluirId">
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('modalExcluir').addEventListener('show.bs.modal', function (event) {
    const botao = event.relatedTarget;
    document.getElementById('produtoExcluirId').value = botao.dataset.id;
    document.getElementById('produtoExcluirNome').textContent = botao.dataset.nome;
});
</script>
</body>
</html>
