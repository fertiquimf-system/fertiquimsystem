<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

$filtro_ticket   = $_GET['ticket'] ?? '';
$filtro_etiqueta = $_GET['etiqueta'] ?? '';

$sql = "
SELECT
    d.id,
    d.ticket_pesagem,
    d.data_criacao,
    e.numero_etiqueta

FROM documentos d

LEFT JOIN documento_etiquetas de
    ON d.id = de.documento_id

LEFT JOIN etiquetas e
    ON de.etiqueta_id = e.id

WHERE 1=1
";

if (!empty($filtro_ticket)) {
    $sql .= " AND d.ticket_pesagem LIKE '%" . mysqli_real_escape_string($conn, $filtro_ticket) . "%'";
}

if (!empty($filtro_etiqueta)) {
    $sql .= " AND e.numero_etiqueta LIKE '%" . mysqli_real_escape_string($conn, $filtro_etiqueta) . "%'";
}

$sql .= "
ORDER BY d.id DESC
";

$resultado = mysqli_query($conn, $sql);

if (!$resultado) {
    die("Erro SQL: " . mysqli_error($conn));
}

$documentos = [];

while ($row = mysqli_fetch_assoc($resultado)) {

    $id = $row['id'];

    if (!isset($documentos[$id])) {

        $documentos[$id] = [
            'ticket' => $row['ticket_pesagem'],
            'data' => $row['data_criacao'],
            'etiquetas' => []
        ];
    }

    if (!empty($row['numero_etiqueta'])) {
        $documentos[$id]['etiquetas'][] = $row['numero_etiqueta'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>

<meta charset="UTF-8">
<title>Consulta de Documentos</title>

<link rel="stylesheet" href="../css/estilo.css">

<style>

body{
    background:#f4f6f9;
}

.container{
    max-width:1400px;
    margin:auto;
    padding:20px;
}

h2{
    color:#1f2937;
    margin-bottom:20px;
    font-size:28px;
    font-weight:600;
}

.filtros{
    background:#fff;
    padding:20px;
    border-radius:15px;
    margin-bottom:25px;
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    box-shadow:0 4px 15px rgba(0,0,0,.08);
}

.filtros input{
    padding:12px;
    width:250px;
    border:1px solid #dcdcdc;
    border-radius:8px;
    font-size:14px;
}

.filtros input:focus{
    outline:none;
    border-color:#0d6efd;
}

.filtros button{
    background:#0d6efd;
    color:#fff;
    border:none;
    padding:12px 20px;
    border-radius:8px;
    cursor:pointer;
    font-weight:bold;
    transition:.3s;
}

.filtros button:hover{
    background:#0b5ed7;
}

.tabela{
    width:100%;
    border-collapse:separate;
    border-spacing:0;
    background:#fff;
    border-radius:15px;
    overflow:hidden;
    box-shadow:0 4px 15px rgba(0,0,0,.08);
}

.tabela thead{
    background:#1f2937;
}

.tabela th{
    color:white;
    padding:15px;
    text-transform:uppercase;
    font-size:13px;
    letter-spacing:1px;
}

.tabela td{
    padding:15px;
    border-bottom:1px solid #ececec;
    text-align:center;
}

.tabela tbody tr{
    transition:.2s;
}

.tabela tbody tr:hover{
    background:#f8fafc;
}

.btn-expandir{
    width:35px;
    height:35px;
    border:none;
    border-radius:50%;
    background:#0d6efd;
    color:white;
    cursor:pointer;
    font-size:18px;
    font-weight:bold;
    transition:.3s;
}

.btn-expandir:hover{
    transform:scale(1.1);
    background:#0b5ed7;
}

.ticket-badge{
    background:#0d6efd;
    color:white;
    padding:8px 15px;
    border-radius:20px;
    font-weight:bold;
}

.qtd-badge{
    background:#ffc107;
    color:#000;
    padding:8px 15px;
    border-radius:20px;
    font-weight:bold;
}

.data-badge{
    background:#6c757d;
    color:white;
    padding:8px 15px;
    border-radius:20px;
}

.linha-detalhes{
    background:#f8fafc;
}

.area-etiquetas{
    padding:10px;
}

.etiqueta{
    display:inline-block;
    padding:8px 15px;
    margin:5px;
    background:#198754;
    color:white;
    border-radius:20px;
    font-weight:bold;
    box-shadow:0 2px 5px rgba(0,0,0,.15);
}

.sem-etiquetas{
    color:#dc3545;
    font-weight:bold;
}

</style>

</head>
<body>

<?php include '../base/cabecalho.php'; ?>

<div class="container">

<h2>Consulta de Tickets e Etiquetas</h2>

<form method="GET" class="filtros">

    <input
        type="text"
        name="ticket"
        placeholder="Pesquisar Ticket"
        value="<?= htmlspecialchars($filtro_ticket) ?>"
    >

    <input
        type="text"
        name="etiqueta"
        placeholder="Pesquisar Etiqueta"
        value="<?= htmlspecialchars($filtro_etiqueta) ?>"
    >

    <button type="submit">
        Pesquisar
    </button>

</form>

<table class="tabela">

<thead>
<tr>
    <th></th>
    <th>Ticket</th>
    <th>Qtd. Etiquetas</th>
    <th>Data</th>
</tr>
</thead>

<tbody>

<?php foreach($documentos as $id => $doc): ?>

<tr>

    <td width="50">
        <button
            class="btn-expandir"
            onclick="toggleLinha(<?= $id ?>)"
            type="button"
        >
            ▼
        </button>
    </td>

<td>
    <span class="ticket-badge">
        <?= htmlspecialchars($doc['ticket']) ?>
    </span>
</td>

<td>
    <span class="qtd-badge">
        <?= count($doc['etiquetas']) ?>
    </span>
</td>

<td>
    <span class="data-badge">
        <?= date('d/m/Y H:i', strtotime($doc['data'])) ?>
    </span>
</td>

</tr>

<tr
    id="linha-<?= $id ?>"
    class="linha-detalhes"
    style="display:none;"
>

<td colspan="4">

<?php if(count($doc['etiquetas']) > 0): ?>

    <?php foreach($doc['etiquetas'] as $etiqueta): ?>

        <span class="etiqueta">
            <?= htmlspecialchars($etiqueta) ?>
        </span>

    <?php endforeach; ?>

<?php else: ?>

    Nenhuma etiqueta vinculada.

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

<script>

function toggleLinha(id){

    let linha = document.getElementById('linha-' + id);

    if(linha.style.display === 'none'){

        linha.style.display = 'table-row';

    }else{

        linha.style.display = 'none';

    }
}

</script>

</body>
</html>