<?php
require_once '../conexaohost/conexao.php';
session_start();

$idRevendedor = isset($_GET['revendedor']) ? intval($_GET['revendedor']) : 0;

// Buscar todos os revendedores
$sqlRevendedores = "SELECT * FROM revenda ORDER BY nome ASC";
$resRevendedores = $conn->query($sqlRevendedores);

// Buscar nome do revendedor selecionado
$nomeRevendedor = "";

if($idRevendedor > 0){

    $sqlNome = "SELECT * FROM revenda WHERE id = $idRevendedor";
    $resNome = $conn->query($sqlNome);

    if($resNome->num_rows > 0){
        $nomeRevendedor = $resNome->fetch_assoc()['nome'];
    }

}

$sqlVendas = "";

if($idRevendedor > 0){

    $sqlVendas = "
        SELECT *
        FROM vendas
        WHERE matricula = $idRevendedor
        ORDER BY data_venda DESC
    ";

}else{

    $sqlVendas = "
        SELECT *
        FROM vendas
        ORDER BY data_venda DESC
    ";

}

$resVendas = $conn->query($sqlVendas);

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">

<title>Vendas por Revendedor</title>

<link rel="stylesheet" href="../css/estilo.css">

<style>

body{

    background:#f2f2f2;

}

.container-flex{

    display:flex;
    gap:20px;
    padding:20px;

}

.lateral{

    width:280px;
    background:white;
    border-radius:10px;
    box-shadow:0 2px 8px rgba(0,0,0,.15);
    padding:15px;

}

.lateral h2{

    margin-top:0;
    text-align:center;

}

.revendedor{

    display:block;

    text-decoration:none;

    color:#333;

    padding:12px;

    border-radius:6px;

    margin-bottom:8px;

    border:1px solid #ddd;

    transition:.2s;

}

.revendedor:hover{

    background:#1976d2;

    color:#fff;

}

.revendedor.ativo{

    background:#1976d2;

    color:white;

}

.conteudo{

    flex:1;

    background:white;

    border-radius:10px;

    box-shadow:0 2px 8px rgba(0,0,0,.15);

    padding:20px;

}

table{

    width:100%;

    border-collapse:collapse;

}

table th{

    background:#1976d2;

    color:white;

    padding:10px;

}

table td{

    padding:10px;

    border-bottom:1px solid #ddd;

}

.item-row{

    display:none;

    background:#fafafa;

}

.btn{

    border:none;

    background:#1976d2;

    color:white;

    padding:6px 10px;

    border-radius:5px;

    cursor:pointer;

}

.status{

    padding:5px 10px;

    border-radius:5px;

    color:white;

}

.status-pendente{

    background:#f39c12;

}

.status-aprovada{

    background:#27ae60;

}

</style>

<script>

function toggleItens(id){

    let linhas=document.querySelectorAll(".item-"+id);

    linhas.forEach(function(l){

        if(l.style.display=="table-row"){

            l.style.display="none";

        }else{

            l.style.display="table-row";

        }

    });

}

</script>

</head>

<body>

<?php include '../base/cabecalho.php'; ?>

<div class="container-flex">

<div class="lateral">

<h2>Revendedores</h2>

<a class="revendedor <?php echo $idRevendedor==0?'ativo':''; ?>" href="vendas_revendedores.php">
Todos
</a>

<?php while($r = $resRevendedores->fetch_assoc()){ ?>

<a
class="revendedor <?php echo $idRevendedor==$r['id']?'ativo':''; ?>"
href="vendas_revendedores.php?revendedor=<?php echo $r['id']; ?>">

<strong><?php echo $r['nome']; ?></strong>

<br>

<small>Matrícula: <?php echo $r['matricula']; ?></small>

</a>

<?php } ?>

</div>

<div class="conteudo">

<h2>

<?php

if($idRevendedor==0){

    echo "Todas as vendas";

}else{

    echo "Vendas de ".$nomeRevendedor;

}

?>

</h2>

<table>

<thead>

<tr>

<th>Nº Venda</th>

<th>Cliente</th>

<th>Telefone</th>

<th>Data</th>

<th>Status</th>

<th>Ações</th>

</tr>

</thead>

<tbody>
    <?php while($v = $resVendas->fetch_assoc()){ ?>

<tr>

    <td><?php echo $v['numero_venda']; ?></td>

    <td><?php echo $v['cliente']; ?></td>

    <td><?php echo $v['telefone']; ?></td>

    <td><?php echo date("d/m/Y H:i", strtotime($v['data_venda'])); ?></td>

    <td>

        <span class="status <?php echo $v['status']=="aprovada" ? "status-aprovada" : "status-pendente"; ?>">

            <?php echo ucfirst($v['status']); ?>

        </span>

    </td>

    <td>

        <button
            class="btn"
            onclick="toggleItens(<?php echo $v['id_venda']; ?>)">
            Ver Itens
        </button>

    </td>

</tr>

<?php

$sqlItens = "

SELECT *

FROM itens_venda

WHERE id_venda=".$v['id_venda'];

$resItens = $conn->query($sqlItens);

while($i = $resItens->fetch_assoc()){

?>

<tr class="item-row item-<?php echo $v['id_venda']; ?>">

    <td colspan="2">

        <strong>Produto:</strong>

        <?php echo $i['produto']; ?>

    </td>

    <td>

        Qtd:

        <?php echo $i['quantidade']; ?>

    </td>

    <td>

        <?php echo $i['unidade']; ?>

    </td>

    <td>

        <?php echo $i['tipo']; ?>

    </td>

    <td>

        <strong>

        R$

        <?php echo number_format($i['valor_total'],2,",","."); ?>

        </strong>

    </td>

</tr>

<?php } ?>

<?php } ?>

</tbody>

</table>

</div>

</div>

<?php include '../base/rodape.php'; ?>

</body>

</html>