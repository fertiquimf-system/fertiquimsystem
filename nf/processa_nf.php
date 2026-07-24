<?php
require_once '../conexaohost/conexao.php';

// Recebe os dados da NF
$numero_nf = $_POST['numero_nf'];
$nome_fantasia = $_POST['nome_fantasia'];
$cnpj = $_POST['cnpj'];
$telefone = $_POST['telefone'];
$endereco = $_POST['endereco'];
$cep = $_POST['cep'];
$responsavel_entrega = $_POST['responsavel_entrega'];

// Insere a nota fiscal na tabela nf_pendente
$stmt = $conn->prepare("INSERT INTO nf_pendente 
    (numero_nf, nome_fantasia, cnpj, telefone, endereco, cep, responsavel_entrega) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("sssssss", $numero_nf, $nome_fantasia, $cnpj, $telefone, $endereco, $cep, $responsavel_entrega);

if (!$stmt->execute()) {
    die("Erro ao salvar a NF: " . $stmt->error);
}

$nf_id = $stmt->insert_id; // pega o ID da NF inserida
$stmt->close();

// Fertilizantes recebidos
$nomes = $_POST['fertilizante_nome'];
$quantidades = $_POST['fertilizante_quantidade'];
$unidades = $_POST['fertilizante_unidade'];
$tipos = $_POST['fertilizante_tipo'];

// Insere fertilizantes na tabela fertilizantes_pendentes
$stmt_fert = $conn->prepare("INSERT INTO fertilizantes_pendentes (nf_id, nome, quantidade, unidade, tipo) VALUES (?, ?, ?, ?, ?)");

foreach ($nomes as $index => $nome) {
    $quantidade = $quantidades[$index];
    $unidade = $unidades[$index];
    $tipo = $tipos[$index];

    $stmt_fert->bind_param("isdss", $nf_id, $nome, $quantidade, $unidade, $tipo);
    if (!$stmt_fert->execute()) {
        die("Erro ao inserir fertilizante: " . $stmt_fert->error);
    }
}

$stmt_fert->close();
$conn->close();

// Redireciona de volta para a página de NF pendente
header("Location: pendente.php");
exit;
?>
