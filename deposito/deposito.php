<?php
require_once '../conexaohost/conexao.php';
include('../sessao/verifica_sessao.php');

// Adicionar novo produto ou somar estoque
if (isset($_POST['adicionar'])) {
    $nome = $_POST['nome'];
    $quantidade = floatval($_POST['quantidade']);
    $unidade = $_POST['unidade'];
    $tipo = $_POST['tipo'];
    $usuario = $_SESSION['nome_usuario'] ?? 'Desconhecido';

    $check = $conn->query("SELECT id, quantidade FROM deposito 
                           WHERE nome_produto = '$nome' AND unidade = '$unidade'");

    if ($check && $check->num_rows > 0) {
        $row = $check->fetch_assoc();
        $nova_qtd = $row['quantidade'] + $quantidade;

        $conn->query("UPDATE deposito 
                      SET quantidade = $nova_qtd, 
                          data_atualizacao = NOW(), 
                          usuario = '$usuario' 
                      WHERE id = {$row['id']}");
    } else {
        $conn->query("INSERT INTO deposito 
                      (nome_produto, quantidade, unidade, tipo, data_atualizacao, usuario) 
                      VALUES ('$nome', $quantidade, '$unidade', '$tipo', NOW(), '$usuario')");
    }

    header("Location: deposito.php");
    exit;
}
//FUNÇÃO PARA PUXAR O USUÁRIO DO SISTEMA PARA REALIZAR AS ALTERAÇÕES
  function temPermissao($tipo_usuario) {
    $tipo = strtolower(trim($tipo_usuario));
    return ($tipo === 'gerencia' || $tipo === 'administrador');
}

// Remover produto (apenas administrador)
if (isset($_GET['remover'])) {
    $tipo_usuario = $_SESSION['funcao_usuario'] ?? '';
    if (!temPermissao($tipo_usuario)) {
        echo "<script>
                alert('Você não tem permissão para remover este item.');
                window.location.href='deposito.php';
              </script>";
        exit;
    }

    $id = intval($_GET['remover']);
    $conn->query("DELETE FROM deposito WHERE id = $id");
    header("Location: deposito.php");
    exit;
}

// Editar produto
if (isset($_GET['editar'])) {
    $tipo_usuario = $_SESSION['funcao_usuario'] ?? '';
    if (!temPermissao($tipo_usuario)) {
        echo "<script>
                alert('Você não tem permissão para editar este item.');
                window.location.href='deposito.php';
              </script>";
        exit;
    }

    $id = intval($_GET['editar']);
    $editar = $conn->query("SELECT * FROM deposito WHERE id = $id")->fetch_assoc();
}

// Atualizar produto
if (isset($_POST['atualizar'])) {
    $tipo_usuario = $_SESSION['funcao_usuario'] ?? '';
    if (!temPermissao($tipo_usuario)) {
        echo "<script>
                alert('Você não tem permissão para atualizar este item.');
                window.location.href='deposito.php';
              </script>";
        exit;
    }

    $id = intval($_POST['id']);
    $nome = $_POST['nome'];
    $quantidade = floatval($_POST['quantidade']);
    $usuario = $_SESSION['nome_usuario'] ?? 'Desconhecido';

    $conn->query("UPDATE deposito 
                  SET nome_produto = '$nome',
                      quantidade = $quantidade, 
                      data_atualizacao = NOW(), 
                      usuario = '$usuario' 
                  WHERE id = $id");

    header("Location: deposito.php");
    exit;
}

$result = $conn->query("SELECT * FROM deposito ORDER BY data_atualizacao DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Depósito - Fertiquim</title>
  <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
<?php include '../base/deposito.php'; ?>

<div class="container">
  <h2 class="titulo">Adicionar Fertilizante ao Depósito</h2>
  <form method="post" class="formulario">
    <input type="text" name="nome" placeholder="Nome do Produto" required>
    <input type="number" name="quantidade" placeholder="Quantidade" required step="0.01">
    <input type="text" name="unidade" placeholder="Unidade (ex: kg, ton, sacas)" required>
    
    <select name="tipo" required>
      <option value="">Selecione</option>
      <option value="Toneladas/BigBag">Toneladas/BigBag</option>
      <option value="Sacarias 50kg">Sacarias 50kg</option>
    </select>

    <button type="submit" name="adicionar">Adicionar</button>
  </form>

  <?php if (isset($editar)) { ?>
    <h2 class="titulo">Editar Produto</h2>
    <form method="post" class="editar-form">
      <input type="hidden" name="id" value="<?php echo $editar['id']; ?>">
      <label>Nome:</label>
      <input type="text" name="nome" value="<?php echo $editar['nome_produto']; ?>" required>
      <label>Quantidade:</label>
      <input type="number" name="quantidade" value="<?php echo $editar['quantidade']; ?>" step="0.01" required>
      <button type="submit" name="atualizar">Atualizar</button>
    </form>
  <?php } ?>

  <h2 class="titulo">Depósito Atual</h2>
  <table class="tabela">
    <thead>
      <tr>
        <th>Nome</th>
        <th>Quantidade</th>
        <th>Unidade</th>
        <th>Tipo</th>
        <th>Atualizado em</th>
        <th>Usuário</th>
        <th>Ações</th>
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
          <td>
<?php 
$tipo_usuario = $_SESSION['funcao_usuario'] ?? ''; 
if (temPermissao($tipo_usuario)) { ?>
  <a href="?editar=<?php echo $row['id']; ?>">Editar</a> | 
  <a href="?remover=<?php echo $row['id']; ?>" onclick="return confirm('Remover este item?')">Remover</a>
<?php } else { ?>
  <span class="link-desativado">Editar</span> | 
  <span class="link-desativado">Remover</span>
<?php } ?>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<?php include '../base/rodape.php'; ?>
</body>
</html>
