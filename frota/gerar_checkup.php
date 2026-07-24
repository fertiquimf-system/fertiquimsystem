<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

// BUSCA VEÍCULOS
$sql = "SELECT id, placa, modelo, marca, ano FROM frota_veiculos WHERE status = 'Ativo'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Laudo de Vistoria do Veículo</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../css/estilo.css">

<style>
.formulario {
  background: rgba(255,255,255,0.96);
  padding: 20px;
  border-radius: 10px;
}
.vistoria-grid {
  display: grid;
  grid-template-columns: 2fr 1.5fr 1fr;
  gap: 20px;
}
.coluna {
  background: #fff;
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 0 8px rgba(0,0,0,0.08);
}
fieldset {
  border: 1px solid #ccc;
  padding: 12px;
  margin-bottom: 15px;
  border-radius: 6px;
}
legend {
  font-weight: bold;
  color: #2f6f1e;
}
label {
  display: block;
  font-size: 14px;
  margin-top: 8px;
}
input, select, textarea {
  width: 100%;
  padding: 6px;
  margin-top: 4px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
input[readonly] {
  background: #f3f3f3;
}
.linha-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 6px;
}
.linha-item label {
  width: 60%;
}
.linha-item select {
  width: 38%;
}
.btn-salvar {
  width: 100%;
  padding: 15px;
  background: #8cc63e;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
}
.btn-salvar:hover {
  background: #78b531;
}
@media (max-width: 1100px) {
  .vistoria-grid {
    grid-template-columns: 1fr;
  }
}
</style>
</head>

<body>

<?php include '../base/cabecalho.php'; ?>

<div class="container">
<h2 class="titulo">Laudo de Vistoria do Veículo</h2>

<form action="salvar_vistoria.php" method="POST" class="formulario">

<div class="vistoria-grid">

<!-- COLUNA 1 -->
<div class="coluna">
<fieldset>
<legend>Dados do Veículo</legend>

<label>Selecionar Veículo</label>
<select name="veiculo_id" id="veiculo_id" required>
  <option value="">Selecione...</option>
  <?php while($v = $result->fetch_assoc()) { ?>
    <option value="<?= $v['id'] ?>">
      <?= $v['placa'] ?> - <?= $v['modelo'] ?>
    </option>
  <?php } ?>
</select>

<label>Placa</label>
<input type="text" id="placa" name="placa" readonly>

<label>Modelo</label>
<input type="text" id="modelo" name="modelo" readonly>

<label>Marca</label>
<input type="text" id="marca" name="marca" readonly>

<label>Ano</label>
<input type="number" id="ano" name="ano" readonly>

<label>KM Atual</label>
<input type="number" name="km_atual" required>
</fieldset>

<fieldset>
<legend>Motorista</legend>

<label>Nome do Motorista</label>
<input type="text" name="motorista" required>

<label>CNH</label>
<input type="text" name="cnh">

<label>Validade CNH</label>
<input type="date" name="data_validade_cnh">
</fieldset>
</div>

<!-- COLUNA 2 -->
<div class="coluna">
<fieldset>
<legend>Itens Verificados</legend>

<?php
$itens = [
'luz_alta'=>'Luz Alta','luz_baixa'=>'Luz Baixa','setas_dianteiras'=>'Setas Dianteiras',
'setas_traseiras'=>'Setas Traseiras','luz_re'=>'Luz de Ré','luz_freio'=>'Luz de Freio',
'meia_luz'=>'Meia Luz','limpador_vidros'=>'Limpador de Vidros','pneus'=>'Pneus',
'estepe'=>'Estepe','cintos_seguranca'=>'Cintos de Segurança','lataria_geral'=>'Lataria em Geral',
'limpeza_veiculo'=>'Limpeza do Veículo','para_brisa'=>'Para-brisa','oleo_motor'=>'Óleo do Motor',
'agua_radiador'=>'Água do Radiador','vidros_travas'=>'Vidros e Travas','barulhos_anormais'=>'Barulhos Anormais'
];

foreach ($itens as $name=>$label) {
echo "
<div class='linha-item'>
<label>$label</label>
<select name='$name' required>
<option value='BOM'>Bom</option>
<option value='RUIM'>Ruim</option>
<option value='NAO_POSSUI'>Não Possui</option>
</select>
</div>";
}
?>
</fieldset>
</div>

<!-- COLUNA 3 -->
<div class="coluna">
<fieldset>
<legend>Observações</legend>
<textarea name="observacoes" rows="8"></textarea>
</fieldset>

<input type="hidden" name="responsavel_vistoria" value="<?= $_SESSION['nome_usuario']; ?>">

<button type="submit" class="btn-salvar">Salvar Vistoria</button>
</div>

</div>
</form>
</div>

<?php include '../base/rodape.php'; ?>

<script>
document.getElementById('veiculo_id').addEventListener('change', function() {
  const id = this.value;
  if (!id) return;

  fetch('buscar_veiculo.php?id=' + id)
    .then(res => res.json())
    .then(d => {
      document.getElementById('placa').value = d.placa;
      document.getElementById('modelo').value = d.modelo;
      document.getElementById('marca').value = d.marca;
      document.getElementById('ano').value = d.ano;
    });
});
</script>

</body>
</html>
