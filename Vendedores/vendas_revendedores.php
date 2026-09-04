<?php

require_once '../conexaohost/conexao.php';

session_start();

// =====================================================
// FILTROS
// =====================================================

$idRevendedor = isset($_POST['revendedor'])
    ? intval($_POST['revendedor'])
    : (isset($_GET['revendedor']) ? intval($_GET['revendedor']) : 0);

$mesSelecionado = isset($_POST['mes'])
    ? intval($_POST['mes'])
    : (isset($_GET['mes']) ? intval($_GET['mes']) : date('n'));

$anoSelecionado = isset($_POST['ano'])
    ? intval($_POST['ano'])
    : (isset($_GET['ano']) ? intval($_GET['ano']) : date('Y'));

// Segurança do mês
if ($mesSelecionado < 1 || $mesSelecionado > 12) {
    $mesSelecionado = date('n');
}

// Segurança do ano
if ($anoSelecionado < 2000 || $anoSelecionado > 2100) {
    $anoSelecionado = date('Y');
}

// =====================================================
// APLICAR COMISSÃO PELO VALOR BASE
// =====================================================
//
// Regra:
// Comissão do item = (valor_unitario - valor_base) x quantidade
//
// Exemplo:
// Venda: R$ 2.100,00/Ton
// Base:  R$ 2.000,00/Ton
// Qtd.:  25 Ton
// Comissão = (2.100 - 2.000) x 25 = R$ 2.500,00
//
// IMPORTANTE:
// A coluna itens_venda.valor_base precisa existir.
// SQL para criar:
// ALTER TABLE itens_venda
// ADD COLUMN valor_base DECIMAL(10,2) NULL AFTER valor_unitario;
//

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aplicar_comissao'])) {

    $idVenda = intval($_POST['id_venda']);

    // Os valores base chegam como array:
    // valor_base[id_item] = valor base informado
    $valoresBase = isset($_POST['valor_base']) && is_array($_POST['valor_base'])
        ? $_POST['valor_base']
        : [];

    if ($idVenda > 0 && !empty($valoresBase)) {

        // =====================================================
        // VERIFICAR VENDA E SE A COMISSÃO JÁ FOI APLICADA
        // =====================================================

        $sqlVerifica = "
            SELECT valor_comissao, data_comissao
            FROM vendas
            WHERE id_venda = $idVenda
            LIMIT 1
        ";

        $resVerifica = $conn->query($sqlVerifica);

        if ($resVerifica && $resVerifica->num_rows > 0) {

            $venda = $resVerifica->fetch_assoc();

            // Se ainda não existe comissão, permite aplicar.
            if ($venda['valor_comissao'] === null) {

                $valorComissaoTotal = 0;
                $itensValidos = 0;

                // =====================================================
                // CALCULAR E GRAVAR O VALOR BASE DE CADA ITEM
                // =====================================================

                foreach ($valoresBase as $idItem => $valorBaseInformado) {

                    $idItem = intval($idItem);

                    $valorBaseInformado = str_replace(
                        ['.', ','],
                        ['', '.'],
                        trim((string)$valorBaseInformado)
                    );

                    // Se o campo veio vazio, ignora.
                    if ($valorBaseInformado === '' || $idItem <= 0) {
                        continue;
                    }

                    $valorBase = floatval($valorBaseInformado);

                    // Não aceita valor base negativo.
                    if ($valorBase < 0) {
                        continue;
                    }

                    // Busca somente o item pertencente à venda.
                    $sqlItem = "
                        SELECT
                            id_item,
                            valor_unitario,
                            quantidade
                        FROM itens_venda
                        WHERE id_item = $idItem
                        AND id_venda = $idVenda
                        LIMIT 1
                    ";

                    $resItem = $conn->query($sqlItem);

                    if (!$resItem || $resItem->num_rows === 0) {
                        continue;
                    }

                    $item = $resItem->fetch_assoc();

                    $valorVendaUnitario = floatval($item['valor_unitario']);
                    $quantidade = floatval($item['quantidade']);

                    // =====================================================
                    // COMISSÃO DO ITEM
                    // =====================================================

                    $diferenca = $valorVendaUnitario - $valorBase;

                    // Se vender abaixo do preço base, não gera comissão negativa.
                    if ($diferenca < 0) {
                        $diferenca = 0;
                    }

                    $comissaoItem = round(
                        $diferenca * $quantidade,
                        2
                    );

                    $valorBaseBanco = number_format(
                        $valorBase,
                        2,
                        '.',
                        ''
                    );

                    $comissaoItemBanco = number_format(
                        $comissaoItem,
                        2,
                        '.',
                        ''
                    );

                    // Grava o preço base utilizado na comissão.
                    $sqlAtualizaItem = "
                        UPDATE itens_venda
                        SET valor_base = $valorBaseBanco
                        WHERE id_item = $idItem
                        AND id_venda = $idVenda
                    ";

                    if ($conn->query($sqlAtualizaItem)) {
                        $valorComissaoTotal += $comissaoItem;
                        $itensValidos++;
                    }
                }

                // Só aplica a comissão se pelo menos um item foi processado.
                if ($itensValidos > 0) {

                    $valorComissaoBanco = number_format(
                        round($valorComissaoTotal, 2),
                        2,
                        '.',
                        ''
                    );

                    // =====================================================
                    // GRAVAR COMISSÃO TOTAL DA VENDA
                    // =====================================================

                    $sqlComissao = "
                        UPDATE vendas
                        SET
                            valor_comissao = $valorComissaoBanco,
                            data_comissao = NOW()
                        WHERE id_venda = $idVenda
                        AND valor_comissao IS NULL
                    ";

                    $conn->query($sqlComissao);
                }
            }
        }
    }

    // Volta para a página mantendo os filtros.
    $url = "vendas_revendedores.php";

    $parametros = [];

    if ($idRevendedor > 0) {
        $parametros[] = "revendedor=" . $idRevendedor;
    }

    $parametros[] = "mes=" . $mesSelecionado;
    $parametros[] = "ano=" . $anoSelecionado;

    if (!empty($parametros)) {
        $url .= "?" . implode("&", $parametros);
    }

    header("Location: $url");
    exit;
}


