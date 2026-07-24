<?php
session_start();
require_once '../conexaohost/conexao.php'; 

// Ajuste da conexão
if (!isset($conn) && !isset($conexao)) {
    die("Erro: conexão com o banco não encontrada. Verifique o arquivo de conexão.");
}
if (isset($conn) && !isset($conexao)) {
    $conexao = $conn;
}

// Proteção de acesso
if (!isset($_SESSION['funcao_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

// Validação de período
if (!isset($_GET['data_inicio']) || !isset($_GET['data_fim'])) {
    die("Erro: Intervalo de datas não informado.");
}

$data_inicio = $_GET['data_inicio'];
$data_fim = $_GET['data_fim'];
$tipo_despesa = isset($_GET['tipo_despesa']) ? $_GET['tipo_despesa'] : "";

// SQL base
$sql = "SELECT * FROM entradas WHERE data BETWEEN ? AND ?";

if ($tipo_despesa !== "") {
    $sql .= " AND tipo = ?";
}

$sql .= " ORDER BY data ASC";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    die("Erro ao preparar SQL: " . $conexao->error . "<br>SQL: " . $sql);
}

if ($tipo_despesa !== "") {
    $stmt->bind_param("sss", $data_inicio, $data_fim, $tipo_despesa);
} else {
    $stmt->bind_param("ss", $data_inicio, $data_fim);
}

$stmt->execute();
$result = $stmt->get_result();

// Soma total
$total = 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Relatório de Despesas</title>

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #eef2f3;
    margin: 0;
    padding: 0;
}
.container {
    width: 95%;
    max-width: 1100px;
    margin: 40px auto;
    background: #fff;
    border-radius: 14px;
    padding: 30px 40px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.15);
}

/* Cabeçalho */
header {
    text-align: center;
    border-bottom: 3px solid #225B0B;
    padding-bottom: 10px;
    margin-bottom: 25px;
}
header h1 {
    color: #225B0B;
    margin: 0;
}
header p {
    margin: 4px 0;
    color: #777;
    font-size: 15px;
}

/* Tabela */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 15px;
}
th, td {
    padding: 10px 8px;
    border: 1px solid #ccc;
    text-align: center;
}
th {
    background-color: #225B0B;
    color: white;
    font-size: 15px;
}
tbody tr:hover {
    background: #f1f7f1;
    transition: .2s;
}
tfoot td {
    font-weight: bold;
    background: #f7fff7;
}

/* Botões */
.botoes {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 25px;
}
button, a.voltar {
    background-color: #225B0B;
    color: white;
    padding: 12px 22px;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    text-decoration: none;
    transition: 0.2s;
    border: none;
}
button:hover, a.voltar:hover {
    background-color: #2f7d2f;
}

/* Impressão */
@media print {
    .botoes, #menu-admin, footer {
        display: none !important;
    }
    body {
        background: white;
    }
    .container {
        box-shadow: none;
        width: 100%;
        padding: 0;
        margin: 0;
    }
}
</style>
</head>

<body>

<?php include '../base/administrador.php'; ?>

<div class="container">

    <header>
        <h1>FERTIQUIM SYSTEM</h1>
        <p><strong>Relatório de Despesas</strong></p>
        <p>Período: <?= date("d/m/Y", strtotime($data_inicio)); ?> à <?= date("d/m/Y", strtotime($data_fim)); ?></p>

        <?php if ($tipo_despesa !== ""): ?>
            <p>Tipo selecionado: <strong><?= htmlspecialchars($tipo_despesa); ?></strong></p>
        <?php else: ?>
            <p>Tipo selecionado: <strong>Todos</strong></p>
        <?php endif; ?>

        <p>Emitido em: <?= date("d/m/Y H:i"); ?></p>
    </header>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Valor (R$)</th>
                <th>Tipo</th>
                <th>Descrição</th>
                <th>Data</th>
                <th>Criado em</th>
            </tr>
        </thead>

        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): 
                $total += $row['valor'];
            ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['nome']); ?></td>
                    <td><?= number_format($row['valor'], 2, ',', '.'); ?></td>
                    <td><?= htmlspecialchars($row['tipo']); ?></td>
                    <td><?= htmlspecialchars($row['descricao']); ?></td>
                    <td><?= date("d/m/Y", strtotime($row['data'])); ?></td>
                    <td><?= date("d/m/Y H:i", strtotime($row['criado_em'])); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">Nenhuma despesa encontrada neste período.</td></tr>
        <?php endif; ?>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="2">TOTAL</td>
                <td colspan="5">R$ <?= number_format($total, 2, ',', '.'); ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="botoes">
        <button onclick="window.print()">🖨️ Imprimir Relatório</button>
        <a href="despesas.php" class="voltar">⬅️ Voltar</a>
    </div>
</div>

<?php include '../base/rodape.php'; ?>

</body>
</html>
