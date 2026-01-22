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
/* ====== TABELA ====== */
.table-container {
  margin-top: 20px;
  overflow-x: auto;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0,0,0,.15);
}

table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  font-size: 14px;
}

/* Cabeçalho */
th {
  background: linear-gradient(135deg, #4CAF50, #3e9142);
  color: #fff;
  padding: 14px;
  text-transform: uppercase;
  font-size: 13px;
  position: sticky;
  top: 0;
  z-index: 2;
}

/* Células */
td {
  padding: 12px;
  border-bottom: 1px solid #e0e0e0;
  text-align: center;
  color: #333;
}

/* Linhas */
tr:nth-child(even) {
  background: #f9f9f9;
}

tr:hover {
  background: #e8f5e9;
  transition: background 0.2s ease-in-out;
}

/* ====== BOTÕES ====== */
.btn {
  padding: 8px 16px;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  font-size: 13px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s ease-in-out;
}

.btn-green {
  background: #4CAF50;
  color: #fff;
}

.btn-green:hover {
  background: #3e9142;
  transform: scale(1.05);
}

/* ====== TÍTULO ====== */
h1 {
  margin-top: 20px;
  font-size: 22px;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* ====== RESPONSIVO ====== */
@media (max-width: 768px) {
  th, td {
    padding: 10px;
    font-size: 12px;
  }

  h1 {
    font-size: 18px;
  }
}
</style>
</head>
<body>
<?php include '../base/cabecalho.php'; ?>

<h1>📑 Canhotos Salvos</h1>
<div class="table-container">
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
      <td><strong><?= $row['placa'] ?></strong></td>
      <td><?= $row['motorista'] ?></td>
      <td><?= number_format($row['peso'], 0, ',', '.') ?></td>
      <td><?= date("d/m/Y H:i", strtotime($row['data_registro'])) ?></td>
      <td>
        <button 
          class="btn btn-green"
          onclick="window.open('gerar_canhoto.php?tipo=saida&id=<?= $row['id'] ?>', '_blank')"
        >
          🧾 Canhoto
        </button>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
  <?php 
  include '../base/rodape.php';
  ?>
</body>
</html>
