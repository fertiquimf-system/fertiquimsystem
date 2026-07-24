<?php
session_start();
require_once '../conexaohost/conexao.php';

if (!isset($_SESSION['funcao_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

// Buscar tipos de despesas distintos
$tipos = [];
$sql = $conn->query("SELECT DISTINCT tipo FROM entradas ORDER BY tipo ASC");
while ($row = $sql->fetch_assoc()) {
    $tipos[] = $row['tipo'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Relatório de Despesas</title>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background: #f2f2f2;
}

/* Container principal */
.container {
    width: 95%;
    max-width: 900px;
    margin: 60px auto;
    background: #fff;
    border-radius: 14px;
    padding: 40px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.15);
}

/* Título */
h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #8b2e2e;
    font-size: 28px;
}

/* GRID DO FORM */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

/* Label */
label {
    font-weight: bold;
    color: #225B0B;
}

/* Inputs */
input, select {
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    width: 100%;
    font-size: 15px;
    background: #fafafa;
}

/* Botão */
button {
    margin-top: 25px;
    width: 100%;
    padding: 15px;
    background: #8b2e2e;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 18px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #5e1f1f;
}
</style>
</head>

<body>

<?php include '../base/administrador.php'; ?>

<div class="container">

    <h2>Gerar Relatório de Despesas</h2>

    <form method="GET" action="gerar_relatorio_despesa.php">

        <div class="form-grid">

            <div>
                <label for="data_inicio">Data Início:</label>
                <input type="date" name="data_inicio" required>
            </div>

            <div>
                <label for="data_fim">Data Fim:</label>
                <input type="date" name="data_fim" required>
            </div>

            <div>
                <label for="tipo_despesa">Tipo de Despesa:</label>
                <select name="tipo_despesa">
                    <option value="">-- Todos --</option>
                    <?php foreach ($tipos as $t): ?>
                        <option value="<?= $t ?>"><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>

        <button type="submit">Gerar Relatório</button>

    </form>

</div>

<?php include '../base/rodape.php'; ?>

</body>
</html>
