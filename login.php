
<?php
session_start(); // sempre a primeira linha
require 'conexao.php';

$erro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $stmt = $conn->prepare('SELECT id, nome, senha FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Troca o ID da sessão após o login para evitar reutilização do ID anterior.
        session_regenerate_id(true);
        $_SESSION['usuario_id']   = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];

        header("Location: index.php");
        exit;
    } else {
        $erro = "E-mail ou senha inválidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Entrar | StockEasy</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>


<body>
<div class="container" style="max-width:380px;margin-top:60px">
  <div class="card shadow-sm">
    <div class="card-header bg-purple text-white text-center"
         style="background-color:#7952b3">
      🔐 Entrar no Sistema
    </div>
    <div class="card-body">

      <?php if ($erro): ?>
        <div class="alert alert-danger">
          <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php endif; ?>
      <?php if (($_GET['cadastro'] ?? '') === 'ok'): ?>
        <div class="alert alert-success">Conta criada. Entre com seu e-mail e senha.</div>
      <?php endif; ?>

      <form method="POST">
        <label class="form-label">E-mail</label>
        <input type="email" name="email"
               class="form-control mb-3" required>

        <label class="form-label">Senha</label>
        <input type="password" name="senha"
               class="form-control mb-3" required>

        <button class="btn btn-purple w-100"
                style="background-color:#7952b3;color:#fff">
          Entrar
        </button>
      </form>

      <p class="text-center mt-3 mb-0">Ainda não tem conta? <a href="cadastro.php">Cadastrar</a></p>

    </div>
  </div>
</div>
</body>
</html>
