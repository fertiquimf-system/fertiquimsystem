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
    <h2 class="titulo">Painel Principal</h2>
    <div class="cards">
      <a href="../financeiro/financeiro.php" class="card financeiro-card">
        <h2></h2>
      </a>
      
      <a href="../rh/rh.php" class="card rh-card">
        <h2></h2>
      </a>
      <a href="../vendedores/vendedores.php" class="card vendas-card">
        <h2></h2>
      </a>
      <a href="../estoque/estoque_inicial.php" class="card almoxarifado-card">
        <h2></h2>
      </a>
      <a href="../carregamento/carregamento.php" class="card carregamento-card">
        <h2></h2>
      </a>
      <a href="../deposito/deposito.php" class="card deposito-card">
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
