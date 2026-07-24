<?php
require_once '../conexaohost/conexao.php';
include('../sessao/verifica_sessao.php');

restringirAcesso(['Administrador', 'Proprietario', 'Gerencia']);

// Recebe parâmetros
$veiculo_id = intval($_GET['veiculo_id'] ?? 0);
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';

if ($veiculo_id <= 0) {
    echo "<p>Veículo inválido.</p>";
    exit;
}

// Busca veículo
$veiculo = $conn->query("SELECT * FROM frota_veiculos WHERE id = $veiculo_id")->fetch_assoc();
if (!$veiculo) {
    echo "<p>Veículo não encontrado.</p>";
    exit;
}

// Monta query do histórico com filtros
$sql = "SELECT * FROM controle_combustivel WHERE veiculo_id = $veiculo_id";

if (!empty($data_inicio)) {
    $inicio = $conn->real_escape_string($data_inicio);
    $sql .= " AND DATE(data) >= '$inicio'";
}
if (!empty($data_fim)) {
    $fim = $conn->real_escape_string($data_fim);
    $sql .= " AND DATE(data) <= '$fim'";
}

$sql .= " ORDER BY data DESC";
$historico = $conn->query($sql);

// Calcula totais
$total_valor = 0;
$total_litros = 0;
$registros = [];
while ($h = $historico->fetch_assoc()) {
    $total_valor += $h['valor'];
    $total_litros += $h['litros'];
    $registros[] = $h;
}
$media_preco_litro = ($total_litros > 0) ? $total_valor / $total_litros : 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Relatório de Combustível - <?php echo htmlspecialchars($veiculo['placa']); ?></title>
<style>
  body { font-family: Arial, sans-serif; padding: 20px; }
  h2 { margin-bottom: 5px; }
  table { width: 100%; border-collapse: collapse; margin-top: 20px; }
  th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
  th { background: #f0f0f0; }
  .totais { margin-top: 20px; font-weight: bold; }
</style>
</head>
<body>
<h2>Relatório de Combustível</h2>
<p><strong>Veículo:</strong> <?php echo htmlspecialchars($veiculo['placa'] . " - " . $veiculo['modelo']); ?></p>
<p><strong>Marca:</strong> <?php echo htmlspecialchars($veiculo['marca']); ?></p>
<?php if($data_inicio || $data_fim): ?>
<p><strong>Período:</strong> <?php echo htmlspecialchars($data_inicio ?: 'Início'); ?> até <?php echo htmlspecialchars($data_fim ?: 'Hoje'); ?></p>
<?php endif; ?>

<table>
<thead>
<tr>
<th>Posto</th>
<th>Valor (R$)</th>
<th>Litros</th>
<th>Preço/Litro (R$)</th>
<th>KM</th>
<th>Data</th>
<th>Usuário</th>
</tr>
</thead>
<tbody>
<?php if (!empty($registros)): ?>
  <?php foreach ($registros as $h): ?>
    <tr>
      <td><?php echo htmlspecialchars($h['posto']); ?></td>
      <td><?php echo number_format($h['valor'], 2, ',', '.'); ?></td>
      <td><?php echo number_format($h['litros'], 2, ',', '.'); ?></td>
      <td><?php echo number_format($h['preco_litro'], 2, ',', '.'); ?></td>
      <td><?php echo number_format($h['km'], 1, ',', '.'); ?></td>
      <td><?php echo date('d/m/Y H:i', strtotime($h['data'])); ?></td>
      <td><?php echo htmlspecialchars($h['usuario']); ?></td>
    </tr>
  <?php endforeach; ?>
<?php else: ?>
  <tr><td colspan="7">Nenhum abastecimento registrado.</td></tr>
<?php endif; ?>
</tbody>
</table>

<div class="totais">
<p>Total gasto: R$ <?php echo number_format($total_valor, 2, ',', '.'); ?></p>
<p>Total de litros: <?php echo number_format($total_litros, 2, ',', '.'); ?> L</p>
<p>Média do preço por litro: R$ <?php echo number_format($media_preco_litro, 2, ',', '.'); ?></p>
</div>

<script>
  window.print(); // abre a janela de impressão automaticamente
</script>
</body>
</html>
