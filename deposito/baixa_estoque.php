<?php
require_once '../conexaohost/conexao.php';
include('../sessao/verifica_sessao.php');

$mensagem = "";

// Detecta automaticamente coluna de data/hora na tabela movimentacao_saida
$timestamp_col = null;
$colRes = $conn->query("SHOW COLUMNS FROM movimentacao_saida");
if ($colRes) {
    while ($col = $colRes->fetch_assoc()) {
        $f = $col['Field'];
        if (in_array($f, ['data_registro', 'data_hora', 'created_at', 'created', 'data', 'timestamp'])) {
            $timestamp_col = $f;
            break;
        }
    }
}

// Se enviou o form de baixa
if (isset($_POST['dar_baixa'])) {
    $ticket_id = intval($_POST['ticket_id']);
    $produto_id = intval($_POST['produto_id']);
    $quantidade = floatval($_POST['quantidade']);
    $usuario = $_SESSION['nome_usuario'] ?? 'Desconhecido';

    // Busca produto no depósito
    $produto = $conn->query("SELECT quantidade FROM deposito WHERE id = $produto_id")->fetch_assoc();

    if ($produto && $produto['quantidade'] >= $quantidade) {

        // Atualiza depósito (prepared)
        $nova_qtd = $produto['quantidade'] - $quantidade;
        $stmtUp = $conn->prepare("UPDATE deposito SET quantidade = ?, data_atualizacao = NOW(), usuario = ? WHERE id = ?");
        if ($stmtUp) {
            $stmtUp->bind_param("dsi", $nova_qtd, $usuario, $produto_id);
            $stmtUp->execute();
            $stmtUp->close();
        } else {
            $mensagem = "❌ Erro ao preparar atualização do depósito: " . $conn->error;
        }

        // Insere na movimentacao_saida (com ou sem coluna de data/hora)
        if (!$mensagem) {
            if ($timestamp_col) {
                $sql = "INSERT INTO movimentacao_saida (ticket_id, produto_id, quantidade, usuario, $timestamp_col) VALUES (?, ?, ?, ?, NOW())";
            } else {
                $sql = "INSERT INTO movimentacao_saida (ticket_id, produto_id, quantidade, usuario) VALUES (?, ?, ?, ?)";
            }

            $stmtIns = $conn->prepare($sql);
            if ($stmtIns) {
                // tipos: i = int, i = int, d = double, s = string  -> "iids"
                $stmtIns->bind_param("iids", $ticket_id, $produto_id, $quantidade, $usuario);
                $ok = $stmtIns->execute();
                if ($ok) {
                    $mensagem = "✅ Baixa registrada com sucesso!";
                } else {
                    $mensagem = "❌ Erro ao registrar baixa: " . $stmtIns->error;
                }
                $stmtIns->close();
            } else {
                $mensagem = "❌ Erro ao preparar insert: " . $conn->error;
            }
        }

    } else {
        $mensagem = "❌ Quantidade insuficiente no depósito!";
    }
}

// Se pesquisou um ticket
$ticketData = null;
if (isset($_GET['ticket_id'])) {
    $ticket_id = intval($_GET['ticket_id']);
    $ticketData = $conn->query("SELECT * FROM balanca_saida WHERE id = $ticket_id")->fetch_assoc();
}

// Pega lista de produtos do depósito
$produtos = $conn->query("SELECT * FROM deposito ORDER BY nome_produto ASC");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Baixa no Depósito</title>
  <link rel="stylesheet" href="../css/estilo.css">
  <style>
    /* ---- TABELAS ---- */
    .tabela-ticket, .tabela-baixa {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        margin-bottom: 20px;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }

    .tabela-ticket th, .tabela-ticket td,
    .tabela-baixa th, .tabela-baixa td {
        border: 1px solid #ddd;
        padding: 10px 12px;
    }

    .tabela-ticket th {
        background: #f5f5f5;
        font-weight: bold;
        text-align: left;
        width: 30%;
    }

    .tabela-baixa th {
        background: #d1e7dd;
        text-align: center;
        font-weight: bold;
    }

    .tabela-baixa td {
        text-align: center;
    }

    /* ---- INPUTS ---- */
    select, input[type="number"] {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 6px;
        width: 90%;
        font-size: 14px;
    }

    /* ---- BOTÃO ---- */
    .btn-baixa {
        background: #198754;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 8px 18px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }

    .btn-baixa:hover {
        background: #157347;
        transform: scale(1.05);
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    /* Mensagem de alerta */
    .alerta {
        padding: 12px;
        border-radius: 6px;
        font-weight: bold;
        margin-top: 10px;
        text-align: center;
    }

    .alerta.sucesso {
        background: #d1e7dd;
        color: #0f5132;
    }

    .alerta.erro {
        background: #f8d7da;
        color: #842029;
    }
  </style>
</head>
<body>
<?php include '../base/deposito.php'; ?>

<div class="container">
  <h2>Baixa no Depósito</h2>

  <!-- Buscar ticket -->
  <form method="get" style="margin-bottom: 15px;">
    <label>Digite o nº do Ticket:</label>
    <input type="number" name="ticket_id" required>
    <button type="submit" class="btn-baixa">Buscar</button>
  </form>

  <?php if ($ticketData): ?>
    <h3>Dados do Ticket</h3>
    <table class="tabela-ticket">
      <tr><th>ID</th><td><?= htmlspecialchars($ticketData['id']) ?></td></tr>
      <tr><th>Placa</th><td><?= htmlspecialchars($ticketData['placa']) ?></td></tr>
      <tr><th>Peso Saída</th><td><?= htmlspecialchars($ticketData['peso_saida']) ?> kg</td></tr>
      <tr><th>Data</th><td><?= htmlspecialchars($ticketData['data_saida']) ?></td></tr>
    </table>

    <h3>Dar Baixa</h3>
    <form method="post">
      <input type="hidden" name="ticket_id" value="<?= htmlspecialchars($ticketData['id']) ?>">
      <table class="tabela-baixa">
        <tr>
          <th>Produto</th>
          <th>Quantidade</th>
          <th>Ação</th>
        </tr>
        <tr>
          <td>
            <select name="produto_id" required>
              <option value="">Selecione...</option>
              <?php while ($row = $produtos->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['id']) ?>"><?= htmlspecialchars($row['nome_produto']) ?> (<?= htmlspecialchars($row['quantidade']) ?> <?= htmlspecialchars($row['unidade']) ?>)</option>
              <?php endwhile; ?>
            </select>
          </td>
          <td><input type="number" step="0.01" name="quantidade" required></td>
          <td><button type="submit" name="dar_baixa" class="btn-baixa">Confirmar</button></td>
        </tr>
      </table>
    </form>
  <?php endif; ?>

  <?php if ($mensagem): ?>
    <div class="alerta <?= strpos($mensagem, '✅') !== false ? 'sucesso' : 'erro' ?>">
        <?= htmlspecialchars($mensagem) ?>
    </div>
  <?php endif; ?>

</div>

<?php include '../base/rodape.php'; ?>
</body>
</html>
