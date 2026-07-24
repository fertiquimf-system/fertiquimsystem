<?php
require_once '../conexaohost/conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipo_pessoa   = $_POST['tipo_pessoa'];
    $nome_razao    = $_POST['nome_razao'];
    $cpf_cnpj      = $_POST['cpf_cnpj'];
    $rg_ie         = $_POST['rg_ie'];
    $data_nasc     = $_POST['data_nascimento'];
    $telefone      = $_POST['telefone'];
    $celular       = $_POST['celular'];
    $email         = $_POST['email'];
    $endereco      = $_POST['endereco'];
    $numero        = $_POST['numero'];
    $complemento   = $_POST['complemento'];
    $bairro        = $_POST['bairro'];
    $cidade        = $_POST['cidade'];
    $estado        = $_POST['estado'];
    $cep           = $_POST['cep'];
    $observacoes   = $_POST['observacoes'];

    $sql = "INSERT INTO clientes (tipo_pessoa, nome_razao, cpf_cnpj, rg_ie, data_nascimento, telefone, celular, email, endereco, numero, complemento, bairro, cidade, estado, cep, observacoes)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssssssss", $tipo_pessoa, $nome_razao, $cpf_cnpj, $rg_ie, $data_nasc, $telefone, $celular, $email, $endereco, $numero, $complemento, $bairro, $cidade, $estado, $cep, $observacoes);

    if ($stmt->execute()) {
        echo "<script>alert('Cliente cadastrado com sucesso!'); window.location.href='cliente.php';</script>";
    } else {
        echo "Erro: " . $stmt->error;
    }
}
