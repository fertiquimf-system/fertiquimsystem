<?php
session_start();
include('../sessao/verifica_sessao.php');
restringirAcesso(['Administrador', 'Proprietario', 'Vendedores']);

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
  <?php include '../base/cabecalho.php'; ?>
  <div class="container">
    <h2 class="titulo">Painel Principal</h2>
    <div class="cards">
      <a href="vendas_tonelada.php" class="card VendasInternas-card">
        <h2></h2>
      </a>
        
      <a href="../cliente/cliente.php" class="card Clientes-card">
      <h2></h2>  
      </a>

      <a href="pendente.php" class="card Venda_pendente-card">
      <h2></h2>  
      </a>
      
      <a href="../cliente/cadastro_revenda.php" class="card cadastrar_revenda-card">
      <h2></h2>  
      </a>
      
      <a href="vendas_revendedores.php" class="card controle_revenda-card">
      <h2></h2>  
      </a>

    </div>
  </div>
  <?php include '../base/rodape.php'; ?>
  <?php if (isset($_SESSION['nome_usuario']) && isset($_SESSION['funcao_usuario'])): ?>
    <div class="usuario-logado">
      <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>
    </div>
  <?php endif; ?>
</body>
</html>
