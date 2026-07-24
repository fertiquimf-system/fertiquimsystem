<?php
require_once '../conexaohost/conexao.php';
$nf_id = isset($_POST['nf_id']) ? (int)$_POST['nf_id'] : 0;
if ($nf_id === 0) {
    die("ID da nota fiscal inválido.");
}

// --- Buscar os dados da NF pendente
$nf_result = $conn->query("SELECT * FROM nf_pendente WHERE id = $nf_id");
if ($nf_result && $nf_result->num_rows > 0) {
    $nf = $nf_result->fetch_assoc();

// Inserir na tabela nf_aceitas
$stmt = $conn->prepare("INSERT INTO nf_aceitas 
    (numero_nf, nome_fantasia, cnpj, telefone, endereco, cep, responsavel_entrega, cpf_responsavel, data_confirmacao)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");

if ($stmt) {
    $stmt->bind_param("ssssssss", 
        $nf['numero_nf'], 
        $nf['nome_fantasia'], 
        $nf['cnpj'], 
        $nf['telefone'], 
        $nf['endereco'], 
        $nf['cep'],
        $nf['responsavel_entrega'], 
        $nf['cpf_responsavel']
    );

        if (!$stmt->execute()) {
            die("Erro ao salvar NF aceita: " . $stmt->error);
        }
        $nova_nf_id = $stmt->insert_id;
        $stmt->close();
    } else {
        die("Erro ao preparar inserção da NF aceita: " . $conn->error);
    }
} else {
    die("NF não encontrada na tabela pendente.");
}

// --- Processar fertilizantes pendentes
$result = $conn->query("SELECT * FROM fertilizantes_pendentes WHERE nf_id = $nf_id");
if (!$result || $result->num_rows === 0) {
    die("Nenhum fertilizante encontrado para esta NF.");
}

while ($row = $result->fetch_assoc()) {
    $nome = $conn->real_escape_string($row['nome']);
    $quantidade = (float)$row['quantidade'];
    $unidade = $conn->real_escape_string($row['unidade']);
    $tipo = $conn->real_escape_string($row['tipo']); // <-- TIPO

    // Inserir fertilizante na tabela fertilizantes_aceitos
    $stmt = $conn->prepare("INSERT INTO fertilizantes_aceitos (nf_id, nome_produto, quantidade, unidade, tipo, data_insercao) VALUES (?, ?, ?, ?, ?, NOW())");
    if (!$stmt) {
        die("Erro ao preparar inserção em fertilizantes_aceitos: " . $conn->error);
    }
    $stmt->bind_param("isdss", $nova_nf_id, $nome, $quantidade, $unidade, $tipo);
    if (!$stmt->execute()) {
        die("Erro ao salvar fertilizante aceito: " . $stmt->error);
    }
    $stmt->close();

    // Verifica se já existe no estoque
    $check = $conn->query("SELECT id, quantidade FROM estoque_fertilizantes WHERE nome_produto = '$nome' AND unidade = '$unidade'");
    if (!$check) {
        die("Erro na verificação do estoque: " . $conn->error);
    }

    if ($check->num_rows > 0) {
        $existente = $check->fetch_assoc();
        $nova_quantidade = $existente['quantidade'] + $quantidade;
        $estoque_id = $existente['id'];

        // Atualiza quantidade e tipo no estoque
        if (!$conn->query("UPDATE estoque_fertilizantes SET quantidade = $nova_quantidade, tipo = '$tipo', data_atualizacao = NOW() WHERE id = $estoque_id")) {
            die("Erro ao atualizar estoque: " . $conn->error);
        }
    } else {
        // Insere novo produto no estoque com tipo
        $stmt = $conn->prepare("INSERT INTO estoque_fertilizantes (nome_produto, quantidade, unidade, tipo, data_atualizacao) VALUES (?, ?, ?, ?, NOW())");
        if (!$stmt) {
            die("Erro ao preparar inserção no estoque: " . $conn->error);
        }
        $stmt->bind_param("sdss", $nome, $quantidade, $unidade, $tipo);
        if (!$stmt->execute()) {
            die("Erro ao inserir novo fertilizante no estoque: " . $stmt->error);
        }
        $stmt->close();
    }
}

// --- Limpar registros pendentes
$conn->query("DELETE FROM fertilizantes_pendentes WHERE nf_id = $nf_id");
$conn->query("DELETE FROM nf_pendente WHERE id = $nf_id");

$conn->close();

// Redireciona
header("Location: pendente.php");
exit;
?>
