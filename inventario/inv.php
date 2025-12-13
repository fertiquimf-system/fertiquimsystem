<?php
session_start();
require_once '../conexaohost/conexao.php';

if (!isset($_SESSION['nome_usuario'])) {
    header('Location: ../pglogin/pglogin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Fertiquim - Sistema</title>
  <link rel="stylesheet" href="../css/estilo.css" />
</head>
<body>
<?php include '../base/estoque.php'; ?>
  <div class="container">
    <h2 class="titulo">Painel Principal</h2>
    <div class="cards">
      <a href="inserir.php" class="card inserirmaterial-card">
        <h2></h2>
      </a>
      <a href="consultar.php" class="card consultarinventario-card">
        <h2></h2>
      </a>
      <a href="editar.php" class="card editarinventario-card">
        <h2></h2>
      </a>

    </div>
  </div>

    <?php 
  include '../base/rodape.php';
  ?>
  <?php if (isset($_SESSION['nome_usuario']) && isset($_SESSION['funcao_usuario'])): ?>
    <div class="usuario-logado">
      <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>
    </div>
  <?php endif; ?>
</body>
</html>
