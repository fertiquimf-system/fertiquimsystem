<?php
require_once '../conexaohost/conexao.php';
include('../sessao/verifica_sessao.php');

restringirAcesso(['Almoxarifado', 'Administrador', 'Gerencia']);

// Filtros
$nome = $_GET['nome_produto'] ?? '';
$data = $_GET['data'] ?? '';
$tipo = $_GET['tipo'] ?? '';

// Monta a query com filtros
$sql = "SELECT * FROM estoque_fertilizantes WHERE 1=1";

if (!empty($nome)) {
    $nome = $conn->real_escape_string($nome);
    $sql .= " AND nome_produto LIKE '%$nome%'";
}
if (!empty($data)) {
    $data = $conn->real_escape_string($data);
    $sql .= " AND DATE(data_atualizacao) = '$data'";
}
if (!empty($tipo)) {
    $tipo = $conn->real_escape_string($tipo);
    $sql .= " AND tipo = '$tipo'";
}

$sql .= " ORDER BY data_atualizacao DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerenciamento de Relatórios - Fertiquim</title>
  <link rel="stylesheet" href="../css/estilo.css">
  <style>
    .filtro-form {
      margin-bottom: 20px;
    }
    .filtro-form input,
    .filtro-form select,
    .filtro-form button {
      padding: 6px;
      margin-right: 10px;
    }
    .acoes {
      margin: 15px 0;
    }
  </style>
</head>
<body>
<?php include '../base/estoque.php'; ?>

<div class="container">
  <h2 class="titulo">Estoque Atual</h2>

  <!-- Formulário de filtro -->
  <form method="GET" class="filtro-form">
    <input type="text" name="nome_produto" placeholder="Nome do produto" value="<?php echo htmlspecialchars($nome); ?>">
    <input type="date" name="data" value="<?php echo htmlspecialchars($data); ?>">
    <select name="tipo">
      <option value="">-- Tipo --</option>
      <option value="Material de informática" <?php if ($tipo === 'Material de informática') echo 'selected'; ?>>Material de informática</option>
      <option value="Ferramentas" <?php if ($tipo === 'Ferramentas') echo 'selected'; ?>>Ferramentas</option>
      <option value="Matéria-prima" <?php if ($tipo === 'Matéria-prima') echo 'selected'; ?>>Matéria-prima</option>
      <option value="Material de Escritório" <?php if ($tipo === 'Material de Escritório') echo 'selected'; ?>>Material de Escritório</option>
      <option value="Saca 50kg" <?php if ($tipo === 'Saca 50kg') echo 'selected'; ?>>Sacaria</option>
      <option value="Materiais Elétricos" <?php if ($tipo === 'Materiais Elétricos') echo 'selected'; ?>>Materiais Elétricos</option>
      <option value="Materiais de Consumo" <?php if ($tipo === 'Materiais de Consumo') echo 'selected'; ?>>Materiais de Consumo</option>
    </select>
    <button type="submit">Filtrar</button>
  </form>

<!-- Formulário para gerar relatório -->
<form method="POST" action="relatorio.php" target="_blank">
  <div class="acoes">
    <button type="submit">Gerar Relatório</button>
  </div>

    <table class="tabela">
      <thead>
        <tr>
          <th><input type="checkbox" id="select-all"></th>
          <th>Nome do Produto</th>
          <th>Quantidade</th>
          <th>Unidade</th>
          <th>Tipo</th>
          <th>Atualizado em</th>
          <th>Usuário</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
              <td><input type="checkbox" name="produtos[]" value="<?php echo $row['id']; ?>"></td>
              <td><?php echo htmlspecialchars($row['nome_produto']); ?></td>
              <td><?php echo htmlspecialchars($row['quantidade']); ?></td>
              <td><?php echo htmlspecialchars($row['unidade']); ?></td>
              <td><?php echo htmlspecialchars($row['tipo']); ?></td>
              <td><?php echo date('d/m/Y H:i', strtotime($row['data_atualizacao'])); ?></td>
              <td><?php echo htmlspecialchars($row['usuario']); ?></td>
            </tr>
          <?php } ?>
        <?php else: ?>
          <tr><td colspan="7">Nenhum resultado encontrado.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </form>
</div>

<script>
// Marcar/desmarcar todos os checkboxes
document.getElementById('select-all').addEventListener('click', function(){
  let checkboxes = document.querySelectorAll('input[name="produtos[]"]');
  for (let checkbox of checkboxes) {
    checkbox.checked = this.checked;
  }
});
</script>

<?php include '../base/rodape.php'; ?>
  <?php include '../avisos/avisos_estoque.php'; ?>
</body>
</html>
