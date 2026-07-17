<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_GET['id_venda'])) {
    die("ID da venda não informado.");
}

$id_venda = intval($_GET['id_venda']);

// Buscar venda
$sqlVenda = "SELECT * FROM vendas WHERE id_venda = ?";
$stmtVenda = $conn->prepare($sqlVenda);
$stmtVenda->bind_param("i", $id_venda);
$stmtVenda->execute();
$resVenda = $stmtVenda->get_result();
$venda = $resVenda->fetch_assoc();

if (!$venda) {
    die("Venda não encontrada.");
}

// Buscar itens com nome do produto
$sqlItens = "
    SELECT
        i.*,
        d.nome_produto
    FROM itens_venda i
    LEFT JOIN deposito d
        ON d.id = i.produto
    WHERE i.id_venda = ?
";

$stmtItens = $conn->prepare($sqlItens);
$stmtItens->bind_param("i", $id_venda);
$stmtItens->execute();
$resItens = $stmtItens->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Pedido Nº <?php echo $venda['numero_venda']; ?></title>

<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

<style>

body{
    font-family:'Roboto',sans-serif;
    margin:40px;
    background:#f4f6f9;
    color:#333;
    position:relative;
}

body::before{
    content:"";
    position:fixed;
    top:50%;
    left:50%;
    width:600px;
    height:600px;
    background:url('../img/logo.png') no-repeat center center;
    background-size:contain;
    opacity:.05;
    transform:translate(-50%,-50%);
    z-index:-1;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:4px solid #2e7d32;
    padding-bottom:15px;
    margin-bottom:25px;
}

.empresa h1{
    margin:0;
    color:#2e7d32;
    font-size:24px;
}

.empresa p{
    margin:2px 0;
    font-size:13px;
}

.pedido-info{
    text-align:right;
}

.section{
    background:#fff;
    border:1px solid #ddd;
    border-radius:10px;
    padding:20px;
    margin-bottom:25px;
    box-shadow:0 2px 6px rgba(0,0,0,.05);
}

.section h3{
    margin-top:0;
    color:#2e7d32;
}

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:10px;
}

table{
    width:100%;
    border-collapse:collapse;
}

table th,
table td{
    border:1px solid #ddd;
    padding:10px;
    text-align:center;
}

table th{
    background:#2e7d32;
    color:#fff;
}

table tr:nth-child(even){
    background:#f9f9f9;
}

.total{
    margin-top:15px;
    text-align:right;
    font-size:18px;
    font-weight:bold;
    color:#2e7d32;
}

.banco{
    display:flex;
    gap:15px;
    flex-wrap:wrap;
}

.banco div{
    flex:1;
    background:#fff;
    border:1px solid #ddd;
    border-radius:8px;
    padding:15px;
}

.banco h4{
    margin-top:0;
    color:#2e7d32;
}

.status{
    margin:30px auto;
    display:inline-block;
    padding:12px 25px;
    border-radius:8px;
    color:#fff;
    font-weight:bold;
}

.pago{
    background:#2e7d32;
}

.pendente{
    background:#f57c00;
}

.outro{
    background:#c62828;
}

.assinaturas{
    display:flex;
    justify-content:space-between;
    margin-top:70px;
}

.assinaturas div{
    width:45%;
    text-align:center;
    border-top:1px solid #000;
    padding-top:5px;
}

.btn-print{
    margin-top:40px;
    background:#2e7d32;
    color:#fff;
    border:none;
    padding:14px 24px;
    border-radius:6px;
    cursor:pointer;
    font-size:15px;
}

.btn-print:hover{
    background:#1b5e20;
}

</style>

</head>
<body>

<div class="header">

<div class="empresa">
<h1>FERTIQUIM FERTILIZANTES LTDA</h1>
<p>CNPJ: 50.788.221/0001-44 | IE: 91162002-25</p>
<p>Av. Paraguai, 342 - São José, Pará de Minas - MG</p>
</div>

<div class="pedido-info">
<p><strong>Nº PEDIDO:</strong> <?php echo $venda['numero_venda']; ?></p>
<p><strong>DATA:</strong> <?php echo date("d/m/Y",strtotime($venda['data_venda'])); ?></p>
</div>

</div>

<div class="section">

<h3>Informações do Cliente</h3>

<div class="grid">

<p><strong>Nome:</strong> <?php echo $venda['cliente']; ?></p>
<p><strong>CNPJ/CPF:</strong> <?php echo $venda['cpf_cnpj']; ?></p>
<p><strong>Celular:</strong> <?php echo $venda['telefone']; ?></p>
<p><strong>Endereço:</strong> <?php echo $venda['endereco']; ?> - CEP: <?php echo $venda['cep']; ?></p>
<p><strong>Responsável:</strong> <?php echo $venda['responsavel']; ?></p>

</div>

</div>

<div class="section">

<h3>Itens do Pedido</h3>

<table>

<thead>

<tr>
<th>Produto</th>
<th>Quantidade</th>
<th>Unidade</th>
<th>Tipo</th>
<th>Valor Unitário (R$)</th>
<th>Total (R$)</th>
</tr>

</thead>

<tbody>

<?php

$totalGeral=0;

while($item=$resItens->fetch_assoc()):

$totalGeral+=$item['valor_total'];

?>

<tr>

<td><?php echo htmlspecialchars($item['nome_produto']); ?></td>

<td><?php echo $item['quantidade']; ?></td>

<td><?php echo $item['unidade']; ?></td>

<td><?php echo $item['tipo']; ?></td>

<td><?php echo number_format($item['valor_unitario'],2,',','.'); ?></td>

<td><?php echo number_format($item['valor_total'],2,',','.'); ?></td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

<div class="total">
TOTAL GERAL: R$ <?php echo number_format($totalGeral,2,',','.'); ?>
</div>

</div>

<div class="banco">

<div>
<h4>Banco</h4>
<p>Bradesco</p>
</div>

<div>
<h4>Agência / Conta</h4>
<p>1921-6 / 16618-9</p>
</div>

<div>
<h4>PIX / Email</h4>
<p>comercial@fertiquim.com.br</p>
</div>

<div>
<h4>CNPJ</h4>
<p>50.788.221/0001-44</p>
</div>

</div>

<div class="status <?php echo $venda['status']=='aprovada' ? 'pago' : ($venda['status']=='pendente' ? 'pendente' : 'outro'); ?>">

<?php

if($venda['status']=='aprovada'){

echo "PEDIDO PAGO";

if(!empty($venda['forma_pagamento'])){
echo " - Forma: ".$venda['forma_pagamento'];
}

}elseif($venda['status']=='pendente'){

echo "PEDIDO PENDENTE";

}else{

echo strtoupper($venda['status']);

}

?>

</div>

<div class="assinaturas">

<div>Assinatura do Cliente</div>

<div>Assinatura do Responsável</div>

</div>

<button class="btn-print" onclick="window.print()">
🖨️ Imprimir Pedido
</button>

</body>
</html>