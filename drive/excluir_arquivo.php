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
        "mensagem" => "Arquivo inválido."
    ]);
    exit;
}

$usuario = $_SESSION['nome_usuario'];

// Busca o arquivo
$stmt = $conexao->prepare("
SELECT
    pasta_id,
    nome_original,
    nome_servidor
FROM drive_arquivos
WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {

    echo json_encode([
        "status" => false,
        "mensagem" => "Arquivo não encontrado."
    ]);
    exit;
}

$arquivo = $resultado->fetch_assoc();

$caminho = "D:/UPLOAD/" .
            $arquivo['pasta_id'] .
            "/" .
            $arquivo['nome_servidor'];

// Remove o arquivo físico
if (file_exists($caminho)) {
    unlink($caminho);
}

// Remove do banco
$stmt = $conexao->prepare("
DELETE FROM drive_arquivos
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

$acao = "Excluir Arquivo";
$descricao = "Excluiu o arquivo '{$arquivo['nome_original']}'";

$stmt->bind_param(
    "sss",
    $usuario,
    $acao,
    $descricao
);

$stmt->execute();

echo json_encode([
    "status" => true,
    "mensagem" => "Arquivo excluído com sucesso."
]);