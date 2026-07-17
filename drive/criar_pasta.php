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

$nome = trim($_POST['nome'] ?? '');
$pastaPai = !empty($_POST['pasta_pai']) ? intval($_POST['pasta_pai']) : null;
$usuario = $_SESSION['nome_usuario'];

if ($nome == '') {
    echo json_encode([
        "status" => false,
        "mensagem" => "Informe o nome da pasta."
    ]);
    exit;
}

try {

    // =========================
    // INSERE NO BANCO
    // =========================
    $stmt = $conexao->prepare("
        INSERT INTO drive_pastas (nome, pasta_pai, criado_por)
        VALUES (?, ?, ?)
    ");

    $stmt->bind_param("sis", $nome, $pastaPai, $usuario);
    $stmt->execute();

    $idPasta = $conexao->insert_id;

    // =========================
    // PASTA FÍSICA (C:)
    // =========================
    $base = "C:/UPLOAD";

    // cria pasta base se não existir
    if (!is_dir($base)) {
        mkdir($base, 0777, true);
    }

    // cria pasta da nova ID
    $pastaFinal = $base . "/" . $idPasta;

    if (!is_dir($pastaFinal)) {
        mkdir($pastaFinal, 0777, true);
    }

    echo json_encode([
        "status" => true,
        "mensagem" => "Pasta criada com sucesso!",
        "id" => $idPasta
    ]);

} catch (Exception $e) {

    echo json_encode([
        "status" => false,
        "mensagem" => "Erro ao criar pasta."
    ]);
}