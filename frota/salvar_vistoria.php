<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

// =========================
// TRATAMENTO DE DADOS
// =========================
$validade_cnh = $_POST['data_validade_cnh'] ?? null;
$responsavel  = $_SESSION['nome_usuario'];

// =========================
// SQL
// =========================
$sql = "INSERT INTO vistorias_veiculos (
    veiculo_id, placa, modelo, marca, ano, km_atual,
    motorista, cnh, validade_cnh,
    luz_alta, luz_baixa, setas_dianteiras, setas_traseiras,
    luz_re, luz_freio, meia_luz, limpador_vidros,
    pneus, estepe, cintos_seguranca, lataria_geral,
    limpeza_veiculo, para_brisa, oleo_motor, agua_radiador,
    vidros_travas, barulhos_anormais,
    observacoes, responsavel_vistoria
) VALUES (
    ?,?,?,?,?,?,
    ?,?,?,
    ?,?,?,?,
    ?,?,?,?,
    ?,?,?,?,
    ?,?,?,?,
    ?,?,
    ?,?
)";

// =========================
// PREPARE (ESSENCIAL)
// =========================
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Erro no prepare: " . $conn->error);
}

// =========================
// BIND PARAM
// =========================
$stmt->bind_param(
    "isssiisssssssssssssssssssssss",

    $_POST['veiculo_id'],
    $_POST['placa'],
    $_POST['modelo'],
    $_POST['marca'],
    $_POST['ano'],
    $_POST['km_atual'],

    $_POST['motorista'],
    $_POST['cnh'],
    $validade_cnh,

    $_POST['luz_alta'],
    $_POST['luz_baixa'],
    $_POST['setas_dianteiras'],
    $_POST['setas_traseiras'],
    $_POST['luz_re'],
    $_POST['luz_freio'],
    $_POST['meia_luz'],
    $_POST['limpador_vidros'],
    $_POST['pneus'],
    $_POST['estepe'],
    $_POST['cintos_seguranca'],
    $_POST['lataria_geral'],
    $_POST['limpeza_veiculo'],
    $_POST['para_brisa'],
    $_POST['oleo_motor'],
    $_POST['agua_radiador'],
    $_POST['vidros_travas'],
    $_POST['barulhos_anormais'],

    $_POST['observacoes'],
    $responsavel
);

// =========================
// EXECUÇÃO
// =========================
if ($stmt->execute()) {
    header("Location: gerar_checkup.php?sucesso=1");
    exit;
} else {
    echo "Erro ao salvar vistoria: " . $stmt->error;
}
