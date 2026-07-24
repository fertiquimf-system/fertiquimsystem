<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
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
  <?php 
  include '../base/cabecalho.php';
  ?>

  <div class="container">
    <h2 class="titulo">Painel Principal - ESTOQUE</h2>
    <div class="cards">

      <a href="estoque.php" class="card editarestoque-card">
        <h2></h2>
      </a>

      <a href="../estoque/estoquevisu.php" class="card consultar_estoque-card">
        <h2></h2>
      </a>

      <a href="../inventario/inv.php" class="card inventario-card">
        <h2></h2>
      </a>

        <a href="../nf/inserir.php" class="card InserirNF-card">
        <h2></h2>
      </a>

      <a href="../nf/consultar.php" class="card ConsultarNF-card">
        <h2></h2>
      </a>
      
      <a href="../nf/pendente.php" class="card PendenteNF-card">
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

  <!-- Avisos -->
  <?php include '../avisos/avisos.php'; ?>

</body>
</html>
