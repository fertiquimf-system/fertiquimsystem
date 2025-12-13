<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

// Buscar somente saídas e completar com dados da entrada
$sql = "
    SELECT 
        s.id,
        'Saída' AS tipo,
        COALESCE(e.marca, '-') AS marca,
        s.placa,
        COALESCE(e.motorista, '-') AS motorista,
        s.peso_saida AS peso,
        s.data_saida AS data_registro
    FROM balanca_saida s
    
    -- Seleciona somente a ÚLTIMA entrada para cada placa
    LEFT JOIN (
        SELECT 
            placa,
            MAX(id) AS max_id
        FROM balanca_entrada
        GROUP BY placa
    ) ult ON ult.placa = s.placa

    -- Junta a entrada correta (somente 1)
    LEFT JOIN balanca_entrada e ON e.id = ult.max_id

    ORDER BY s.data_saida DESC
";
$registros = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Canhotos Salvos</title>
  <link rel="stylesheet" href="../css/estilo.css"/>
<style>
  table { width: 100%; border-collapse: collapse; margin-top: 20px; }
  th, td { padding: 12px; border: 1px solid #2d2d2d; text-align: center; }
  
  th { background: #4CAF50; color: #fff; font-weight: bold; }
  tr:nth-child(even) { background: #fff; }
  tr:nth-child(odd) { background: #fff; }
  tr:hover { background: #3c3c3c; color: #fff; }

  .btn { padding: 8px 14px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; }
  .btn-green { background: #4CAF50; color: #fff; }
</style>
</head>
<body>
<?php include '../base/cabecalho.php'; ?>

<h1>📑 Canhotos Salvos</h1>
<table>
  <tr>
    <th>ID</th>
    <th>Tipo</th>
    <th>Marca</th>
    <th>Placa</th>
    <th>Motorista</th>
    <th>Peso (kg)</th>
    <th>Data</th>
    <th>Ações</th>
  </tr>
  <?php while ($row = $registros->fetch_assoc()): ?>
  <tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['tipo'] ?></td>
    <td><?= $row['marca'] ?></td>
    <td><?= $row['placa'] ?></td>
    <td><?= $row['motorista'] ?></td>
    <td><?= number_format($row['peso'], 0, ',', '.') ?></td>
    <td><?= date("d/m/Y H:i", strtotime($row['data_registro'])) ?></td>
    <td>
<button 
  class="btn btn-green" 
  onclick="window.open('gerar_canhoto.php?tipo=saida&id=<?= $row['id'] ?>', '_blank')"
>
  🧾 Gerar Canhoto
</button>
    </td>
  </tr>
  <?php endwhile; ?>
</table>
  <?php 
  include '../base/rodape.php';
  ?>
</body>
</html>
