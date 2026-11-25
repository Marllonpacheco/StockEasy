<nav class="navbar navbar-expand-md navbar-dark" style="background-color:#7952b3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">StockEasy</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Produtos</a></li>
                <li class="nav-item"><a class="nav-link" href="novo.php">＋ Novo produto</a></li>
                <li class="nav-item"><a class="nav-link" href="categorias.php">Categorias</a></li>
                <li class="nav-item"><span class="nav-link">Olá, <?= htmlspecialchars($_SESSION['usuario_nome'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
            </ul>
        </div>
    </div>
</nav>


