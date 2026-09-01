<?php
require_once '../conexaohost/conexao.php';
session_start();

// Filtrar status
$statusFiltro = isset($_GET['status']) ? $_GET['status'] : '';

// Buscar vendas
$sqlVendas = "SELECT * FROM vendas";

if($statusFiltro == 'pendente' || $statusFiltro == 'aprovada' || $statusFiltro == 'parcial'){
    $sqlVendas .= " WHERE status='".$statusFiltro."'";
}

$sqlVendas .= " ORDER BY data_venda DESC";

$resVendas = $conn->query($sqlVendas);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">

<title>Lista de Vendas</title>

<link rel="stylesheet" href="../css/estilo.css">

<style>

body{
    background:#f5f5f5;
}

.container{
    padding:20px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
    border-radius:8px;
    overflow:hidden;
    box-shadow:0 2px 8px rgba(0,0,0,.10);
}

table th,
table td{
    padding:10px;
    border-bottom:1px solid #ddd;
}

table th{
    background:#1976d2;
    color:#fff;
}

.btn{
    border:none;
    color:#fff;
    padding:6px 12px;
    border-radius:4px;
    cursor:pointer;
    margin:2px;
}

.btn-expand{
    background:#0288d1;
}

.btn-approve{
    background:#2e7d32;
}

.btn-canhoto{
    background:#ef6c00;
}

.btn-remove{
    background:#c62828;
}

.btn:hover{
    opacity:.85;
}

.btn[disabled]{
    background:#999;
    cursor:not-allowed;
}

.status{
    color:#fff;
    padding:4px 8px;
    border-radius:4px;
    font-weight:bold;
}

.status-pendente{
    background:#f9a825;
}

.status-aprovada{
    background:#2e7d32;
}

.status-parcial{
    background:#1565c0;
}

.item-row{
    display:none;
    background:#fafafa;
}

.box{
    border:1px solid #ddd;
    border-radius:8px;
    margin:15px 0;
    overflow:hidden;
}

.box-title{
    background:#1976d2;
    color:#fff;
    padding:10px;
    font-weight:bold;
}

.box table{
    box-shadow:none;
    border-radius:0;
}

.resumo{
    display:flex;
    gap:20px;
    margin-top:15px;
    flex-wrap:wrap;
}

.card{
    flex:1;
    min-width:220px;
    background:#fff;
    border-radius:8px;
    padding:15px;
    box-shadow:0 2px 6px rgba(0,0,0,.10);
}

.card h3{
    margin-top:0;
}

.total{
    color:#2e7d32;
    font-size:20px;
    font-weight:bold;
}

.saldo{
    color:#c62828;
    font-size:20px;
    font-weight:bold;
}

#filtro-status{
    padding:6px;
}

@media(max-width:900px){

table th,
table td{
    font-size:13px;
    padding:8px;
}

.btn{
    font-size:12px;
}

}

</style>

<script>

function toggleItens(id){

    let rows=document.querySelectorAll(".item-of-"+id);

    rows.forEach(function(row){

        if(row.style.display=="table-row"){
            row.style.display="none";
        }else{
            row.style.display="table-row";
        }

    });

}

function registrarPagamento(id,total){

    let valor=prompt(
        "Valor total da venda: R$ "+Number(total).toFixed(2)+
        "\n\nInforme o valor pago:"
    );

    if(valor==null)
        return;

    valor=valor.replace(",", ".");

    if(isNaN(valor) || Number(valor)<=0){
        alert("Informe um valor válido.");
        return;
    }

    let observacao=prompt("Observação (opcional):","");

    fetch(
        "registrar_pagamento.php?id_venda="+id+
        "&valor_pago="+valor+
        "&observacao="+encodeURIComponent(observacao)
    )
    .then(r=>r.text())
    .then(r=>{
        alert(r);
        location.reload();
    });

}

function gerarCanhoto(id){

    window.open(
        "gerar_canhoto.php?id_venda="+id,
        "_blank"
    );

}

