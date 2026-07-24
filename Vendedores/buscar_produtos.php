<?php
require_once '../conexaohost/conexao.php';

$term = $_GET['term'] ?? '';

$stmt = $conn->prepare("SELECT nome_produto, unidade, tipo FROM estoque_fertilizantes 
                        WHERE nome_produto LIKE ? LIMIT 10");
$like = "%$term%";
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();

$produtos = [];
while ($row = $result->fetch_assoc()) {
    $produtos[] = [
        "label" => $row['nome_produto'],  // aparece na lista
        "value" => $row['nome_produto'],  // valor do input
        "unidade" => $row['unidade'],
        "tipo" => $row['tipo']
    ];
}

echo json_encode($produtos);
