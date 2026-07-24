<?php
require_once '../conexaohost/conexao.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['nome_usuario'])) {
    echo json_encode([
        "status" => false,
        "mensagem" => "Sessão expirada."
    ]);
    exit;
}

$pastaId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Busca informações da pasta
$stmt = $conexao->prepare("
SELECT *
FROM drive_pastas
WHERE id = ?
");

$stmt->bind_param("i", $pastaId);
$stmt->execute();

$pasta = $stmt->get_result()->fetch_assoc();

if (!$pasta) {

    echo json_encode([
        "status" => false,
        "mensagem" => "Pasta não encontrada."
    ]);
    exit;

}

// Lista subpastas
$stmt = $conexao->prepare("
SELECT *
FROM drive_pastas
WHERE pasta_pai = ?
ORDER BY nome
");

$stmt->bind_param("i", $pastaId);
$stmt->execute();

$pastas = [];

while ($row = $stmt->get_result()->fetch_assoc()) {
    $pastas[] = $row;
}

// Lista arquivos
$stmt = $conexao->prepare("
SELECT *
FROM drive_arquivos
WHERE pasta_id = ?
ORDER BY nome_original
");

$stmt->bind_param("i", $pastaId);
$stmt->execute();

$resultado = $stmt->get_result();

$arquivos = [];

while ($row = $resultado->fetch_assoc()) {
    $arquivos[] = $row;
}

echo json_encode([
    "status" => true,
    "pasta" => $pasta,
    "pastas" => $pastas,
    "arquivos" => $arquivos
]);