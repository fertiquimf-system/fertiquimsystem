<?php
session_start();
require_once '../conexaohost/conexao.php';

if (!isset($_SESSION['funcao_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Relatório de Lucro</title>
<style>
body { font-family: Arial, sans-serif; background: #f8f9fa; margin:0; padding:0; }
.container { width: 100%; max-width: 700px; margin: 50px auto; background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.2); }
h2 { text-align: center; margin-bottom: 20px; color: #2e5d2e; }
form { display: flex; flex-direction: column; gap: 20px; }
label { font-weight: bold; }
input { padding: 10px; border: 1px solid #ccc; border-radius: 6px; }
button { padding: 12px; background: #2e5d2e; color: white; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; transition: 0.3s; }
button:hover { background: #1e401e; }
</style>
</head>
<body>
    <?php include '../base/administrador.php'; ?>
    <div class="container">
        <h2>Gerar Relatório de Lucro</h2>
        <form method="GET" action="relatorio.php">
            <input type="hidden" name="tipo" value="lucro">
            
            <label for="data_inicio">Data Início:</label>
            <input type="date" name="data_inicio" required>

            <label for="data_fim">Data Fim:</label>
            <input type="date" name="data_fim" required>

            <button type="submit" formaction="gerar_relatorio.php" formmethod="get">Gerar Relatório</button>
        </form>
    </div>
</body>
<?php include '../base/rodape.php'; ?>
</html>
