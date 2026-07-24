<?php
session_start();
require_once '../conexaohost/conexao.php';

// Verifica se o ID foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: consultar_cliente.php");
    exit;
}

$id = intval($_GET['id']);

// Preparar query para evitar SQL Injection
$stmt = $conn->prepare("DELETE FROM clientes WHERE id = ?");
$stmt->bind_param("i", $id);

// Executar
if ($stmt->execute()) {
    $_SESSION['msg'] = "Cliente excluído com sucesso!";
} else {
    $_SESSION['msg'] = "Erro ao excluir cliente.";
}

// Fechar
$stmt->close();
$conn->close();

// Redirecionar de volta
header("Location: consultar_cliente.php");
exit;
?>