<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'registrar') {
    $marca = $_POST['marca'];
    $placa = $_POST['placa'];
    $motorista = $_POST['motorista'];
    $cpf_motorista = $_POST['cpf_motorista'];
    $telefone = $_POST['telefone'];   // ✅ corrigido
    $peso_entrada = $_POST['peso_entrada'];
    $data_entrada = date('Y-m-d H:i:s');
    
    $sql = "INSERT INTO balanca_entrada (marca, placa, motorista, cpf_motorista, peso_entrada, data_entrada, telefone) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiss", $marca, $placa, $motorista, $cpf_motorista, $peso_entrada, $data_entrada, $telefone); // ✅ corrigido
    $stmt->execute();

    header("Location: entrada_balanca.php?success=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8"/>
  <title>Entrada Balança</title>
  <link rel="stylesheet" href="../css/estilo.css"/>
  <style>
    .layout { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px; }
    .form-box { background: #1a1a1a; padding: 20px; border-radius: 12px; color: #fff; }
    .form-box h2 { color: #4CAF50; margin-bottom: 15px; }
    .form-box input { width: 100%; padding: 10px; margin: 8px 0; border-radius: 8px; border: none; }
    .btn { padding: 12px; border: none; border-radius: 8px; font-size: 15px; cursor: pointer; width: 100%; margin-top: 10px; }
    .btn-green { background: #4CAF50; color: #fff; }
    .camera-box { background: #000; border-radius: 12px; padding: 10px; text-align: center; }
    .camera-box h2 { color: #4CAF50; margin-bottom: 10px; }
    img { width: 100%; height: 400px; border-radius: 12px; object-fit: cover; }
  </style>
</head>
<body>
<?php include '../base/cabecalho.php'; ?>

<div class="layout">
    <!-- Formulário de entrada -->
    <div class="form-box">
        <h2>Registrar Entrada</h2>
        <form method="post">
            <input type="hidden" name="acao" value="registrar">
            
            <label>Marca do Caminhão:</label>
            <input type="text" name="marca" placeholder="Ex: Scania" required>

            <label>Placa:</label>
            <input type="text" name="placa" placeholder="ABC-1234" required>

            <label>Motorista:</label>
            <input type="text" name="motorista" placeholder="Nome do motorista" required>

            <label>CPF do Motorista:</label>
            <input type="text" name="cpf_motorista" placeholder="000.000.000-00" required>

            <label>Telefone:</label>
            <input type="text" name="telefone" placeholder="(00)00000-0000" required>

            <label>Peso Entrada (kg):</label>
            <input type="number" name="peso_entrada" placeholder="00000" required>

            <button type="submit" class="btn btn-green">🚚 Registrar Entrada</button>
        </form>
    </div>

    <!-- Visualização da câmera -->
    <div class="camera-box">
        <h2>Visualização da Balança</h2>
        <img id="camera" src="camera.php" alt="Câmera não disponível">
    </div>
</div>

<script>
// Atualiza a imagem da câmera a cada 1s
setInterval(() => {
  const cam = document.getElementById("camera");
  cam.src = "camera.php?" + new Date().getTime();
}, 1000);
</script>
  <?php 
  include '../base/rodape.php';
  ?>
</body>
</html>
