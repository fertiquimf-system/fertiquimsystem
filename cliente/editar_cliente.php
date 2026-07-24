<?php
session_start();
require_once '../conexaohost/conexao.php';


// Verifica se recebeu o ID
if (!isset($_GET['id'])) {
    die("ID do cliente não informado.");
}

$id = intval($_GET['id']);

// Buscar cliente
$sql = "SELECT * FROM clientes WHERE id = $id LIMIT 1";
$res = $conn->query($sql);

if (!$res || $res->num_rows == 0) {
    die("Cliente não encontrado.");
}

$cliente = $res->fetch_assoc();

// Se atualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_pessoa = $_POST['tipo_pessoa'];
    $nome_razao = $_POST['nome_razao'];
    $cpf_cnpj = $_POST['cpf_cnpj'];
    $rg_ie = $_POST['rg_ie'];
    $data_nascimento = $_POST['data_nascimento'];
    $telefone = $_POST['telefone'];
    $celular = $_POST['celular'];
    $email = $_POST['email'];
    $endereco = $_POST['endereco'];
    $numero = $_POST['numero'];
    $complemento = $_POST['complemento'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $cep = $_POST['cep'];
    $observacoes = $_POST['observacoes'];

    $sql_update = "UPDATE clientes SET 
        tipo_pessoa='$tipo_pessoa',
        nome_razao='$nome_razao',
        cpf_cnpj='$cpf_cnpj',
        rg_ie='$rg_ie',
        data_nascimento='$data_nascimento',
        telefone='$telefone',
        celular='$celular',
        email='$email',
        endereco='$endereco',
        numero='$numero',
        complemento='$complemento',
        bairro='$bairro',
        cidade='$cidade',
        estado='$estado',
        cep='$cep',
        observacoes='$observacoes'
        WHERE id=$id";

    if ($conn->query($sql_update) === TRUE) {
        echo "<script>alert('Cliente atualizado com sucesso!'); window.location.href='consultar_cliente.php';</script>";
        exit;
    } else {
        echo "Erro ao atualizar: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Editar Cliente</title>
  <link rel="stylesheet" href="../css/estilo.css">
  <style>
    .container { max-width: 900px; margin: 0 auto; padding: 20px; }
    .form-section { background: #fff; padding: 20px; margin-bottom: 30px; border-radius: 8px; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
    .form-section h2 { margin-top: 0; color: #4e4e4e; }
    .form-row { display: flex; flex-wrap: wrap; gap: 20px; }
    .form-group { flex: 1; min-width: 200px; }
    .form-group label { display: block; font-weight: bold; margin-bottom: 4px; }
    .form-group input, .form-group select, .form-group textarea {
      width: 100%; padding: 6px 8px; border-radius: 5px;
      border: 1px solid #ccc; font-size: 14px;
    }
    input[type="submit"] {
      background-color: #4CAF50; color: white; padding: 12px 20px;
      border: none; border-radius: 5px; cursor: pointer;
      margin-top: 10px; font-size: 15px;
    }
  </style>
</head>
<body>
<?php include '../base/cabecalho.php'; ?>
<div class="container">
  <form action="" method="post">
    <div class="form-section">
      <h2>Editar Cliente</h2>
      <div class="form-row">
        <div class="form-group">
          <label>Tipo de Pessoa:</label>
          <select name="tipo_pessoa" required>
            <option value="FISICA" <?= $cliente['tipo_pessoa']=="FISICA"?"selected":"" ?>>Pessoa Física</option>
            <option value="JURIDICA" <?= $cliente['tipo_pessoa']=="JURIDICA"?"selected":"" ?>>Pessoa Jurídica</option>
          </select>
        </div>
        <div class="form-group">
          <label>Nome/Razão Social:</label>
          <input type="text" name="nome_razao" value="<?= $cliente['nome_razao'] ?>" required>
        </div>
        <div class="form-group">
          <label>CPF/CNPJ:</label>
          <input type="text" name="cpf_cnpj" value="<?= $cliente['cpf_cnpj'] ?>" required>
        </div>
        <div class="form-group">
          <label>RG/IE:</label>
          <input type="text" name="rg_ie" value="<?= $cliente['rg_ie'] ?>">
        </div>
        <div class="form-group">
          <label>Data Nascimento:</label>
          <input type="date" name="data_nascimento" value="<?= $cliente['data_nascimento'] ?>">
        </div>
      </div>
    </div>

    <div class="form-section">
      <h2>Contato</h2>
      <div class="form-row">
        <div class="form-group">
          <label>Telefone:</label>
          <input type="text" name="telefone" value="<?= $cliente['telefone'] ?>">
        </div>
        <div class="form-group">
          <label>Celular:</label>
          <input type="text" name="celular" value="<?= $cliente['celular'] ?>">
        </div>
        <div class="form-group">
          <label>Email:</label>
          <input type="email" name="email" value="<?= $cliente['email'] ?>">
        </div>
      </div>
    </div>

    <div class="form-section">
      <h2>Endereço</h2>
      <div class="form-row">
        <div class="form-group">
          <label>Endereço:</label>
          <input type="text" name="endereco" value="<?= $cliente['endereco'] ?>">
        </div>
        <div class="form-group">
          <label>Número:</label>
          <input type="text" name="numero" value="<?= $cliente['numero'] ?>">
        </div>
        <div class="form-group">
          <label>Complemento:</label>
          <input type="text" name="complemento" value="<?= $cliente['complemento'] ?>">
        </div>
        <div class="form-group">
          <label>Bairro:</label>
          <input type="text" name="bairro" value="<?= $cliente['bairro'] ?>">
        </div>
        <div class="form-group">
          <label>Cidade:</label>
          <input type="text" name="cidade" value="<?= $cliente['cidade'] ?>">
        </div>
        <div class="form-group">
          <label>Estado:</label>
          <input type="text" name="estado" maxlength="2" value="<?= $cliente['estado'] ?>">
        </div>
        <div class="form-group">
          <label>CEP:</label>
          <input type="text" name="cep" value="<?= $cliente['cep'] ?>">
        </div>
      </div>
    </div>

    <div class="form-section">
      <h2>Observações</h2>
      <div class="form-row">
        <div class="form-group" style="flex: 1;">
          <textarea name="observacoes" rows="4"><?= $cliente['observacoes'] ?></textarea>
        </div>
      </div>
    </div>

    <input type="submit" value="Salvar Alterações">
  </form>
</div>
<?php include '../base/rodape.php';?>
</body>
</html>
    