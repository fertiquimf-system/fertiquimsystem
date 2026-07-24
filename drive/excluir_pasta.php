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

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode([
        "status" => false,
        "mensagem" => "Pasta inválida."
    ]);
    exit;
}

$usuario = $_SESSION['nome_usuario'];

// Busca a pasta
$stmt = $conexao->prepare("
SELECT nome
FROM drive_pastas
WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {

    echo json_encode([
        "status" => false,
        "mensagem" => "Pasta não encontrada."
    ]);
    exit;

}

$pasta = $resultado->fetch_assoc();

// Verifica subpastas
$stmt = $conexao->prepare("
SELECT COUNT(*) AS total
FROM drive_pastas
WHERE pasta_pai = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$totalSubpastas = $stmt->get_result()->fetch_assoc()['total'];

if ($totalSubpastas > 0) {

    echo json_encode([
        "status" => false,
        "mensagem" => "A pasta possui subpastas."
    ]);
    exit;

}

// Verifica arquivos
$stmt = $conexao->prepare("
SELECT COUNT(*) AS total
FROM drive_arquivos
WHERE pasta_id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$totalArquivos = $stmt->get_result()->fetch_assoc()['total'];

if ($totalArquivos > 0) {

    echo json_encode([
        "status" => false,
        "mensagem" => "A pasta possui arquivos."
    ]);
    exit;

}

// Remove a pasta física
$diretorio = "D:/UPLOAD/" . $id;

if (is_dir($diretorio)) {
    @rmdir($diretorio);
}

// Exclui do banco
$stmt = $conexao->prepare("
DELETE FROM drive_pastas
WHERE id = ?
");

$stmt->bind_param("i", $id);

if (!$stmt->execute()) {

    echo json_encode([
        "status" => false,
        "mensagem" => "Erro ao excluir."
    ]);
    exit;

}

// Histórico
$stmt = $conexao->prepare("
INSERT INTO drive_historico
(usuario,acao,descricao)
VALUES (?,?,?)
");

$acao = "Excluir Pasta";
$descricao = "Excluiu a pasta '{$pasta['nome']}'";

$stmt->bind_param(
    "sss",
    $usuario,
    $acao,
    $descricao
);

$stmt->execute();

echo json_encode([
    "status" => true,
    "mensagem" => "Pasta excluída com sucesso."
]);