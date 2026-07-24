<?php
require_once '../conexaohost/conexao.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['username'];
    $senha = $_POST['password'];
    $matricula = $_POST['matricula'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ? AND matricula = ?");
    $stmt->bind_param("ss", $usuario, $matricula);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $dados = $resultado->fetch_assoc();

        // Comparação simples (ideal: usar password_hash)
        if ($senha === $dados['senha']) {
            $_SESSION['nome_usuario'] = $dados['nome'];
            $_SESSION['funcao_usuario'] = $dados['funcao']; // <- ESSENCIAL

            // Redireciona conforme matrícula
            if ($dados['matricula'] == '2501') {
                header("Location: ../pginicial/pginicial.php");
                exit;
            } elseif ($dados['matricula'] == '2502') {
                header("Location: ../FERTIQUIM2502/pginicial/pginicial.php");
                exit;
            } elseif ($dados['matricula'] == '1707') {
                header("Location: pagina1707.php");
                exit;
            } else {
                header("Location: ../pginicial/pginicial.php");
                exit;
            }
        } else {
            $erro = "Senha incorreta.";
        }
    } else {
        $erro = "Usuário ou matrícula não encontrados.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../css/estilologin.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (isset($erro)) echo "<p style='color:red;'>$erro</p>"; ?>
        <form method="POST" action="">
            <label for="matricula">Matrícula</label>
            <input type="text" id="matricula" name="matricula" required>

            <label for="username">Usuário</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Senha</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
