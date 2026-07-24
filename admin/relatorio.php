<?php
require_once '../conexaohost/conexao.php';
session_start();

include('../sessao/verifica_sessao.php');
restringirAcesso(['Gerencia', 'Administrador', 'Proprietario']);

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
<?php include '../base/administrador.php'; ?>
  <div class="container">
    <h2 class="titulo">Painel Principal</h2>
    <div class="cards">
     <a href="lucro.php" class="card lucro-card">
     <h2></h2>
     </a>

     <a href="despesas.php" class="card prejuizo-card">
     <h2></h2>
     </a>

    </div>
  </div>

  <footer>
    &copy; 2025 Fertiquim Fertilizantes. Todos os direitos reservados.
  </footer>

  <?php if (isset($_SESSION['nome_usuario']) && isset($_SESSION['funcao_usuario'])): ?>
    <div class="usuario-logado">
      <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>
    </div>
  <?php endif; ?>
</body>
</html>
