<?php
require_once '../conexaohost/conexao.php';

header('Content-Type: application/json; charset=utf-8');

// validação
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([]);
    exit;
}

$id = intval($_GET['id']);

// busca veículo
$sql = "SELECT placa, modelo, marca, ano 
        FROM frota_veiculos 
        WHERE id = $id 
        LIMIT 1";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode([]);
}
