<?php
require_once '../conexaohost/conexao.php';
session_start();

// Verifica se está logado
if (!isset($_SESSION['nome_usuario'])) {
    header('Location: ../pglogin/pglogin.php');
    exit;
}

$funcionario_id = $_GET['funcionario_id'] ?? null;
$funcionario_nome = "";
$itens_inventario = [];

if ($funcionario_id) {
    // Nome do funcionário
    $stmt_nome = $conn->prepare("SELECT nome FROM cadastro_funcionario WHERE id = ?");
    $stmt_nome->bind_param("i", $funcionario_id);
    $stmt_nome->execute();
    $res_nome = $stmt_nome->get_result()->fetch_assoc();
    $funcionario_nome = $res_nome['nome'] ?? "";

    // Itens do inventário
    $stmt = $conn->prepare("
        SELECT ef.nome_produto, inv.quantidade, inv.data_entrega 
        FROM inventario_funcionario AS inv
        JOIN estoque_fertilizantes AS ef ON inv.item_id = ef.id
        WHERE inv.funcionario_id = ?
        ORDER BY inv.data_entrega DESC
    ");
    $stmt->bind_param("i", $funcionario_id);
    $stmt->execute();
    $itens_inventario = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Inventário - Impressão</title>
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 30px;
  }
  .cabecalho {
    text-align: center;
    margin-bottom: 20px;
  }
  .cabecalho img {
    max-height: 80px;
    margin-bottom: 10px;
  }
  .cabecalho h2 {
    margin: 0;
  }
  .dados-func {
    margin-bottom: 20px;
    font-size: 16px;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
  }
  table, th, td {
    border: 1px solid #333;
  }
  th, td {
    padding: 8px;
    text-align: center;
  }
  th {
    background: #f2f2f2;
  }
  .ok-col {
    width: 80px;
  }
  .assinatura {
    margin-top: 50px;
    display: flex;
    justify-content: space-between;
  }
  .assinatura div {
    width: 45%;
    text-align: center;
    border-top: 1px solid #000;
    padding-top: 5px;
  }
</style>
</head>
<body onload="window.print()">

<div class="cabecalho">
  <img src="../img/logo.jpg" alt="Logo Fertiquim">
  <h2>Relatório de Inventário do Funcionário</h2>
</div>

<div class="dados-func">
  <strong>Funcionário:</strong> <?= htmlspecialchars($funcionario_nome) ?><br>
  <strong>Data da emissão:</strong> <?= date('d/m/Y H:i') ?>
</div>

<?php if ($itens_inventario && $itens_inventario->num_rows > 0): ?>
<table>
  <thead>
    <tr>
      <th>Item</th>
      <th>Quantidade</th>
      <th>Data da Entrega</th>
      <th class="ok-col">OK</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $itens_inventario->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['nome_produto']) ?></td>
        <td><?= htmlspecialchars($row['quantidade']) ?></td>
        <td><?= date('d/m/Y H:i', strtotime($row['data_entrega'])) ?></td>
        <td></td> <!-- Campo em branco para marcar o "OK" -->
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
<?php else: ?>
<p>Nenhum item encontrado.</p>
<?php endif; ?>

<div class="assinatura">
  <div>Assinatura do Responsável</div>
  <div>Assinatura do Funcionário</div>
</div>

</body>
</html>
