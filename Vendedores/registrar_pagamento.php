<?php
require_once '../conexaohost/conexao.php';
session_start();

if(!isset($_GET['id_venda']) || !isset($_GET['valor_pago'])){
    die('Dados não informados.');
}

$id_venda = intval($_GET['id_venda']);
$valor_informado = str_replace(',', '.', $_GET['valor_pago']);
$valor_informado = floatval($valor_informado);
$observacao = $_GET['observacao'] ?? '';
$usuario = $_SESSION['usuario'] ?? 'Sistema';

if($valor_informado <= 0){
    die('Valor inválido.');
}

// Buscar dados da venda
$sqlVenda = "
SELECT
    v.valor_pago,
    COALESCE(SUM(i.valor_total),0) AS valor_total
FROM vendas v
LEFT JOIN itens_venda i
    ON i.id_venda = v.id_venda
WHERE v.id_venda = ?
GROUP BY v.id_venda
";

$stmtVenda = $conn->prepare($sqlVenda);
$stmtVenda->bind_param("i", $id_venda);
$stmtVenda->execute();

$resVenda = $stmtVenda->get_result();

if($resVenda->num_rows == 0){
    die("Venda não encontrada.");
}

$venda = $resVenda->fetch_assoc();

$valor_total = floatval($venda['valor_total']);
$valor_pago_atual = floatval($venda['valor_pago']);
// Somar novo pagamento
$novo_valor_pago = $valor_pago_atual + $valor_informado;

// Não permitir pagar mais que o total
if($novo_valor_pago > $valor_total){
    die('O valor pago ultrapassa o valor total da venda.');
}

// Calcular saldo
$saldo = $valor_total - $novo_valor_pago;

// Definir status
if($novo_valor_pago == 0){
    $status = 'pendente';
}elseif($saldo > 0){
    $status = 'parcial';
}else{
    $status = 'aprovada';
}

// Iniciar transação
$conn->begin_transaction();

try {

    // Salvar histórico do pagamento
    $sqlPagamento = 'INSERT INTO pagamentos_venda
        (id_venda, valor_pago, usuario, observacao)
        VALUES (?, ?, ?, ?)';

    $stmtPagamento = $conn->prepare($sqlPagamento);
    $stmtPagamento->bind_param(
        'idss',
        $id_venda,
        $valor_informado,
        $usuario,
        $observacao
    );
    $stmtPagamento->execute();

    // Atualizar venda
    $sqlUpdate = 'UPDATE vendas
                  SET valor_pago = ?,
                      saldo = ?,
                      status = ?
                  WHERE id_venda = ?';

    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param(
        'ddsi',
        $novo_valor_pago,
        $saldo,
        $status,
        $id_venda
    );

    $stmtUpdate->execute();

    // Confirmar transação
    $conn->commit();

    echo 'Pagamento registrado com sucesso!\n\n';
    echo 'Valor pago agora: R$ ' . number_format($novo_valor_pago,2,',','.') . '\n';
    echo 'Saldo restante: R$ ' . number_format($saldo,2,',','.') . '\n';
    echo 'Status: ' . strtoupper($status);

} catch (Exception $e){

    $conn->rollback();
    echo 'Erro ao registrar pagamento: ' . $e->getMessage();
}
?>