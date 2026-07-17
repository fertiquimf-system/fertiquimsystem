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

$pastaId = intval($_GET['pasta_id'] ?? 0);

if ($pastaId <= 0) {
    echo json_encode([
        "status" => false,
        "mensagem" => "Pasta inválida."
    ]);
    exit;
}

$stmt = $conexao->prepare("
SELECT *
FROM drive_arquivos
WHERE pasta_id = ?
ORDER BY data_upload DESC
");

$stmt->bind_param("i", $pastaId);
$stmt->execute();

$resultado = $stmt->get_result();

$arquivos = [];

while ($arquivo = $resultado->fetch_assoc()) {

    $tamanho = $arquivo['tamanho'];

    if ($tamanho >= 1073741824) {
        $tamanhoFormatado = round($tamanho / 1073741824, 2) . " GB";
    } elseif ($tamanho >= 1048576) {
        $tamanhoFormatado = round($tamanho / 1048576, 2) . " MB";
    } elseif ($tamanho >= 1024) {
        $tamanhoFormatado = round($tamanho / 1024, 2) . " KB";
    } else {
        $tamanhoFormatado = $tamanho . " Bytes";
    }

    $ext = strtolower($arquivo['extensao']);

    switch ($ext) {

        case 'pdf':
            $icone = "📕";
            break;

        case 'doc':
        case 'docx':
            $icone = "📘";
            break;

        case 'xls':
        case 'xlsx':
            $icone = "📗";
            break;

        case 'ppt':
        case 'pptx':
            $icone = "📙";
            break;

        case 'png':
        case 'jpg':
        case 'jpeg':
        case 'gif':
        case 'bmp':
        case 'webp':
            $icone = "🖼️";
            break;

        case 'zip':
        case 'rar':
        case '7z':
            $icone = "🗜️";
            break;

        case 'txt':
            $icone = "📄";
            break;

        case 'mp4':
        case 'avi':
        case 'mov':
            $icone = "🎥";
            break;

        case 'mp3':
        case 'wav':
            $icone = "🎵";
            break;

        default:
            $icone = "📁";
            break;
    }

    $arquivos[] = [

        "id" => $arquivo['id'],

        "nome" => $arquivo['nome_original'],

        "arquivo" => $arquivo['nome_servidor'],

        "icone" => $icone,

        "extensao" => strtoupper($arquivo['extensao']),

        "tamanho" => $tamanhoFormatado,

        "usuario" => $arquivo['enviado_por'],

        "data" => date(
            "d/m/Y H:i",
            strtotime($arquivo['data_upload'])
        )

    ];

}

echo json_encode([
    "status" => true,
    "arquivos" => $arquivos
]);