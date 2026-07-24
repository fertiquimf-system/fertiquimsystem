<?php
require_once '../conexaohost/conexao.php';

if (isset($_POST['produtos']) && count($_POST['produtos']) > 0) {
    $ids = implode(",", array_map('intval', $_POST['produtos']));
    $sql = "SELECT * FROM estoque_fertilizantes WHERE id IN ($ids)";
    $result = $conn->query($sql);
} else {
    echo "<p>Nenhum produto selecionado.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Relatório de Estoque</title>
  <style>
    body { font-family: Arial, sans-serif; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #000; padding: 8px; text-align: center; }
    th { background: #f2f2f2; }
  </style>
</head>
<body>
  <h2>Relatório de Estoque</h2>
  <table>
    <thead>
      <tr>
        <th>Nome</th>
        <th>Quantidade</th>
        <th>Unidade</th>
        <th>Tipo</th>
        <th>Atualizado em</th>
        <th>Usuário</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
          <td><?php echo $row['nome_produto']; ?></td>
          <td><?php echo $row['quantidade']; ?></td>
          <td><?php echo $row['unidade']; ?></td>
          <td><?php echo $row['tipo']; ?></td>
          <td><?php echo date('d/m/Y H:i', strtotime($row['data_atualizacao'])); ?></td>
          <td><?php echo $row['usuario']; ?></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>

  <script>
    window.print(); // abre a janela de impressão automaticamente
  </script>
</body>
</html>
