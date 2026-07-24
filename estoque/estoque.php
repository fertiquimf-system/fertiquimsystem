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

    $check = $conn->query("SELECT id, quantidade FROM estoque_fertilizantes 
                           WHERE nome_produto = '$nome' AND unidade = '$unidade'");

    if ($check && $check->num_rows > 0) {
        $row = $check->fetch_assoc();
        $nova_qtd = $row['quantidade'] + $quantidade;

        $conn->query("UPDATE estoque_fertilizantes 
                      SET quantidade = $nova_qtd, 
                          data_atualizacao = NOW(), 
                          usuario = '$usuario' 
                      WHERE id = {$row['id']}");
    } else {
        $conn->query("INSERT INTO estoque_fertilizantes 
                      (nome_produto, quantidade, unidade, tipo, data_atualizacao, usuario) 
                      VALUES ('$nome', $quantidade, '$unidade', '$tipo', NOW(), '$usuario')");
    }

    header("Location: estoque.php");
    exit;
}

  function temPermissao($tipo_usuario) {
    $tipo = strtolower(trim($tipo_usuario));
    return ($tipo === 'gerencia' || $tipo === 'administrador');
}


// Remover produto (apenas administrador)
if (isset($_GET['remover'])) {
    $tipo_usuario = $_SESSION['funcao_usuario'] ?? '';
    if (!temPermissao($tipo_usuario)) {
        echo "<script>
                alert('Você não tem permissão para remover este item. Solicite um chamado para TI_Fertiquim@gmail.com.');
                window.location.href='estoque.php';
              </script>";
        exit;
    }

    $id = intval($_GET['remover']);
    $conn->query("DELETE FROM estoque_fertilizantes WHERE id = $id");
    header("Location: estoque.php");
    exit;
}

// Editar produto (carregar dados)
if (isset($_GET['editar'])) {
    $tipo_usuario = $_SESSION['funcao_usuario'] ?? '';
    if (!temPermissao($tipo_usuario)){
        echo "<script>
                alert('Você não tem permissão para editar este item. Solicite um chamado para TI_Fertiquim@gmail.com.');
                window.location.href='estoque.php';
              </script>";
        exit;
    }

    $id = intval($_GET['editar']);
    $editar = $conn->query("SELECT * FROM estoque_fertilizantes WHERE id = $id")->fetch_assoc();
}

// Atualizar produto (nome e quantidade)
if (isset($_POST['atualizar'])) {
    $tipo_usuario = $_SESSION['funcao_usuario'] ?? '';
    if (!temPermissao($tipo_usuario)){
        echo "<script>
                alert('Você não tem permissão para atualizar este item. Solicite um chamado para TI_Fertiquim@gmail.com.');
                window.location.href='estoque.php';
              </script>";
        exit;
    }

    $id = intval($_POST['id']);
    $nome = $_POST['nome'];
    $quantidade = floatval($_POST['quantidade']);
    $usuario = $_SESSION['nome_usuario'] ?? 'Desconhecido';

    $conn->query("UPDATE estoque_fertilizantes 
                  SET nome_produto = '$nome',
                      quantidade = $quantidade, 
                      data_atualizacao = NOW(), 
                      usuario = '$usuario' 
                  WHERE id = $id");

    header("Location: estoque.php");
    exit;
}

$result = $conn->query("SELECT * FROM estoque_fertilizantes ORDER BY data_atualizacao DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Estoque - Fertiquim</title>
  <link rel="stylesheet" href="../css/estilo.css">
  <style>
    
    .editar-form {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .editar-form label {
      font-size: 16px;
      font-weight: bold;
    }

    .editar-form input {
      padding: 5px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    .editar-form .input-pequeno {
      width: 100px;
      text-align: right;
    }

    .editar-form button {
      padding: 6px 12px;
      background: #8bc34a;
      border: none;
      border-radius: 4px;
      color: white;
      cursor: pointer;
    }

    .editar-form button:hover {
      background: #689f38;
    }

    .link-desativado {
      color: #888;
      cursor: not-allowed;
      text-decoration: none;
    }
  </style>
</head>
<body>
<?php include '../base/estoque.php'; ?>

<div class="container">
  <h2 class="titulo">Adicionar Material Manualmente</h2>
  <form method="post" class="formulario">
    <input type="text" name="nome" placeholder="Nome do Material" required>
    <input type="number" name="quantidade" placeholder="Quantidade" required step="0.01">
    <input type="text" name="unidade" placeholder="Unidade (ex: kg, L)" required>
    
    <select name="tipo" required>
      <option value="">Selecione</option>
      <option value="Material de informática">Material de informática</option>
      <option value="Ferramentas">Ferramentas</option>
      <option value="Matéria-prima">Matéria-prima</option>
      <option value="Material de Escritorio">Material de Escritório</option>
      <option value="EPI">EPi's</option>
      <option value="Saca 50kg">Sacaria</option>
      <option value="Materiais Elétricos">Materiais elétricos</option>
      <option value="Materiais de Consumo">Materiais de Consumo</option>
      <option value="BigBag">BigBag</option>
    </select>

    <button type="submit" name="adicionar">Adicionar</button>
  </form>

  <?php if (isset($editar)) { ?>
    <h2 class="titulo">Editar Produto</h2>
    <form method="post" class="editar-form">
      <input type="hidden" name="id" value="<?php echo $editar['id']; ?>">

      <label>Nome:</label>
      <input type="text" name="nome" 
             value="<?php echo $editar['nome_produto']; ?>" required>

      <label>Quantidade:</label>
      <input type="number" name="quantidade" 
             value="<?php echo $editar['quantidade']; ?>" 
             step="0.01" required class="input-pequeno">

      <button type="submit" name="atualizar">Atualizar</button>
    </form>
  <?php } ?>

  <h2 class="titulo">Estoque Atual</h2>
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
  if (temPermissao($tipo_usuario)){ ?>
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