// =====================================================
// MESES
// =====================================================

$meses = [

    1  => 'Janeiro',
    2  => 'Fevereiro',
    3  => 'Março',
    4  => 'Abril',
    5  => 'Maio',
    6  => 'Junho',
    7  => 'Julho',
    8  => 'Agosto',
    9  => 'Setembro',
    10 => 'Outubro',
    11 => 'Novembro',
    12 => 'Dezembro'

];


// =====================================================
// SEGURANÇA DO MÊS
// =====================================================

if($mesSelecionado < 1 || $mesSelecionado > 12){

    $mesSelecionado = date('n');

}


// =====================================================
// SEGURANÇA DO ANO
// =====================================================

if($anoSelecionado < 2000 || $anoSelecionado > 2100){

    $anoSelecionado = date('Y');

}


// =====================================================
// BUSCAR TODOS OS REVENDEDORES
// =====================================================

$sqlRevendedores = "

    SELECT *

    FROM revenda

    ORDER BY nome ASC

";

$resRevendedores = $conn->query($sqlRevendedores);


// =====================================================
// BUSCAR NOME DO REVENDEDOR SELECIONADO
// =====================================================

$nomeRevendedor = "";

$matriculaRevendedor = 0;


if($idRevendedor > 0){

    $sqlNome = "

        SELECT *

        FROM revenda

        WHERE id = $idRevendedor

    ";

    $resNome = $conn->query($sqlNome);


    if($resNome && $resNome->num_rows > 0){

        $revendedor = $resNome->fetch_assoc();

        $nomeRevendedor = $revendedor['nome'];

        $matriculaRevendedor = $revendedor['matricula'];

    }

}


// =====================================================
// BUSCAR VENDAS
// =====================================================
//
// IMPORTANTE:
// O link usa o ID da revenda.
// A tabela vendas usa a MATRÍCULA.
//
// Por isso usamos o INNER JOIN.
//

