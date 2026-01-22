<?php
require_once '../conexaohost/conexao.php';
session_start();

// 🔧 Força o fuso horário para evitar data/hora errada
date_default_timezone_set('America/Sao_Paulo');

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'registrar') {
    $placa = $_POST['placa'];
    $produto = $_POST['produto'];
    $peso_saida = $_POST['peso_saida'];
    $destino = $_POST['destino'];

    // ✅ Agora com campo destino
    $sql = "INSERT INTO balanca_saida (placa, produto, peso_saida, destino, data_saida) 
            VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $placa, $produto, $peso_saida, $destino);
    $stmt->execute();

    header("Location: saida_balanca.php?success=1");
    exit;
}

// Buscar placas já registradas na entrada
$placas = $conn->query("
    SELECT DISTINCT placa 
    FROM balanca_entrada 
    WHERE DATE(data_entrada) = CURDATE()
");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8"/>
  <title>Saída Balança</title>
  <link rel="stylesheet" href="../css/estilo.css"/>
  <style>
    .layout { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px; }
    .form-box { background: #1a1a1a; padding: 20px; border-radius: 12px; color: #fff; }
    .form-box h2 { color: #f44336; margin-bottom: 15px; }
    .form-box input, .form-box select { width: 100%; padding: 10px; margin: 8px 0; border-radius: 8px; border: none; }
    .btn { padding: 12px; border: none; border-radius: 8px; font-size: 15px; cursor: pointer; width: 100%; margin-top: 10px; }
    .btn-red { background: #f44336; color: #fff; }
    .btn-purple { background: #6a1b9a; color: #fff; }
    .camera-box { background: #000; border-radius: 12px; padding: 10px; }
    .camera-box h2 { color: #f44336; margin-bottom: 10px; }
    img { width: 100%; height: 400px; border-radius: 12px; object-fit: cover; }
  </style>
</head>
<body>
<?php include '../base/cabecalho.php'; ?>

<div class="layout">
    <!-- Formulário de saída -->
    <div class="form-box">
        <h2>Registrar Saída</h2>
        <form method="post">
            <input type="hidden" name="acao" value="registrar">
            
            <label>Selecionar Placa:</label>
            <select name="placa" required>
                <option value="">-- Selecione --</option>
                <?php while($row = $placas->fetch_assoc()): ?>
                    <option value="<?= $row['placa'] ?>"><?= $row['placa'] ?></option>
                <?php endwhile; ?>
            </select>

            <label>Produto:</label>
            <input type="text" name="produto" placeholder="Digite o produto" required>

            <label>Peso Saída (kg):</label>
            <input type="number" name="peso_saida" placeholder="00000" required>

            <label>Destino do Caminhão:</label>
            <input type="text" name="destino" placeholder="Digite o destino" required>

            <button type="submit" class="btn btn-red">🚛 Registrar Saída</button>
        </form>

        <!-- Botão para ir até a página de canhotos -->
        <button onclick="window.location.href='canhotos_salvos.php'" class="btn btn-purple">🧾 Ver Canhotos</button>
    </div>

    <!-- Visualização da câmera -->
    <div class="camera-box">
        <h2>Visualização da Balança</h2>
        <img id="camera" src="camera.php" alt="Câmera não disponível">
    </div>
</div>

<script>
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
