<?php

require_once '../conexaohost/conexao.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    exit("Acesso inválido.");
}

if (!isset($_POST['id'])) {
    http_response_code(400);
    exit("ID não fornecido.");
}

$id = $_POST['id'];
$stmt = $conn->prepare("DELETE FROM cadastro_funcionario WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo $stmt->affected_rows > 0 ? "sucesso" : "erro";
?>
