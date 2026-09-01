<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

// ===============================
// REVENDEDORES
// ===============================
$sqlRevendedores = "
SELECT
    id,
    matricula,
    nome
FROM revenda
ORDER BY nome
";

$resRevendedores = $conn->query($sqlRevendedores);

// ===============================
// FILTRO
// ===============================
$idRevendedor = isset($_GET['revendedor']) ? intval($_GET['revendedor']) : 0;

// ===============================
// VENDAS
// ===============================
$vendas = [];

if ($idRevendedor > 0) {

    $sqlVendas = "
    SELECT
        v.id_venda,
        v.numero_venda,
        v.data_venda,
        v.cliente,
        v.status,
        r.nome AS revendedor,

        COALESCE(SUM(iv.valor_total),0) AS total_venda,

        c.percentual,
        c.valor_comissao

    FROM vendas v

    INNER JOIN revenda r
        ON r.id = v.revendedor_id

    LEFT JOIN itens_venda iv
        ON iv.id_venda = v.id_venda

    LEFT JOIN comissoes_revendedores c
        ON c.id_venda = v.id_venda

    WHERE v.revendedor_id = ?

    GROUP BY
        v.id_venda

    ORDER BY
        v.data_venda DESC
    ";

    $stmt = $conn->prepare($sqlVendas);
    $stmt->bind_param("i", $idRevendedor);
    $stmt->execute();

    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $vendas[] = $row;
    }
}

// ===============================
// CARDS
// ===============================

$totalVendas = count($vendas);

$totalAprovadas = 0;
$totalPendentes = 0;
$totalCanceladas = 0;

$totalVendido = 0;
$totalComissao = 0;

foreach ($vendas as $v){

    $totalVendido += $v['total_venda'];
    $totalComissao += $v['valor_comissao'];

    if($v['status'] == 'aprovada'){
        $totalAprovadas++;
    }

    elseif($v['status'] == 'pendente'){
        $totalPendentes++;
    }

    else{
        $totalCanceladas++;
    }

}
?>