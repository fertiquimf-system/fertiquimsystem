<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

// Busca veículos ativos
$sql = "SELECT id, placa, modelo FROM frota_veiculos WHERE status = 'Ativo'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Consulta de Vistorias</title>
<link rel="stylesheet" href="../css/estilo.css">

<style>
.container-box {
  background: #fff;
  padding: 20px;
  border-radius: 10px;
}

/* BLOCO DO SELECT */
.filtro-veiculo {
  display: flex;
  align-items: center;
  gap: 10px;
  max-width: 500px;
}

.filtro-veiculo label {
  font-weight: bold;
  white-space: nowrap;
}

.filtro-veiculo select {
  flex: 1;
  padding: 6px;
}

/* TABELA */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

th, td {
  border: 1px solid #ccc;
  padding: 10px;
  text-align: center;
}

th {
  background: #2f6f1e;
  color: #fff;
}
</style>
</head>

<body>

<?php include '../base/cabecalho.php'; ?>

<div class="container">
  <h2 class="titulo">Consulta de Vistorias por Veículo</h2>

  <div class="container-box">

    <!-- FILTRO -->
    <div class="filtro-veiculo">
      <label for="veiculo_id">Selecionar Veículo:</label>
      <select id="veiculo_id">
        <option value="">Selecione...</option>
        <?php while($v = $result->fetch_assoc()) { ?>
          <option value="<?= $v['id'] ?>">
            <?= $v['placa'] ?> - <?= $v['modelo'] ?>
          </option>
        <?php } ?>
      </select>
    </div>

    <!-- RESULTADO -->
    <div id="resultado"></div>

  </div>
</div>

<?php include '../base/rodape.php'; ?>

<script>
document.getElementById('veiculo_id').addEventListener('change', function() {
    const id = this.value;

    if (!id) {
        document.getElementById('resultado').innerHTML = '';
        return;
    }

    fetch('buscar_vistorias.php?veiculo_id=' + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById('resultado').innerHTML = html;
        });
});
</script>

</body>
</html>
