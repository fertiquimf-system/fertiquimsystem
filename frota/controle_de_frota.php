<?php       
require_once '../conexaohost/conexao.php';
include('../sessao/verifica_sessao.php');

restringirAcesso(['Administrador', 'Proprietario', 'Gerencia']);

// Adicionar veículo
if (isset($_POST['adicionar'])) {
    $placa = $conn->real_escape_string($_POST['placa']);
    $modelo = $conn->real_escape_string($_POST['modelo']);
    $marca = $conn->real_escape_string($_POST['marca']);
    $ano = intval($_POST['ano']);
    $tipo = $conn->real_escape_string($_POST['tipo']);
    $status = $conn->real_escape_string($_POST['status']);
    $usuario = $_SESSION['nome_usuario'] ?? 'Desconhecido';

    $conn->query("INSERT INTO frota_veiculos 
        (placa, modelo, marca, ano, tipo, status, data_cadastro, usuario) 
        VALUES ('$placa', '$modelo', '$marca', $ano, '$tipo', '$status', NOW(), '$usuario')");

    header("Location: controle_de_frota.php");
    exit;
}

// Buscar veículos
$result = $conn->query("SELECT * FROM frota_veiculos ORDER BY data_cadastro DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Controle de Frota - Fertiquim</title>
  <link rel="stylesheet" href="../css/estilo.css">
  <style>
    .formulario, .tabela { margin-bottom: 20px; }
    .formulario input, .formulario select, .formulario button { padding: 6px; margin-right: 10px; }
  </style>
</head>
<body>
<?php include '../base/cabecalho.php'; ?>

<div class="container">
  <h2 class="titulo">Cadastro de Veículos</h2>

  <form method="POST" class="formulario">
    <input type="text" name="placa" placeholder="Placa" required>
    <input type="text" name="modelo" placeholder="Modelo" required>
    <input type="text" name="marca" placeholder="Marca" required>
    <input type="number" name="ano" placeholder="Ano" required min="1900" max="<?php echo date('Y'); ?>">
    
    <select name="tipo" required>
      <option value="">-- Tipo de Veículo --</option>
      <option value="Carro">Carro</option>
      <option value="Caminhão">Caminhão</option>
      <option value="Utilitário">Utilitário</option>
    </select>

    <select name="status" required>
      <option value="Ativo">Ativo</option>
      <option value="Inativo">Inativo</option>
    </select>

    <button type="submit" name="adicionar">Adicionar</button>
  </form>

  <h2 class="titulo">Veículos Cadastrados</h2>
  <table class="tabela">
    <thead>
      <tr>
        <th>Placa</th>
        <th>Modelo</th>
        <th>Marca</th>
        <th>Ano</th>
        <th>Tipo</th>
        <th>Status</th>
        <th>Data de Cadastro</th>
        <th>Usuário</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($row['placa']); ?></td>
          <td><?php echo htmlspecialchars($row['modelo']); ?></td>
          <td><?php echo htmlspecialchars($row['marca']); ?></td>
          <td><?php echo htmlspecialchars($row['ano']); ?></td>
          <td><?php echo htmlspecialchars($row['tipo']); ?></td>
          <td><?php echo htmlspecialchars($row['status']); ?></td>
          <td><?php echo date('d/m/Y H:i', strtotime($row['data_cadastro'])); ?></td>
          <td><?php echo htmlspecialchars($row['usuario']); ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php include '../base/rodape.php'; ?>
</body>
</html>
