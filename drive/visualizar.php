<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    die("Sessão expirada.");
}

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Arquivo inválido.");
}

$stmt = $conexao->prepare("
SELECT
    pasta_id,
    nome_original,
    nome_servidor,
    mime
FROM drive_arquivos
WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    die("Arquivo não encontrado.");
}

$arquivo = $resultado->fetch_assoc();

$caminho = "D:/UPLOAD/" .
            $arquivo['pasta_id'] .
            "/" .
            $arquivo['nome_servidor'];

if (!file_exists($caminho)) {
    die("Arquivo não existe no servidor.");
}

header("Content-Description: File Transfer");
header("Content-Type: " . $arquivo['mime']);
header("Content-Disposition: attachment; filename=\"" . basename($arquivo['nome_original']) . "\"");
header("Content-Length: " . filesize($caminho));
header("Cache-Control: must-revalidate");
header("Pragma: public");

readfile($caminho);
exit;