function removerVenda(id){

    if(confirm("Deseja remover esta venda?")){

        fetch("remover_venda.php?id_venda="+id)
        .then(r=>r.text())
        .then(r=>{
            alert(r);
            location.reload();
        });

    }

}

function filtrarStatus(select){

    location.href="?status="+select.value;

}

</script>

</head>

<body>

<?php include '../base/cabecalho.php'; ?>

<div class="container">

<h1 style="text-align:center;">Lista de Vendas</h1>

<div style="text-align:right;margin-bottom:20px;">

<label><strong>Status:</strong></label>

<select id="filtro-status" onchange="filtrarStatus(this)">

<option value="">Todos</option>

<option value="pendente" <?= $statusFiltro=="pendente"?"selected":"" ?>>Pendente</option>

<option value="parcial" <?= $statusFiltro=="parcial"?"selected":"" ?>>Parcial</option>

<option value="aprovada" <?= $statusFiltro=="aprovada"?"selected":"" ?>>Aprovada</option>

</select>

</div>

<table>

<thead>

<tr>

<th>Nº Venda</th>
<th>Cliente</th>
<th>CPF/CNPJ</th>
<th>Telefone</th>
<th>Responsável</th>
<th>Data</th>
<th>Status</th>
<th>Ações</th>

</tr>

</thead>

<tbody>
    <?php while($v = $resVendas->fetch_assoc()): ?>

<?php

// Total da venda
$sqlTotal = "
SELECT SUM(valor_total) AS total
FROM itens_venda
WHERE id_venda=".$v['id_venda'];

$resTotal = $conn->query($sqlTotal);
$totalVenda = 0;

if($resTotal && $resTotal->num_rows > 0){
    $dadosTotal = $resTotal->fetch_assoc();
    $totalVenda = $dadosTotal['total'] ?? 0;
}


// Total pago
$sqlPago = "
SELECT SUM(valor_pago) AS total_pago
FROM pagamentos_venda
WHERE id_venda=".$v['id_venda'];

$resPago = $conn->query($sqlPago);
$totalPago = 0;

if($resPago && $resPago->num_rows > 0){
    $dadosPago = $resPago->fetch_assoc();
    $totalPago = $dadosPago['total_pago'] ?? 0;
}

$saldo = $totalVenda - $totalPago;

?>

<tr>

    <td><?= $v['numero_venda']; ?></td>

    <td><?= $v['cliente']; ?></td>

    <td><?= $v['cpf_cnpj']; ?></td>

    <td><?= $v['telefone']; ?></td>

    <td><?= $v['responsavel']; ?></td>

    <td><?= date("d/m/Y H:i",strtotime($v['data_venda'])); ?></td>

    <td>

        <span class="status status-<?= strtolower($v['status']); ?>">
            <?= ucfirst($v['status']); ?>
        </span>

    </td>

    <td>

        <button
            class="btn btn-expand"
            onclick="toggleItens(<?= $v['id_venda']; ?>)">
            Expandir
        </button>

        <button
            class="btn btn-approve"
            onclick="registrarPagamento(
                <?= $v['id_venda']; ?>,
                <?= $totalVenda; ?>
            )"
            <?= strtolower($v['status'])=="aprovada" ? "disabled" : ""; ?>>
            Registrar Pagamento
        </button>

        <button
            class="btn btn-canhoto"
            onclick="gerarCanhoto(<?= $v['id_venda']; ?>)">
            Canhoto
        </button>

        <button
            class="btn btn-remove"
            onclick="removerVenda(<?= $v['id_venda']; ?>)">
            Remover
        </button>

    </td>

</tr>

<tr class="item-row item-of-<?= $v['id_venda']; ?>">

<td colspan="8">

<div class="box">

<div class="box-title">
Dados da Venda
</div>

<table>

<tr>
<td><strong>Cliente:</strong></td>
<td><?= $v['cliente']; ?></td>

