<?php

require_once '../conexaohost/conexao.php';


if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Fertiquim - Sistema</title>
  <link rel="stylesheet" href="../css/estilo.css" />
</head>
<body>
  <header>
    <h1>FERTIQUIM Fertilizantes</h1>
    <nav>
      <a href="../pginicial/pginicial.php">Início</a>
      <a href="../admin/dashboard_admin.php">Painel Administrador</a>
      <a href="../pglogin/logout.php">Sair</a>
    </nav>
  </header>
