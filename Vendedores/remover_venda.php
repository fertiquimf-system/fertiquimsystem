<?php
require_once '../conexaohost/conexao.php';
session_start();

if(!isset($_GET['id_venda'])){
    die("ID da venda não informado.");
}

$id_venda = intval($_GET['id_venda']);

// Iniciar transação
$conn->begin_transaction();

try {
    // Remover itens da venda
    $conn->query("DELETE FROM itens_venda WHERE id_venda = $id_venda");
    // Remover venda
    $conn->query("DELETE FROM vendas WHERE id_venda = $id_venda");

    $conn->commit();
    echo "Venda removida com sucesso!";
} catch (Exception $e) {
    $conn->rollback();
    echo "Erro ao remover venda: " . $e->getMessage();
}
?>
