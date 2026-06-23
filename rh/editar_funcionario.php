<?php
session_start();  
require_once '../conexaohost/conexao.php';

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    exit("Acesso proibido");
}

// Campos obrigatórios (tiramos salario daqui)
$campos_necessarios = [
    'id',
    'nome',
    'matricula_filial',
    'matricula_funcionario',
    'funcao',
    'cpf',
    'data_nascimento',
    'endereco',
    'numero_casa',
    'cep',
    'uf'
];

foreach ($campos_necessarios as $campo) {
    if (!isset($_POST[$campo])) {
        http_response_code(400);
        exit("Erro: Campo '$campo' não recebido");
    }
}

// Receber dados
$id                   = $_POST['id'];
$nome                 = $_POST['nome'];
$matricula_filial     = $_POST['matricula_filial'];
$matricula_funcionario= $_POST['matricula_funcionario'];
$funcao               = $_POST['funcao'];
$cpf                  = $_POST['cpf'];
$data_nascimento      = $_POST['data_nascimento'];
$endereco             = $_POST['endereco'];
$numero_casa          = $_POST['numero_casa'];
$cep                  = $_POST['cep'];
$uf                   = $_POST['uf'];
$salario              = $_POST['salario'] ?? '';

// 🔥 Montar SQL dinamicamente
if (!empty($salario) && $salario !== '********') {

    $sql = "UPDATE cadastro_funcionario SET
        nome = ?,
        matricula_filial = ?,
        matricula_funcionario = ?,
        funcao = ?,
        cpf = ?,
        data_nascimento = ?,
        endereco = ?,
        numero_casa = ?,
        cep = ?,
        uf = ?,
        salario = ?
        WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssssssdi",
        $nome,
        $matricula_filial,
        $matricula_funcionario,
        $funcao,
        $cpf,
        $data_nascimento,
        $endereco,
        $numero_casa,
        $cep,
        $uf,
        $salario,
        $id
    );

} else {

    // 🚫 NÃO atualiza salário
    $sql = "UPDATE cadastro_funcionario SET
        nome = ?,
        matricula_filial = ?,
        matricula_funcionario = ?,
        funcao = ?,
        cpf = ?,
        data_nascimento = ?,
        endereco = ?,
        numero_casa = ?,
        cep = ?,
        uf = ?
        WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssssssi",
        $nome,
        $matricula_filial,
        $matricula_funcionario,
        $funcao,
        $cpf,
        $data_nascimento,
        $endereco,
        $numero_casa,
        $cep,
        $uf,
        $id
    );
}

$stmt->execute();

echo ($stmt->affected_rows >= 0) ? "sucesso" : "erro";
?>