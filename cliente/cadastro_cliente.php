<?php
session_start();

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastro de Cliente</title>
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
  <form action="salvar_cliente.php" method="post">
    <div class="form-section">
      <h2>Cadastro de Cliente</h2>
      <div class="form-row">
        <div class="form-group">
          <label>Tipo de Pessoa:</label>
          <select name="tipo_pessoa" required>
            <option value="">Selecione</option>
            <option value="FISICA">Pessoa Física</option>
            <option value="JURIDICA">Pessoa Jurídica</option>
          </select>
        </div>
        <div class="form-group">
          <label>Nome/Razão Social:</label>
          <input type="text" name="nome_razao" required>
        </div>
        <div class="form-group">
          <label>CPF/CNPJ:</label>
          <input type="text" name="cpf_cnpj" required>
        </div>
        <div class="form-group">
          <label>RG/IE:</label>
          <input type="text" name="rg_ie">
        </div>
        <div class="form-group">
          <label>Data Nascimento:</label>
          <input type="date" name="data_nascimento">
        </div>
      </div>
    </div>

    <div class="form-section">
      <h2>Contato</h2>
      <div class="form-row">
        <div class="form-group">
          <label>Celular:</label>
          <input type="text" name="telefone" required>
        </div>
        <div class="form-group">
          <label>Telefone:</label>
          <input type="text" name="celular">
        </div>
        <div class="form-group">
          <label>Email:</label>
          <input type="email" name="email">
        </div>
      </div>
    </div>

    <div class="form-section">
      <h2>Endereço</h2>
      <div class="form-row">
        <div class="form-group">
          <label>Endereço:</label>
          <input type="text" name="endereco" required>
        </div>
        <div class="form-group">
          <label>Número:</label>
          <input type="text" name="numero">
        </div>
        <div class="form-group">
          <label>Complemento:</label>
          <input type="text" name="complemento">
        </div>
        <div class="form-group">
          <label>Bairro:</label>
          <input type="text" name="bairro" required>
        </div>
        <div class="form-group">
          <label>Cidade:</label>
          <input type="text" name="cidade" required>
        </div>
        <div class="form-group">
          <label>Estado:</label>
          <input type="text" name="estado" maxlength="2" required>
        </div>
        <div class="form-group">
          <label>CEP:</label>
          <input type="text" name="cep" required>
        </div>
      </div>
    </div>

    <div class="form-section">
      <h2>Observações</h2>
      <div class="form-row">
        <div class="form-group" style="flex: 1;">
          <textarea name="observacoes" rows="4"></textarea>
        </div>
      </div>
    </div>

    <input type="submit" value="Salvar Cliente">
  </form>
</div>
<?php include '../base/rodape.php';?>
</body>

</html>