<td><strong>CPF/CNPJ:</strong></td>
<td><?= $v['cpf_cnpj']; ?></td>
</tr>

<tr>
<td><strong>Telefone:</strong></td>
<td><?= $v['telefone']; ?></td>

<td><strong>Responsável:</strong></td>
<td><?= $v['responsavel']; ?></td>
</tr>

<tr>
<td><strong>Endereço:</strong></td>
<td><?= $v['endereco']; ?></td>

<td><strong>CEP:</strong></td>
<td><?= $v['cep']; ?></td>
</tr>

<tr>
<td><strong>Data:</strong></td>
<td><?= date("d/m/Y H:i",strtotime($v['data_venda'])); ?></td>

<td><strong>Status:</strong></td>
<td><?= ucfirst($v['status']); ?></td>
</tr>

</table>

</div>
<div class="box">

    <div class="box-title">
        Produtos da Venda
    </div>

    <table>

        <tr>
            <th>Produto</th>
            <th>Quantidade</th>
            <th>Unidade</th>
            <th>Tipo</th>
            <th>Valor Unitário</th>
            <th>Total</th>
        </tr>

<?php

$sqlItens = "
SELECT 
    iv.*,
    d.nome_produto
FROM itens_venda iv
LEFT JOIN deposito d 
    ON d.id = iv.produto
WHERE iv.id_venda=".$v['id_venda'];

$resItens = $conn->query($sqlItens);

while($i = $resItens->fetch_assoc()){

?>

<tr>
  
    <td><?= htmlspecialchars($i['nome_produto'] ?? 'Produto não encontrado'); ?></td>

    <td><?= $i['quantidade']; ?></td>

    <td><?= $i['unidade']; ?></td>

    <td><?= $i['tipo']; ?></td>

    <td>
        R$ <?= number_format($i['valor_unitario'],2,',','.'); ?>
    </td>

    <td>
        R$ <?= number_format($i['valor_total'],2,',','.'); ?>
    </td>

</tr>

<?php } ?>

<tr>

    <td colspan="5" align="right">
        <strong>Total da Venda</strong>
    </td>

    <td>
        <strong>
            R$ <?= number_format($totalVenda,2,',','.'); ?>
        </strong>
    </td>

</tr>

</table>

</div>



<div class="box">

<div class="box-title">
Histórico de Pagamentos
</div>

<table>

<tr>

<th>Data</th>
<th>Valor Pago</th>
<th>Usuário</th>
<th>Observação</th>

</tr>

<?php

$sqlPagamentos = "
SELECT *
FROM pagamentos_venda
WHERE id_venda=".$v['id_venda']."
ORDER BY data_pagamento ASC";

$resPagamentos = $conn->query($sqlPagamentos);

if($resPagamentos->num_rows > 0){

while($p = $resPagamentos->fetch_assoc()){

?>

<tr>

<td>
<?= date("d/m/Y H:i",strtotime($p['data_pagamento'])); ?>
</td>

<td>
R$ <?= number_format($p['valor_pago'],2,',','.'); ?>
</td>

<td>
<?= $p['usuario']; ?>
</td>

<td>
<?= $p['observacao']; ?>
</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="4" align="center">

Nenhum pagamento registrado.

</td>

</tr>

<?php } ?>

</table>

</div>



<div class="resumo">

<div class="card">

<h3>Total da Venda</h3>

<div class="total">

R$ <?= number_format($totalVenda,2,',','.'); ?>

</div>

</div>



<div class="card">

<h3>Total Pago</h3>

<div class="total">

R$ <?= number_format($totalPago,2,',','.'); ?>

</div>

</div>



<div class="card">

<h3>Saldo Restante</h3>

<div class="saldo">

R$ <?= number_format($saldo,2,',','.'); ?>

</div>

</div>

</div>

</td>

</tr>
<?php endwhile; ?>

    </tbody>

</table>

</div>

<?php include '../base/rodape.php'; ?>

</body>
</html>