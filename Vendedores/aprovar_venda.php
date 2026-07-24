<?php
require_once '../conexaohost/conexao.php';
session_start();

if(!isset($_GET['id_venda'])){
    die("ID da venda não informado.");
}

$id_venda = intval($_GET['id_venda']);
$forma_pagamento = $_GET['forma_pagamento'] ?? '';

if(empty($forma_pagamento)){
    die("Forma de pagamento não informada.");
}

// Atualizar status e salvar forma de pagamento
$sql = "UPDATE vendas SET status = 'aprovada', forma_pagamento = ? WHERE id_venda = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $forma_pagamento, $id_venda);

if($stmt->execute()){
    echo "Venda aprovada com pagamento: $forma_pagamento";
}else{
    echo "Erro ao aprovar venda: " . $conn->error;
}
?>
