<?php
require_once '../conexaohost/conexao.php';
session_start();
include('../sessao/verifica_sessao.php');
restringirAcesso(['Administrador']);
$cadastro_sucesso = false;

if (isset($_POST['submit'])) {
    $nome = $_POST['nome'];
    $senha = $_POST['senha'];
    $usuario = $_POST['usuario'];
    $matricula = $_POST['matricula'];
    $funcao = $_POST['funcao'];

    $stmt = $conn->prepare("INSERT INTO usuarios (nome, usuario, senha, matricula, funcao) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nome, $usuario, $senha, $matricula, $funcao);

    if ($stmt->execute()) {
        $cadastro_sucesso = true;
    } else {
        echo "<script>alert('Erro ao cadastrar: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Funcionário</title>

    <!-- CSS padrão do sistema -->
    <link rel="stylesheet" href="../css/estilopadrao.css">

    <style>
        .conteudo-centro {
            margin: 90px auto 40px auto;
            background: #ffffff;
            width: 95%;
            max-width: 600px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        .conteudo-centro h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #225B0B;
        }

        form label {
            font-weight: bold;
            color: #225B0B;
        }

        form input[type="text"],
        form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        form input[type="submit"] {
            width: 100%;
            padding: 12px;
            background: #A7D129;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            color: #333;
            transition: 0.3s;
        }

        form input[type="submit"]:hover {
            background: #8EB822;
        }

        .voltar {
            text-align: center;
            margin-top: 15px;
        }

        .voltar a {
            text-decoration: none;
            color: #225B0B;
            font-weight: bold;
        }

        .voltar a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <!-- HEADER DO SISTEMA -->
    <?php include "../base/cabecalho.php"; ?>

    <!-- CONTEÚDO CENTRAL -->
    <div class="conteudo-centro">
        <h2>Registrar Funcionário</h2>

        <form action="" method="POST">
            <label for="matricula">Nº Matrícula</label>
            <input type="text" id="matricula" name="matricula" required>

            <label for="funcao">Função</label>
            <input type="text" id="funcao" name="funcao" required>

            <label for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" required>

            <label for="usuario">Usuário</label>
            <input type="text" id="usuario" name="usuario" required>

            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required>

            <input type="submit" name="submit" id="submit" value="Registrar">
        </form>

        <div class="voltar">
            <a href="../rh/rh.php">← Voltar</a>
        </div>
    </div>

    <!-- FOOTER DO SISTEMA -->
    <?php include "../base/rodape.php"; ?>

    <?php if ($cadastro_sucesso): ?>
        <script>alert("Usuário cadastrado com sucesso!");</script>
    <?php endif; ?>

</body>
</html>
