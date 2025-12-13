<?php
require_once '../conexaohost/conexao.php';
session_start();

include('../sessao/verifica_sessao.php');
restringirAcesso(['Gerencia', 'Administrador', 'Proprietario, Financeiro, Vendedores, Almoxarifado']);

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
  <header>
    <h1>FERTIQUIM Fertilizantes</h1>
    <nav>
      <a href="../pginicial/pginicial.php">Início</a>
      <a href="../pglogin/pglogin.php">Sair</a>
    </nav>
  </header>

  <div class="container">
    <h2 class="titulo">Painel Principal</h2>
    <div class="cards">
     <a href="entrada_balanca.php" class="card entrada_balanca-card">
     <h2></h2>
     </a>

     <a href="saida_balanca.php" class="card saida_balanca-card">
     <h2></h2>
     </a>

     <a href="canhotos_salvos.php" class="card consultarcanhoto-card">
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
