<?php
require_once '../conexaohost/conexao.php';
include('../sessao/verifica_sessao.php');

// consulta (usamos ms.* para não depender de um nome de coluna de data específico)
$sql = "SELECT ms.*, d.nome_produto
        FROM movimentacao_saida ms
        INNER JOIN deposito d ON ms.produto_id = d.id
        ORDER BY ms.id DESC";

$result = $conn->query($sql);
$erroQuery = null;
if ($result === false) {
    // guarda o erro para exibir no HTML (útil em dev). Em produção, só logue.
    $erroQuery = $conn->error . " — SQL: " . $sql;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Baixas Registradas</title>
  <link rel="stylesheet" href="../css/estilo.css">
  <style>
    .tabela-baixas {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }

    .tabela-baixas th, .tabela-baixas td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
        font-size: 14px;
    }

    .tabela-baixas th {
        background: #0d6efd;
        color: #fff;
    }

    .tabela-baixas tr:nth-child(even) {
        background: #f8f9fa;
    }

    .btn-voltar {
        background: #6c757d;
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        margin-top: 15px;
        transition: all 0.2s ease-in-out;
    }

    .btn-voltar:hover {
        background: #5a6268;
        transform: scale(1.02);
    }

    .mensagem-erro {
        background: #f8d7da;
        color: #842029;
        padding: 10px;
        border-radius: 6px;
        margin-top: 10px;
    }

    .mensagem-info {
        background: #e2e3e5;
        color: #41464b;
        padding: 10px;
        border-radius: 6px;
        margin-top: 10px;
    }
  </style>
</head>
<body>
<?php include '../base/deposito.php'; ?>

<div class="container">
  <h2>Baixas Registradas</h2>

  <?php if ($erroQuery): ?>
    <div class="mensagem-erro">
      <strong>Erro na consulta:</strong><br>
      <?= htmlspecialchars($erroQuery) ?>
    </div>
    <a href="baixa_deposito.php" class="btn-voltar">⬅ Voltar</a>
    <?php include '../base/rodape.php'; ?>
    </body>
    </html>
    <?php
    exit; // não tentar processar mais nada
  endif;
  ?>

  <?php if ($result && $result->num_rows > 0): ?>
    <table class="tabela-baixas">
      <tr>
        <th>ID</th>
        <th>Ticket</th>
        <th>Produto</th>
        <th>Quantidade</th>
        <th>Usuário</th>
        <th>Data/Hora</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()): 
            // tenta detectar um campo de data/horário comum
            $dateField = $row['data_registro'] ?? $row['data'] ?? $row['created_at'] ?? $row['data_hora'] ?? null;
            $formattedDate = $dateField ? date('d/m/Y H:i', strtotime($dateField)) : '-';
      ?>
        <tr>
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td><?= htmlspecialchars($row['ticket_id']) ?></td>
          <td><?= htmlspecialchars($row['nome_produto']) ?></td>
          <td><?= number_format((float)$row['quantidade'], 2, ',', '.') ?></td>
          <td><?= htmlspecialchars($row['usuario'] ?? '-') ?></td>
          <td><?= $formattedDate ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <div class="mensagem-info">Nenhuma baixa registrada até o momento.</div>
  <?php endif; ?>

  <a href="deposito.php" class="btn-voltar">⬅ Voltar</a>
</div>

<?php include '../base/rodape.php'; ?>
</body>
</html>
