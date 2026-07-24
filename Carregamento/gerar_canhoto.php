<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

$tipo = $_GET['tipo'] ?? '';
$id   = $_GET['id'] ?? 0;

if ($tipo === 'entrada') {
    $sql = "SELECT id, marca, placa, motorista, cpf_motorista, telefone, peso_entrada, data_entrada AS data_registro 
            FROM balanca_entrada WHERE id = ?";
} else {
    $sql = "SELECT id, placa, produto, destino, peso_saida, data_saida AS data_registro 
            FROM balanca_saida WHERE id = ?";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$dados = $result->fetch_assoc();

// Default
$pesoEntrada = 0;
$pesoSaida   = 0;
$marca       = "-";
$motorista   = "-";
$cpf_motorista = "-";
$produto     = $dados['produto'] ?? "-";
$destino     = $dados['destino'] ?? "-";
$telefone     = $dados['telefone'] ?? "-";

// Se for entrada
if ($tipo === 'entrada') {
    $pesoEntrada   = $dados['peso_entrada'] ?? 0;
    $marca         = $dados['marca'] ?? "-";
    $motorista     = $dados['motorista'] ?? "-";
    $cpf_motorista = $dados['cpf_motorista'] ?? "-";
    $telefone = $dados['telefone'] ?? "-";
} 
// Se for saída, buscar dados da entrada correspondente
else {
    $pesoSaida = $dados['peso_saida'] ?? 0;

    $sqlEntrada = "SELECT peso_entrada, marca, motorista, cpf_motorista, telefone 
                   FROM balanca_entrada 
                   WHERE placa = ? 
                   ORDER BY data_entrada DESC LIMIT 1";
    $stmt2 = $conn->prepare($sqlEntrada);
    $stmt2->bind_param("s", $dados['placa']);
    $stmt2->execute();
    $res2 = $stmt2->get_result()->fetch_assoc();

    $pesoEntrada   = $res2['peso_entrada'] ?? 0;
    $marca         = $res2['marca'] ?? "-";
    $motorista     = $res2['motorista'] ?? "-";
    $cpf_motorista = $res2['cpf_motorista'] ?? "-";
      $telefone = $res2['telefone'] ?? "-";
}

$pesoSaida = $dados['peso_saida'] ?? $pesoSaida;
$tara = ($pesoEntrada && $pesoSaida) ? abs($pesoEntrada - $pesoSaida) : 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Canhoto</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .ticket { border: 1px solid #000; padding: 15px; margin-bottom: 20px; }
    .header { display: flex; justify-content: space-between; align-items: center; }
    .empresa { font-size: 14px; line-height: 1.4; }
    h2 { text-align: center; margin: 10px 0; }
    .info p { margin: 5px 0; }
    .linha { border-top: 2px dashed #000; margin: 20px 0; }
    .assinaturas { margin-top: 30px; display: flex; justify-content: space-between; }
    .assinatura { text-align: center; width: 45%; }
    .assinatura span { display: block; margin-top: 50px; border-top: 1px solid #000; }
  </style>
</head>
<body onload="window.print()">

<?php for ($i=0; $i<2; $i++): ?>
<div class="ticket">
  <div class="header">
    <div class="empresa">
      <b>FERTIQUIM FERTILIZANTES GOIAS LTDA</b><br>
      CNPJ: 59.125.844/0001-01<br>
      IE: 91162002-25<br>
      Endereço: Rua Arthur Costa e Silva, 86 - Centro<br>
      Engenheiro Beltrão - PR - CEP 87270-000<br>
      Fone: (64) 99644-6680<br>
      E-mail: fertiquimf@gmail.com
    </div>
    <div>
      <img src="../img/logo.jpg" alt="Logo" style="height:80px;">
    </div>
  </div>

  <h2>Ticket de <?= ucfirst($tipo) ?></h2>

  <div class="info">
    <p><b>Nº Ticket:</b> <?= $dados['id'] ?></p>
    <p><b>Data/Hora:</b> <?= date("d/m/Y H:i:s", strtotime($dados['data_registro'])) ?></p>
    <p><b>Marca:</b> <?= $marca ?></p>
    <p><b>Placa:</b> <?= $dados['placa'] ?></p>
    <p><b>Motorista:</b> <?= $motorista ?></p>
    <p><b>CPF Motorista:</b> <?= $cpf_motorista ?></p>
    <p><b>Telefone:</b> <?= $telefone ?></p>
    <?php if ($tipo === 'saida'): ?>
      <p><b>Produto:</b> <?= $produto ?></p>
      <p><b>Destino:</b> <?= $destino ?></p>
    <?php endif; ?>
    <p><b>Peso Entrada:</b> <?= number_format($pesoEntrada, 0, ',', '.') ?> kg</p>
    <p><b>Peso Saída:</b> <?= number_format($pesoSaida, 0, ',', '.') ?> kg</p>
    <p><b>Tara:</b> <?= number_format($tara, 0, ',', '.') ?> kg</p>
  </div>

  <div class="assinaturas">
    <div class="assinatura">
      <span><?= $motorista ?></span>
      Motorista
    </div>
    <div class="assinatura">
      <span>________________________</span>
      Responsável
    </div>
  </div>
</div>

<?php if ($i == 0): ?>
<div class="linha"></div>
<?php endif; ?>
<?php endfor; ?>

</body>
</html>
