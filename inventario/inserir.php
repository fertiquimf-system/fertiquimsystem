<?php
require_once '../conexaohost/conexao.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['nome_usuario'])) {
    header('Location: ../pglogin/pglogin.php');
    exit;
}

// Processamento do formulário
$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $funcionario_id = intval($_POST['funcionario_id']);
    $item_id = intval($_POST['item_id']);
    $quantidade = intval($_POST['quantidade']);
    $usuario = $conn->real_escape_string($_SESSION['nome_usuario']);

    $res = $conn->query("SELECT quantidade FROM estoque_fertilizantes WHERE id = $item_id");
    $estoque = $res->fetch_assoc();

    if ($estoque && $estoque['quantidade'] >= $quantidade) {
        // Registra a entrega no inventário do funcionário
        $conn->query("INSERT INTO inventario_funcionario (funcionario_id, item_id, quantidade) 
                      VALUES ($funcionario_id, $item_id, $quantidade)");

        // Atualiza o estoque, quem alterou e a data
        $conn->query("UPDATE estoque_fertilizantes 
                      SET quantidade = quantidade - $quantidade, 
                          usuario = '$usuario', 
                          data_atualizacao = NOW() 
                      WHERE id = $item_id");

        $mensagem = "<p style='color:green;'>Item entregue com sucesso!</p>";
    } else {
        $mensagem = "<p style='color:red;'>Quantidade insuficiente no estoque.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventário de Funcionário - Fertiquim</title>
  <link rel="stylesheet" href="../css/estilo.css">
  <style>
    .form-entrega {
      max-width: 600px;
      background: #f9f9f9;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    .form-entrega label {
      font-weight: bold;
      margin-top: 10px;
      display: block;
    }
    .form-entrega select,
    .form-entrega input {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      margin-bottom: 15px;
    }
    .form-entrega button {
      padding: 10px 15px;
    }
    .mensagem {
      margin: 10px 0;
      font-weight: bold;
    }
    .usuario-logado {
      position: fixed;
      bottom: 10px;
      right: 10px;
      background: #eee;
      padding: 5px 10px;
      border-radius: 5px;
      font-size: 14px;
    }
  </style>
</head>
<body>
<?php include '../base/estoque.php'; ?>
<div class="container">
  <h2 class="titulo">Entrega de Itens para Funcionário</h2>

  <?php if (!empty($mensagem)) echo "<div class='mensagem'>$mensagem</div>"; ?>

  <form method="POST" class="form-entrega">
    <label for="funcionario_id">Funcionário:</label>
    <select name="funcionario_id" required>
      <option value="">-- Selecione --</option>
      <?php
      $funcs = $conn->query("SELECT id, nome FROM cadastro_funcionario");
      while ($f = $funcs->fetch_assoc()):
      ?>
        <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nome']) ?></option>
      <?php endwhile; ?>
    </select>

    <label for="item_id">Item do Estoque:</label>
    <select name="item_id" required>
      <option value="">-- Selecione --</option>
      <?php
      $items = $conn->query("SELECT id, nome_produto, quantidade FROM estoque_fertilizantes");
      while ($i = $items->fetch_assoc()):
      ?>
        <option value="<?= $i['id'] ?>">
          <?= htmlspecialchars($i['nome_produto']) ?> (<?= $i['quantidade'] ?> disponíveis)
        </option>
      <?php endwhile; ?>
    </select>

    <label for="quantidade">Quantidade a Entregar:</label>
    <input type="number" name="quantidade" min="1" required>

    <button type="submit">Entregar</button>
  </form>
</div>

  <?php 
  include '../base/rodape.php';
  ?>

</body>
</html>
