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
$tipo = $_POST['tipo'] ?? '';
$novoNome = trim($_POST['novo_nome'] ?? '');

if ($id <= 0 || $novoNome == '') {
    echo json_encode([
        "status" => false,
        "mensagem" => "Dados inválidos."
    ]);
    exit;
}

$usuario = $_SESSION['nome_usuario'];

if ($tipo == "pasta") {

    $stmt = $conexao->prepare("
        UPDATE drive_pastas
        SET nome = ?
        WHERE id = ?
    ");

    $stmt->bind_param("si", $novoNome, $id);

    if (!$stmt->execute()) {

        echo json_encode([
            "status" => false,
            "mensagem" => "Erro ao renomear pasta."
        ]);
        exit;
    }

    $acao = "Renomear Pasta";
    $descricao = "Renomeou a pasta para '{$novoNome}'";

} elseif ($tipo == "arquivo") {

    // Mantém a extensão original
    $stmt = $conexao->prepare("
        SELECT extensao
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

    $nomeFinal = pathinfo($novoNome, PATHINFO_FILENAME) . "." . $arquivo['extensao'];

    $stmt = $conexao->prepare("
        UPDATE drive_arquivos
        SET nome_original = ?
        WHERE id = ?
    ");

    $stmt->bind_param("si", $nomeFinal, $id);

    if (!$stmt->execute()) {

        echo json_encode([
            "status" => false,
            "mensagem" => "Erro ao renomear arquivo."
        ]);
        exit;
    }

    $acao = "Renomear Arquivo";
    $descricao = "Renomeou o arquivo para '{$nomeFinal}'";

} else {

    echo json_encode([
        "status" => false,
        "mensagem" => "Tipo inválido."
    ]);
    exit;
}

// Histórico
$stmt = $conexao->prepare("
    INSERT INTO drive_historico
    (usuario, acao, descricao)
    VALUES (?, ?, ?)
");

$stmt->bind_param(
    "sss",
    $usuario,
    $acao,
    $descricao
);

$stmt->execute();

echo json_encode([
    "status" => true,
    "mensagem" => "Renomeado com sucesso."
]);