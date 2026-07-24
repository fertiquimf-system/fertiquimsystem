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

if (!isset($_FILES['arquivo'])) {
    echo json_encode([
        "status" => false,
        "mensagem" => "Nenhum arquivo enviado."
    ]);
    exit;
}

$pastaId = intval($_POST['pasta_id'] ?? 0);
$usuario = $_SESSION['nome_usuario'];

if ($pastaId <= 0) {
    echo json_encode([
        "status" => false,
        "mensagem" => "Pasta inválida."
    ]);
    exit;
}

// Verifica se a pasta existe
$stmt = $conexao->prepare("SELECT id FROM drive_pastas WHERE id = ?");
$stmt->bind_param("i", $pastaId);
$stmt->execute();

if ($stmt->get_result()->num_rows == 0) {
    echo json_encode([
        "status" => false,
        "mensagem" => "Pasta não encontrada."
    ]);
    exit;
}

$arquivo = $_FILES['arquivo'];

$nomeOriginal = $arquivo['name'];
$tamanho = $arquivo['size'];
$mime = mime_content_type($arquivo['tmp_name']);
$extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

$nomeServidor = uniqid() . "." . $extensao;

$diretorio = "D:/UPLOAD/" . $pastaId;

if (!is_dir($diretorio)) {
    mkdir($diretorio, 0777, true);
}

$caminhoFinal = $diretorio . "/" . $nomeServidor;

if (!move_uploaded_file($arquivo['tmp_name'], $caminhoFinal)) {

    echo json_encode([
        "status" => false,
        "mensagem" => "Erro ao enviar arquivo."
    ]);
    exit;
}

// Salva no banco
$stmt = $conexao->prepare("
INSERT INTO drive_arquivos
(
pasta_id,
nome_original,
nome_servidor,
extensao,
tamanho,
mime,
enviado_por
)
VALUES
(?,?,?,?,?,?,?)
");

$stmt->bind_param(
    "isssiss",
    $pastaId,
    $nomeOriginal,
    $nomeServidor,
    $extensao,
    $tamanho,
    $mime,
    $usuario
);

$stmt->execute();

$idArquivo = $conexao->insert_id;

// Histórico
$stmt = $conexao->prepare("
INSERT INTO drive_historico
(usuario,acao,descricao)
VALUES (?,?,?)
");

$acao = "Upload";
$descricao = "Enviou o arquivo '{$nomeOriginal}' para a pasta {$pastaId}";

$stmt->bind_param(
    "sss",
    $usuario,
    $acao,
    $descricao
);

$stmt->execute();

echo json_encode([
    "status" => true,
    "mensagem" => "Arquivo enviado com sucesso.",
    "id" => $idArquivo
]);