if($idRevendedor > 0){

    $sqlVendas = "

        SELECT v.*

        FROM vendas v

        INNER JOIN revenda r

            ON v.matricula = r.matricula

        WHERE r.id = $idRevendedor

        AND MONTH(v.data_venda) = $mesSelecionado

        AND YEAR(v.data_venda) = $anoSelecionado

        ORDER BY v.data_venda DESC

    ";

}else{

    $sqlVendas = "

        SELECT *

        FROM vendas

        WHERE MONTH(data_venda) = $mesSelecionado

        AND YEAR(data_venda) = $anoSelecionado

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


/* =====================================================
   FUNDO
   ===================================================== */

body{

    background:#f2f2f2;

}


/* =====================================================
   CONTAINER PRINCIPAL
   ===================================================== */

.container-flex{

    display:flex;

    gap:20px;

    padding:20px;

}


/* =====================================================
   LATERAL
   ===================================================== */

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


/* =====================================================
   REVENDEDORES
   ===================================================== */

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


/* =====================================================
   CONTEÚDO
   ===================================================== */

.conteudo{

    flex:1;

    background:white;

    border-radius:10px;

    box-shadow:0 2px 8px rgba(0,0,0,.15);

    padding:20px;

}


/* =====================================================
   FILTROS
   ===================================================== */

.filtros{

    background:#f7f7f7;

    padding:15px;

    border-radius:8px;

    margin-bottom:20px;

    border:1px solid #ddd;

}


.filtros form{

    display:flex;

    align-items:center;

    gap:10px;

    flex-wrap:wrap;

}


.filtros select{

    padding:8px 12px;

    border:1px solid #ccc;

    border-radius:5px;

    font-size:14px;

}


.filtros label{

    margin-left:5px;

}


/* =====================================================
   TABELA
   ===================================================== */

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


/* =====================================================
   LINHAS DOS ITENS
   ===================================================== */

.item-row{

    display:none;

    background:#fafafa;

}


/* =====================================================
   BOTÃO
   ===================================================== */

.btn{

    border:none;

    background:#1976d2;

    color:white;

    padding:6px 10px;

    border-radius:5px;

    cursor:pointer;

}


.btn:hover{

    background:#125ca8;

}


/* =====================================================
   STATUS
   ===================================================== */

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


.status-parcial{

    background:#3498db;

}


/* =====================================================
   MENSAGEM SEM VENDAS
   ===================================================== */

.sem-vendas{

    text-align:center;

    padding:30px;

    color:#777;

}
/* =====================================================
   COMISSÃO
   ===================================================== */

.comissao-box{
    padding:18px;
    margin:5px 0;
    background:#f4f6f8;
    border:1px solid #ddd;
    border-radius:8px;
}

.comissao-titulo{
    font-size:16px;
    font-weight:bold;
    margin-bottom:12px;
}

.comissao-subtitulo{
    margin-bottom:15px;
    color:#555;
    line-height:1.5;
}

.comissao-form{
    display:flex;
    flex-direction:column;
    gap:12px;
}

.comissao-item{
    display:flex;
    align-items:center;
    justify-content:flex-start;
    gap:10px;
    width:100%;
    min-width:0;
    padding:10px 12px;
    background:#fff;
    border:1px solid #ddd;
    border-radius:7px;
    box-sizing:border-box;
}

.comissao-item-nome{
    font-weight:600;
}

.comissao-item-info{
    font-size:13px;
    color:#555;
}
.comissao-input-area{
    display:flex;
    align-items:center;
    justify-content:flex-start;
    gap:6px;
    width:auto;
    margin-left:0;
}

.comissao-input-area input{
    width:120px;
    padding:9px;
    border:1px solid #ccc;
    border-radius:5px;
    font-size:14px;
    box-sizing:border-box;
}

.comissao-input-area span{
    font-weight:bold;
}

.comissao-previa{
    font-weight:600;
}

.btn-comissao{
    background:#27ae60;
    padding:9px 15px;
    width:fit-content;
}

.btn-comissao:hover{
    background:#219150;
}

.comissao-total-previa{
    padding:12px;
    margin-top:4px;
    background:#eaf7ee;
    border:1px solid #b9dfc4;
    border-radius:7px;
    font-size:16px;
}

.comissao-total-previa strong{
    font-size:18px;
}

/* =====================================================
   COMISSÃO JÁ APLICADA
   ===================================================== */

.comissao-aplicada{
    display:flex;
    flex-direction:column;
    gap:12px;
}

.comissao-info{
    display:flex;
    gap:30px;
    flex-wrap:wrap;
}

.comissao-info span{
    font-size:14px;
}

.comissao-info strong{
    font-size:16px;
}

.comissao-detalhes{
    width:100%;
    border-collapse:collapse;
    margin-top:5px;
    background:white;
}

.comissao-detalhes th{
    background:#e9ecef;
    color:#333;
    padding:8px;
    text-align:left;
    font-size:13px;
}

.comissao-detalhes td{
    padding:8px;
    border-bottom:1px solid #ddd;
    font-size:13px;
}

.comissao-detalhes .total{
    font-weight:bold;
    background:#f7f7f7;
}

/* =====================================================
   STATUS DA COMISSÃO
   ===================================================== */

.comissao-status{
    display:inline-block;
    width:fit-content;
    padding:7px 12px;
    border-radius:5px;
    font-weight:600;
}

.comissao-aguardando{
    background:#fff3cd;
    color:#856404;
    border:1px solid #ffeeba;
}

.comissao-liberada{
    background:#d4edda;
    color:#155724;
    border:1px solid #c3e6cb;
}

/* =====================================================
   RESPONSIVO - COMISSÃO
   ===================================================== */

@media(max-width:900px){

    .comissao-item{
        grid-template-columns:1fr;
    }

}
</style>

<script>

// =====================================================
// MOSTRAR / ESCONDER ITENS
// =====================================================

function toggleItens(id){

    let linhas = document.querySelectorAll(".item-" + id);

    linhas.forEach(function(linha){

        if(linha.style.display == "table-row"){

            linha.style.display = "none";

        }else{

            linha.style.display = "table-row";

        }

    });

}

// =====================================================
// FORMATAR NÚMERO
// =====================================================

function numeroBR(valor){

    valor = parseFloat(valor);

    if(isNaN(valor)){
        return 0;
    }

    return valor;
}

// =====================================================
// CALCULAR PRÉVIA DA COMISSÃO
// =====================================================

function calcularComissaoVenda(idVenda){

    let box = document.getElementById("comissao-" + idVenda);

    if(!box){
        return;
    }

    let inputs = box.querySelectorAll(".valor-base-input");

    let total = 0;

    inputs.forEach(function(input){

        let valorBase = numeroBR(
            input.value.replace(",", ".")
        );

        let valorVenda = numeroBR(
            input.dataset.valorVenda
        );

        let quantidade = numeroBR(
            input.dataset.quantidade
        );

        let diferenca = valorVenda - valorBase;

        if(diferenca < 0){
            diferenca = 0;
        }

        let comissao = diferenca * quantidade;

        total += comissao;

        let previa = input
            .closest(".comissao-item")
            .querySelector(".comissao-previa-valor");

        if(previa){

            previa.textContent =
                "R$ " +
                comissao.toLocaleString("pt-BR", {
                    minimumFractionDigits:2,
                    maximumFractionDigits:2
                });

        }

    });

    let totalElemento = box.querySelector(
        ".comissao-total-previa-valor"
    );

    if(totalElemento){

        totalElemento.textContent =
            "R$ " +
            total.toLocaleString("pt-BR", {
                minimumFractionDigits:2,
                maximumFractionDigits:2
            });

    }
}

// =====================================================
// ATUALIZAR PRÉVIA AO DIGITAR O VALOR BASE
// =====================================================

document.addEventListener("input", function(event){

    if(event.target.classList.contains("valor-base-input")){

        let idVenda = event.target.dataset.idVenda;

        calcularComissaoVenda(idVenda);

    }

});

</script>

</head>

<body>


<?php include '../base/cabecalho.php'; ?>


<div class="container-flex">


<!-- =====================================================
     LATERAL
     ===================================================== -->

<div class="lateral">

<h2>Revendedores</h2>


<a

    class="revendedor <?php echo $idRevendedor == 0 ? 'ativo' : ''; ?>"

    href="vendas_revendedores.php?mes=<?php echo $mesSelecionado; ?>&ano=<?php echo $anoSelecionado; ?>"

>

    Todos

</a>


<?php while($r = $resRevendedores->fetch_assoc()){ ?>


<a

    class="revendedor <?php echo $idRevendedor == $r['id'] ? 'ativo' : ''; ?>"

    href="vendas_revendedores.php?revendedor=<?php echo $r['id']; ?>&mes=<?php echo $mesSelecionado; ?>&ano=<?php echo $anoSelecionado; ?>"

>


    <strong>

        <?php echo htmlspecialchars($r['nome']); ?>

    </strong>


    <br>


    <small>

        Matrícula:

        <?php echo htmlspecialchars($r['matricula']); ?>

    </small>


</a>


<?php } ?>


</div>


<!-- =====================================================
     CONTEÚDO
     ===================================================== -->

<div class="conteudo">


<!-- =====================================================
     FILTRO MÊS / ANO
     ===================================================== -->

<div class="filtros">

<form method="GET">


<?php if($idRevendedor > 0){ ?>


<input

    type="hidden"

    name="revendedor"

    value="<?php echo $idRevendedor; ?>"

>


<?php } ?>


<label>

    <strong>Mês:</strong>

</label>


<select name="mes">


<?php

foreach($meses as $numero => $nome){

    $selected = ($mesSelecionado == $numero)

        ? 'selected'

        : '';

?>


<option

    value="<?php echo $numero; ?>"

    <?php echo $selected; ?>

>

    <?php echo $nome; ?>

</option>


<?php } ?>


</select>


<label>

    <strong>Ano:</strong>

</label>


<select name="ano">


<?php

$anoAtual = date('Y');


for(

    $ano = $anoAtual - 2;

    $ano <= $anoAtual + 1;

    $ano++

){

    $selected = ($anoSelecionado == $ano)

        ? 'selected'

        : '';

?>


<option

    value="<?php echo $ano; ?>"

    <?php echo $selected; ?>

>

    <?php echo $ano; ?>

</option>


<?php } ?>


</select>


<button

    type="submit"

    class="btn"

>

    Filtrar

</button>


</form>

</div>
<!-- =====================================================
     TÍTULO
     ===================================================== -->

<h2>

<?php

if($idRevendedor == 0){

    echo "Todas as vendas";

}else{

    echo "Vendas de " . htmlspecialchars($nomeRevendedor);

}

?>

 - <?php echo $meses[$mesSelecionado]; ?>/<?php echo $anoSelecionado; ?>

</h2>


<!-- =====================================================
     TABELA DE VENDAS
     ===================================================== -->

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


<?php

// =====================================================
// VERIFICAR SE EXISTEM VENDAS
// =====================================================

if($resVendas && $resVendas->num_rows > 0){

    while($v = $resVendas->fetch_assoc()){

?>


<!-- =====================================================
     LINHA PRINCIPAL DA VENDA
     ===================================================== -->

<tr>


    <!-- NÚMERO DA VENDA -->

    <td>

        <?php

        echo htmlspecialchars($v['numero_venda']);

        ?>

    </td>


    <!-- CLIENTE -->

    <td>

        <?php

        echo htmlspecialchars($v['cliente']);

        ?>

    </td>


    <!-- TELEFONE -->

    <td>

        <?php

        echo htmlspecialchars($v['telefone']);

        ?>

    </td>


    <!-- DATA -->

    <td>

        <?php

        echo date(

            "d/m/Y H:i",

            strtotime($v['data_venda'])

        );

        ?>

    </td>


    <!-- STATUS -->

    <td>


        <?php

        if($v['status'] == 'aprovada'){

            $classeStatus = 'status-aprovada';

        }elseif($v['status'] == 'parcial'){

            $classeStatus = 'status-parcial';

        }else{

            $classeStatus = 'status-pendente';

        }

        ?>


        <span

            class="status <?php echo $classeStatus; ?>"

        >

            <?php

            echo ucfirst(

                htmlspecialchars($v['status'])

            );

            ?>

        </span>


    </td>


    <!-- AÇÕES -->

    <td>


        <button

            type="button"

            class="btn"

            onclick="toggleItens(<?php echo $v['id_venda']; ?>)"

        >

            Ver Itens

        </button>


    </td>


</tr>



<?php


// =====================================================
// BUSCAR ITENS DA VENDA
// =====================================================

$sqlItens = "

    SELECT

        i.*,

        COALESCE(p.total_pago, 0) AS valor_pago

    FROM itens_venda i


    LEFT JOIN (

        SELECT

            id_venda,

            SUM(valor_pago) AS total_pago

        FROM pagamentos_venda

        GROUP BY id_venda

    ) p

        ON p.id_venda = i.id_venda


    WHERE i.id_venda = ".$v['id_venda'];



$resItens = $conn->query($sqlItens);


// =====================================================
// MOSTRAR ITENS
// =====================================================

if($resItens){

    while($i = $resItens->fetch_assoc()){

?>

<tr class="item-row item-<?php echo $v['id_venda']; ?>">

    <td colspan="2">
        <strong>Produto:</strong>
        <?php echo htmlspecialchars($i['produto']); ?>
    </td>

    <td>
        <strong>Qtd:</strong>
        <?php echo htmlspecialchars($i['quantidade']); ?>
    </td>

    <td>
        <strong>Pago:</strong>
        R$
        <?php
        echo number_format(
            $i['valor_pago'],
            2,
            ",",
            "."
        );
        ?>
    </td>

    <td>
        <?php echo htmlspecialchars($i['tipo']); ?>
    </td>

    <td>
        <strong>
            R$
            <?php
            echo number_format(
                $i['valor_total'],
                2,
                ",",
                "."
            );
            ?>
        </strong>
    </td>

</tr>

<?php

    }

}

?>
<?php
// =====================================================
// COMISSÃO DA VENDA
// =====================================================

// A comissão agora é considerada aplicada quando
// valor_comissao já foi gravado.
$comissaoAplicada = (
    $v['valor_comissao'] !== null
);

// Verifica se a venda está paga.
$statusVenda = strtolower(trim($v['status']));

$vendaPaga = (
    $statusVenda === 'pago' ||
    $statusVenda === 'aprovada'
);

// Busca os itens para mostrar os valores-base
// e calcular/exibir a comissão por item.
$sqlItensComissao = "
    SELECT
        id_item,
        produto,
        quantidade,
        tipo,
        valor_unitario,
        valor_total,
        valor_base
    FROM itens_venda
    WHERE id_venda = ".$v['id_venda']."
    ORDER BY id_item ASC
";

$resItensComissao = $conn->query($sqlItensComissao);
?>

<tr class="item-row item-<?php echo $v['id_venda']; ?>">

    <td colspan="6">

        <div
            class="comissao-box"
            id="comissao-<?php echo $v['id_venda']; ?>"
        >

            <div class="comissao-titulo">
                Comissão da Venda
            </div>

            <?php if (!$comissaoAplicada): ?>

                <div class="comissao-subtitulo">

                    Informe o <strong>valor base por unidade</strong>
                    de cada produto.
                </div>

                <?php if($resItensComissao && $resItensComissao->num_rows > 0): ?>

                    <form method="POST" class="comissao-form">

                        <input
                            type="hidden"
                            name="id_venda"
                            value="<?php echo $v['id_venda']; ?>"
                        >

                        <input
                            type="hidden"
                            name="mes"
                            value="<?php echo $mesSelecionado; ?>"
                        >

                        <input
                            type="hidden"
                            name="ano"
                            value="<?php echo $anoSelecionado; ?>"
                        >

                        <input
                            type="hidden"
                            name="revendedor"
                            value="<?php echo $idRevendedor; ?>"
                        >

                        <?php while($ci = $resItensComissao->fetch_assoc()): ?>

                            <?php
                            $valorVendaUnitario = floatval($ci['valor_unitario']);
                            $quantidadeItem = floatval($ci['quantidade']);
                            $valorBaseAtual = $ci['valor_base'] !== null
                                ? floatval($ci['valor_base'])
                                : 0;

                            $comissaoPrevia = max(
                                0,
                                ($valorVendaUnitario - $valorBaseAtual)
                                * $quantidadeItem
                            );
                            ?>

                            <div class="comissao-item">

                                <div class="comissao-item-nome">
                                    
                                </div>


                                <div class="comissao-input-area">

                                    <input
                                        type="number"
                                        class="valor-base-input"
                                        name="valor_base[<?php echo $ci['id_item']; ?>]"
                                        value="<?php echo $valorBaseAtual > 0 ? number_format($valorBaseAtual, 2, '.', '') : ''; ?>"
                                        min="0"
                                        step="0.01"
                                        placeholder="Ex: 2000,00"
                                        data-id-venda="<?php echo $v['id_venda']; ?>"
                                        data-valor-venda="<?php echo $valorVendaUnitario; ?>"
                                        data-quantidade="<?php echo $quantidadeItem; ?>"
                                        required
                                    >

                                    <span>
                                        / <?php echo htmlspecialchars($ci['tipo']); ?>
                                    </span>

                                </div>

                            </div>

                        <?php endwhile; ?>

                        <div class="comissao-total-previa">

                            Comissão total:

                            <strong class="comissao-total-previa-valor">
                                R$ 0,00
                            </strong>

                        </div>

                        <button
                            type="submit"
                            name="aplicar_comissao"
                            class="btn btn-comissao"
                            onclick="return confirm('Depois de aplicar, a comissão desta venda NÃO poderá ser alterada. Deseja continuar?');"
                        >
                            Aplicar Comissão
                        </button>

                    </form>

                    <script>
                        calcularComissaoVenda(
                            <?php echo $v['id_venda']; ?>
                        );
                    </script>

                <?php else: ?>

                    <div class="comissao-status comissao-aguardando">
                        Nenhum item encontrado para esta venda.
                    </div>

                <?php endif; ?>

            <?php else: ?>

                <div class="comissao-aplicada">

                    <div class="comissao-info">

                        <span>
                            Comissão total:
                            <strong>
                                R$
                                <?php
                                echo number_format(
                                    $v['valor_comissao'],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>
                            </strong>
                        </span>

                    </div>

                    <?php
                    // Reconsulta porque o resultado acima pode já ter
                    // sido consumido no formulário.
                    $resItensDetalhes = $conn->query("
                        SELECT
                            produto,
                            quantidade,
                            tipo,
                            valor_unitario,
                            valor_base
                        FROM itens_venda
                        WHERE id_venda = ".$v['id_venda']."
                        ORDER BY id_item ASC
                    ");
                    ?>

                    <?php if($resItensDetalhes && $resItensDetalhes->num_rows > 0): ?>

                        <table class="comissao-detalhes">

                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Qtd.</th>
                                    <th>Preço venda</th>
                                    <th>Preço base</th>
                                    <th>Diferença/un.</th>
                                    <th>Comissão</th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php
                            $totalDetalhado = 0;

                            while($di = $resItensDetalhes->fetch_assoc()):

                                $vendaUnit = floatval($di['valor_unitario']);
                                $baseUnit = floatval($di['valor_base']);
                                $qtd = floatval($di['quantidade']);

                                $diferencaUnit = max(
                                    0,
                                    $vendaUnit - $baseUnit
                                );

                                $comissaoItem = round(
                                    $diferencaUnit * $qtd,
                                    2
                                );

                                $totalDetalhado += $comissaoItem;
                            ?>

                                <tr>

                                    <td>
                                        <?php echo htmlspecialchars($di['produto']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($di['quantidade']); ?>
                                        <?php echo htmlspecialchars($di['tipo']); ?>
                                    </td>

                                    <td>
                                        R$
                                        <?php
                                        echo number_format(
                                            $vendaUnit,
                                            2,
                                            ',',
                                            '.'
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        R$
                                        <?php
                                        echo number_format(
                                            $baseUnit,
                                            2,
                                            ',',
                                            '.'
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        R$
                                        <?php
                                        echo number_format(
                                            $diferencaUnit,
                                            2,
                                            ',',
                                            '.'
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <strong>
                                            R$
                                            <?php
                                            echo number_format(
                                                $comissaoItem,
                                                2,
                                                ',',
                                                '.'
                                            );
                                            ?>
                                        </strong>
                                    </td>

                                </tr>

                            <?php endwhile; ?>

                                <tr class="total">

                                    <td colspan="5">
                                        Total calculado
                                    </td>

                                    <td>
                                        R$
                                        <?php
                                        echo number_format(
                                            $totalDetalhado,
                                            2,
                                            ',',
                                            '.'
                                        );
                                        ?>
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    <?php endif; ?>

                    <?php if ($vendaPaga): ?>

                        <div class="comissao-status comissao-liberada">
                            ✓ Comissão liberada para pagamento
                        </div>

                    <?php else: ?>

                        <div class="comissao-status comissao-aguardando">
                            ⏳ Aguardando venda ser paga
                        </div>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </div>

    </td>

</tr>

<?php

    }

?>
<!-- =====================================================
     SEM VENDAS
     ===================================================== -->

<tr>

    <td

        colspan="6"

        class="sem-vendas"

    >

        Nenhuma venda encontrada para

        <strong>

            <?php echo $meses[$mesSelecionado]; ?>

            /

            <?php echo $anoSelecionado; ?>

        </strong>


        <?php

        if($idRevendedor > 0){

            echo " para este revendedor.";

        }

        ?>


    </td>

</tr>


<?php

}

?>


</tbody>

</table>


</div>


</div>
<?php

// =====================================================
// FECHAR CONTEÚDO PRINCIPAL
// =====================================================

?>

</div>

</div>


<?php

// =====================================================
// RODAPÉ
// =====================================================

include '../base/rodape.php';

?>


</body>

</html>