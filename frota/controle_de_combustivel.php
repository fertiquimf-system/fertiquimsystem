<?php
require_once '../conexaohost/conexao.php';
include('../sessao/verifica_sessao.php');

restringirAcesso(['Administrador', 'Proprietario', 'Gerencia']);

// Adicionar abastecimento
if (isset($_POST['adicionar'])) {
    $veiculo_id = intval($_POST['veiculo_id']);
    $posto = $conn->real_escape_string($_POST['posto']);
    $valor = floatval($_POST['valor']);
    $litros = floatval($_POST['litros']);
    $km = floatval($_POST['km']);
    $usuario = $_SESSION['nome_usuario'] ?? 'Desconhecido';

    if ($litros > 0) {
        $preco_litro = $valor / $litros;
    } else {
        $preco_litro = 0;
    }

    $conn->query("INSERT INTO controle_combustivel
        (veiculo_id, posto, valor, litros, preco_litro, km, data, usuario)
        VALUES ($veiculo_id, '$posto', $valor, $litros, $preco_litro, $km, NOW(), '$usuario')");

    header("Location: controle_de_combustivel.php");
    exit;
}

// Buscar veículos para dropdown
$veiculos = $conn->query("SELECT id, placa, modelo, marca FROM frota_veiculos ORDER BY placa ASC");

// Buscar histórico de abastecimentos
$historico = $conn->query("SELECT c.*, v.placa, v.modelo FROM controle_combustivel c
                           JOIN frota_veiculos v ON c.veiculo_id = v.id
                           ORDER BY c.data DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Controle de Combustível - Fertiquim</title>
  <link rel="stylesheet" href="../css/estilo.css">
  <style>
    .formulario, .tabela { margin-bottom: 20px; }
    .formulario input, .formulario select, .formulario button { padding: 6px; margin-right: 10px; }
    .tabela th, .tabela td { padding: 8px; border: 1px solid #ccc; text-align: center; }
    .tabela { border-collapse: collapse; width: 100%; }
    th { background: #f0f0f0; }
  </style>
</head>
<body>
<?php include '../base/cabecalho.php'; ?>

<div class="container">
  <h2 class="titulo">Controle de Abastecimento</h2>

  <form method="POST" class="formulario">
    <select name="veiculo_id" required>
      <option value="">-- Selecione o Veículo --</option>
      <?php while($v = $veiculos->fetch_assoc()): ?>
        <option value="<?php echo $v['id']; ?>">
          <?php echo $v['placa'] . " - " . $v['modelo'] . " (" . $v['marca'] . ")"; ?>
        </option>
      <?php endwhile; ?>
    </select>

    <input type="text" name="posto" placeholder="Nome do Posto" required>
    <input type="number" step="0.01" name="valor" placeholder="Valor (R$)" required>
    <input type="number" step="0.01" name="litros" placeholder="Litros Abastecidos" required>
    <input type="number" step="0.1" name="km" placeholder="Quilometragem Atual" required>
    <button type="submit" name="adicionar">Registrar Abastecimento</button>
  </form>

  <h2 class="titulo">Histórico de Abastecimentos</h2>
  <table class="tabela">
    <thead>
      <tr>
        <th>Veículo</th>
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
      <?php if ($historico && $historico->num_rows > 0): ?>
        <?php while($h = $historico->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($h['placa'] . " - " . $h['modelo']); ?></td>
            <td><?php echo htmlspecialchars($h['posto']); ?></td>
            <td><?php echo number_format($h['valor'], 2, ',', '.'); ?></td>
            <td><?php echo number_format($h['litros'], 2, ',', '.'); ?></td>
            <td><?php echo number_format($h['preco_litro'], 2, ',', '.'); ?></td>
            <td><?php echo number_format($h['km'], 1, ',', '.'); ?></td>
            <td><?php echo date('d/m/Y H:i', strtotime($h['data'])); ?></td>
            <td><?php echo htmlspecialchars($h['usuario']); ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="8">Nenhum abastecimento registrado.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include '../base/rodape.php'; ?>
</body>
</html>
