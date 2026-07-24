<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM vistorias_veiculos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$v = $stmt->get_result()->fetch_assoc();

if (!$v) {
    die("Vistoria não encontrada.");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Laudo de Vistoria Veicular</title>

<style>
@page {
  size: A4;
  margin: 20mm;
}

body {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 12px;
  color: #000;
}

h1 {
  text-align: center;
  border-bottom: 2px solid #000;
  padding-bottom: 6px;
  margin-bottom: 20px;
}

.bloco {
  margin-bottom: 15px;
}

.bloco h3 {
  background: #eee;
  padding: 6px;
  font-size: 13px;
  border: 1px solid #000;
}

table {
  width: 100%;
  border-collapse: collapse;
}

td, th {
  border: 1px solid #000;
  padding: 6px;
}

.status-BOM { font-weight: bold; }
.status-RUIM { font-weight: bold; color: #000; }
.status-NAO_POSSUI { color: #444; }

.assinatura {
  margin-top: 40px;
  text-align: center;
}

@media print {
  button { display: none; }
}
</style>
</head>

<body onload="window.print()">

<button onclick="window.print()">Imprimir</button>

<h1>LAUDO DE VISTORIA VEICULAR</h1>

<div class="bloco">
<h3>DADOS DO VEÍCULO</h3>
<table>
<tr><td>Placa</td><td><?= $v['placa'] ?></td></tr>
<tr><td>Modelo</td><td><?= $v['modelo'] ?></td></tr>
<tr><td>Marca</td><td><?= $v['marca'] ?></td></tr>
<tr><td>Ano</td><td><?= $v['ano'] ?></td></tr>
<tr><td>KM Atual</td><td><?= $v['km_atual'] ?></td></tr>
</table>
</div>

<div class="bloco">
<h3>MOTORISTA</h3>
<table>
<tr><td>Nome</td><td><?= $v['motorista'] ?></td></tr>
<tr><td>CNH</td><td><?= $v['cnh'] ?></td></tr>
<tr>
<td>Validade CNH</td>
<td><?= $v['validade_cnh'] ? date('d/m/Y', strtotime($v['validade_cnh'])) : '-' ?></td>
</tr>
</table>
</div>

<div class="bloco">
<h3>ITENS VERIFICADOS</h3>
<table>
<?php
$itens = [
'luz_alta'=>'Luz Alta','luz_baixa'=>'Luz Baixa','setas_dianteiras'=>'Setas Dianteiras',
'setas_traseiras'=>'Setas Traseiras','luz_re'=>'Luz de Ré','luz_freio'=>'Luz de Freio',
'meia_luz'=>'Meia Luz','limpador_vidros'=>'Limpador de Vidros','pneus'=>'Pneus',
'estepe'=>'Estepe','cintos_seguranca'=>'Cintos de Segurança','lataria_geral'=>'Lataria Geral',
'limpeza_veiculo'=>'Limpeza','para_brisa'=>'Para-brisa','oleo_motor'=>'Óleo do Motor',
'agua_radiador'=>'Água do Radiador','vidros_travas'=>'Vidros e Travas',
'barulhos_anormais'=>'Barulhos Anormais'
];

foreach ($itens as $campo=>$label) {
  echo "<tr>
    <td>$label</td>
    <td class='status-{$v[$campo]}'>{$v[$campo]}</td>
  </tr>";
}
?>
</table>
</div>

<div class="bloco">
<h3>OBSERVAÇÕES</h3>
<p><?= nl2br($v['observacoes']) ?: 'Nenhuma observação.' ?></p>
</div>

<div class="bloco">
<table>
<tr><td>Responsável pela vistoria</td><td><?= $v['responsavel_vistoria'] ?></td></tr>
<tr><td>Data da vistoria</td><td><?= date('d/m/Y H:i', strtotime($v['data_vistoria'])) ?></td></tr>
</table>
</div>

<div class="assinatura">
_________________________________________<br>
Assinatura do responsável
</div>

</body>
</html>
