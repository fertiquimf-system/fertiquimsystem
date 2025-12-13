<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

$sql = "SELECT * FROM entradas ORDER BY data DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Erro na consulta: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Consultar Despesas</title>
  <link rel="stylesheet" href="../css/estilo.css">
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background: #fff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px;
      text-align: center;
    }
    th {
      background-color: #444;
      color: #fff;
    }
    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
    .btn-descricao {
      background-color: #444;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
    }
    .btn-descricao:hover {
      background-color: #333;
    }
    .descricao-row {
      display: none;
      background: #f4f4f4;
    }
    .descricao-cell {
      text-align: left;
      padding: 15px;
      font-style: italic;
      color: #333;
    }
  </style>
  <script>
    function toggleDescricao(id) {
      var row = document.getElementById("desc_" + id);
      if (row.style.display === "none" || row.style.display === "") {
        row.style.display = "table-row";
      } else {
        row.style.display = "none";
      }
    }
  </script>
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
    <h2 class="titulo">Relatório de Despesas</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Valor</th>
          <th>Tipo</th>
          <th>Data</th>
          <th>Ação</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo $row['id']; ?></td>
              <td><?php echo htmlspecialchars($row['nome']); ?></td>
              <td style="color:green; font-weight:bold;">
                R$ <?php echo number_format($row['valor'], 2, ',', '.'); ?>
              </td>
              <td><?php echo htmlspecialchars($row['tipo']); ?></td>
              <td><?php echo date("d/m/Y", strtotime($row['data'])); ?></td>
              <td>
                <button class="btn-descricao" onclick="toggleDescricao(<?php echo $row['id']; ?>)">Descrição</button>
              </td>
            </tr>
            <tr id="desc_<?php echo $row['id']; ?>" class="descricao-row">
              <td colspan="6" class="descricao-cell">
                <?php echo nl2br(htmlspecialchars($row['descricao'])); ?>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6">Nenhuma entrada encontrada.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php 
  include '../base/rodape.php';
  ?>
</body>
</html>
