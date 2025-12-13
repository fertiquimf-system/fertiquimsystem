<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

$mensagem = "";

// Se o formulário for enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $valor = $_POST['valor'];
    $tipo = $_POST['tipo'];
    $descricao = $_POST['descricao'];
    $data = $_POST['data'];

    // Inserindo no banco
    $sql = "INSERT INTO entradas (nome, valor, tipo, descricao, data) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdsss", $nome, $valor, $tipo, $descricao, $data);

    if ($stmt->execute()) {
        $mensagem = "✅ Entrada cadastrada com sucesso!";
    } else {
        $mensagem = "❌ Erro ao cadastrar: " . $conn->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registrar Entrada</title>
    <link rel="stylesheet" href="../css/estilo.css" />
  <style>
    .form-box {
      max-width: 700px;
      margin: 25px auto;
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .form-box h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 20px;
    }
    form label {
      display: block;
      margin-top: 12px;
      font-weight: bold;
      color: #333;
    }
    form input, form select, form textarea {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    form textarea {
      resize: none;
      height: 80px;
    }
    button {
      margin-top: 20px;
      padding: 12px;
      width: 100%;
      background: #27ae60;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }
    button:hover { background: #219150; }
    .mensagem {
      text-align: center;
      font-weight: bold;
      margin-bottom: 15px;
    }
    .mensagem.ok { color: #27ae60; }
    .mensagem.erro { color: #c0392b; }
  </style>
</head>
<body>
  <header>
    <h1>FERTIQUIM Fertilizantes</h1>
    <nav>
      <a href="../pginicial/pginicial.php">Início</a>
      <a href="../financeiro/financeiro.php">Voltar</a>
      <a href="../pglogin/pglogin.php">Sair</a>
    </nav>
  </header>

  <div class="form-box">
    <h2>Registrar Saída</h2>

    <?php if (!empty($mensagem)): ?>
      <p class="mensagem <?php echo (strpos($mensagem, 'sucesso') !== false) ? 'ok' : 'erro'; ?>">
        <?php echo $mensagem; ?>
      </p>
    <?php endif; ?>

    <form method="POST" action="">
      <label for="nome">Nome da Despesa:</label>
      <input type="text" id="nome" name="nome" required>

      <label for="valor">Valor da Despesa (R$):</label>
      <input type="number" step="0.01" id="valor" name="valor" required>

      <label for="tipo">Tipo da Despesa:</label>
      <select id="tipo" name="tipo" required>
        <option value="">-- Selecione --</option>
        <option value="Combustível">Combustível</option>
        <option value="Material de Escritório">Material de Escritório</option>
        <option value="Material Elétrico">Material Elétrico</option>
        <option value="Material de Informática">Material de Informática</option>
        <option value="EPI's">EPI's</option>
        <option value="Uniforme">Uniforme</option>
        <option value="Deslocamento Fluvial">Deslocamento Fluvial</option>
        <option value="Pedágio">Pedágio</option>
        <option value="Alimentação">Alimentação</option>
        <option value="Acessórios">Acessórios</option>
        <option value="Reposição Bancária">Reposição Bancária</option>
        <option value="Estacionamento">Estacionamento</option>
        <option value="Ferramentas">Ferramentas</option>
        <option value="Hospedagem">Hospedagem</option>
        <option value="Material de Copa">Material de Copa</option>
        <option value="VR">VR</option>
        <option value="Diária">Diária</option>
        <option value="Locação">Locação</option>
        <option value="Salário">Salário</option>
        <option value="Material de Construção">Material de Construção</option>
        <option value="Manutenção de Veículo">Manutenção de Veículo</option>
        <option value="Despesas Mensais">Despesas Mensais</option>
        <option value="Despesas Administrativas">Despesas Administrativas</option>

      </select>

      <label for="descricao">Descrição:</label>
      <textarea id="descricao" name="descricao" required></textarea>

      <label for="data">Data:</label>
      <input type="date" id="data" name="data" required>

      <button type="submit">Salvar Entrada</button>
    </form>
  </div>

    <?php 
  include '../base/rodape.php';
  ?>

  <?php if (isset($_SESSION['nome_usuario']) && isset($_SESSION['funcao_usuario'])): ?>
    <div class="usuario-logado">
      <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>
    </div>
  <?php endif; ?>
  
</body>
</html>
