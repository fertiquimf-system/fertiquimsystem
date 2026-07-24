<?php
session_start();
require_once '../conexaohost/conexao.php';
include '../base/cabecalho.php';

// Buscar clientes
$sql = "SELECT id, tipo_pessoa, nome_razao, cpf_cnpj, rg_ie, data_nascimento, telefone, celular, email, endereco, numero, complemento 
        FROM clientes ORDER BY nome_razao ASC";
$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Consulta de Clientes</title>
  <link rel="stylesheet" href="../css/estilo.css">
  <style>
    body {
      
      font-family: "Poppins", Arial, sans-serif;
      color: #444;
    }
    .container {
      max-width: 1100px;
      margin: 30px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }
    h2 {
      font-size: 24px;
      font-weight: 600;
      margin-bottom: 20px;
      color: #2c3e50;
      text-align: center;
    }
    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0 10px;
    }
    th {
      background: none;
      color: #888;
      font-size: 13px;
      text-transform: uppercase;
      padding: 8px;
      text-align: left;
    }
    td {
      background: #fff;
      padding: 16px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      font-size: 14px;
    }
    tr.details td {
      background: #f9fafc;
      box-shadow: none;
      font-size: 13px;
    }
    .btn {
      display: inline-block;
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 500;
      text-decoration: none;
      color: #fff;
      transition: all 0.3s;
    }
    .btn:hover { opacity: 0.9; }
    .btn-edit { background: #3498db; }
    .btn-delete { background: #e74c3c; }
    .btn-expand { background: #2ecc71; }
    .actions {
      display: flex;
      gap: 10px;
    }
    /* animação expandir */
    .details {
      display: none;
    }
  </style>
  <script>
    function toggleDetails(id) {
      const row = document.getElementById('details-' + id);
      row.style.display = (row.style.display === 'table-row') ? 'none' : 'table-row';
    }
  </script>
</head>
<body>
<div class="container">
  <h2>📑 Consulta de Clientes</h2>

  <table>
    <thead>
      <tr>
        <th>Nome/Razão</th>
        <th>CPF/CNPJ</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php if($res && $res->num_rows > 0): ?>
        <?php while($row = $res->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['nome_razao']); ?></td>
            <td><?php echo $row['cpf_cnpj']; ?></td>
            <td class="actions">
              <button class="btn btn-expand" onclick="toggleDetails(<?php echo $row['id']; ?>)">Detalhes</button>
            </td>
          </tr>
          <tr class="details" id="details-<?php echo $row['id']; ?>">
            <td colspan="3">
              <strong>Tipo:</strong> <?php echo $row['tipo_pessoa']; ?><br>
              <strong>RG/IE:</strong> <?php echo $row['rg_ie']; ?><br>
              <strong>Data Nasc.:</strong> <?php echo $row['data_nascimento']; ?><br>
              <strong>Celular:</strong> <?php echo $row['telefone']; ?><br>
              <strong>Telefone:</strong> <?php echo $row['celular']; ?><br>
              <strong>Email:</strong> <?php echo $row['email']; ?><br>
              <strong>Endereço:</strong> <?php echo $row['endereco'] . ', ' . $row['numero']; ?><br>
              <strong>Complemento:</strong> <?php echo $row['complemento']; ?><br><br>

              <a href="editar_cliente.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">Editar</a>
              <a href="deletar_cliente.php?id=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Deseja realmente excluir este cliente?');">Excluir</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="3">Nenhum cliente encontrado.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php include '../base/rodape.php';?>
</body>
</html>
