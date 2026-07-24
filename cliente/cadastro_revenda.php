<?php
session_start();
require_once '../conexaohost/conexao.php';

$cadastro_sucesso = false;

if (isset($_POST['submit'])) {
    $matricula = $_POST['matricula'];
    $nome = $_POST['nome'];
    $celular = $_POST['celular'];
    $cpf = $_POST['cpf'];
    $datanasc = $_POST['datanasc'];
    $endereco = $_POST['endereco'];
    $numero_casa = $_POST['numero_casa'];
    $cep = $_POST['cep'];
    $uf = $_POST['uf'];
    $funcao = $_POST['funcao'];

    $stmt = $conn->prepare("INSERT INTO revenda
        (matricula, nome, celular, cpf, datanasc, endereco, numero_casa, cep, uf, funcao) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "ssssssssss",
    $matricula,
    $nome,
    $celular,
    $cpf,
    $datanasc,
    $endereco,
    $numero_casa,
    $cep,
    $uf,
    $funcao
);

    if ($stmt->execute()) {
        $cadastro_sucesso = true;
        echo "<script>alert('Revenda : " . $nome . " cadastrado com sucesso');</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Registrar Funcionário</title>

<style>
    /* --- CONFIGURAÇÃO PARA O RODAPÉ FICAR COLADO EMBAIXO --- */
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }

    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh; 
        font-family: Arial, sans-serif;
        background: #f4f4f4;
    }

    .conteudo {
        flex: 1; /* empurra o rodapé */
    }

    /* --- ESTILOS DO SEU FORMULÁRIO --- */
    .login-container {
        background: #fff;
        padding: 20px 25px;
        max-width: 900px;
        margin: 20px auto;
        border-radius: 8px;
        box-shadow: 0 0 12px rgba(0,0,0,0.1);
    }

    h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #225B0B;
    }

    form {
        display: flex;
        flex-wrap: wrap;
        gap: 15px 20px;
        justify-content: space-between;
    }

    .form-group {
        flex: 1 1 30%;
        display: flex;
        flex-direction: column;
    }

    label {
        margin-bottom: 6px;
        font-weight: bold;
        color: #225B0B;
    }

    input[type="text"],
    input[type="date"],
    input[type="number"] {
        padding: 8px 10px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 4px;
        transition: 0.3s;
    }

    input:focus {
        border-color: #225B0B;
        outline: none;
    }

    .full-width {
        flex: 1 1 100%;
        text-align: center;
        margin-top: 20px;
    }

    input[type="submit"] {
        background-color: #A7D129;
        border: none;
        padding: 12px 25px;
        color: #fff;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        border-radius: 6px;
        transition: 0.3s;
    }

    input[type="submit"]:hover {
        background-color: #225B0B;
    }

    p a {
        color: #225B0B;
        text-decoration: none;
    }

    p a:hover {
        text-decoration: underline;
    }

    @media(max-width: 720px) {
        .form-group {
            flex: 1 1 45%;
        }
    }

    @media(max-width: 480px) {
        .form-group {
            flex: 1 1 100%;
        }
    }
</style>
</head>

<body>

<?php include '../base/cabecalho.php'; ?>

<div class="conteudo">

<br>

<div class="login-container">
    <h2>Registrar Revenda</h2>
    <form action="" method="POST">
        
        <div class="form-group">
            <label for="matricula_filial">Matrícula</label>
            <input type="text" id="matricula" name="matricula" required />
        </div>

        <div class="form-group">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" required />
        </div>

        <div class="form-group">
            <label for="nome">Celular</label>
            <input type="text" id="celular" name="celular" required />
        </div>

        <div class="form-group">
            <label for="cpf">CPF (somente números)</label>
            <input type="text" id="cpf" name="cpf" maxlength="11" pattern="\d{11}" required />
        </div>

        <div class="form-group">
            <label for="data_nascimento">Data de Nascimento</label>
            <input type="date" id="datanasc" name="datanasc" required />
        </div>

        <div class="form-group">
            <label for="endereco">Endereço</label>
            <input type="text" id="endereco" name="endereco" required />
        </div>

        <div class="form-group">
            <label for="numero_casa">Número (residencial)</label>
            <input type="text" id="numero_casa" name="numero_casa" required />
        </div>

        <div class="form-group">
            <label for="cep">CEP (somente números)</label>
            <input type="text" id="cep" name="cep" maxlength="8" pattern="\d{8}" required />
        </div>

        <div class="form-group">
            <label for="uf">UF</label>
            <input type="text" id="uf" name="uf" maxlength="2" pattern="[A-Za-z]{2}" required />
        </div>

        <div class="form-group">
            <label for="funcao">Função</label>
            <input type="text" id="funcao" name="funcao" required />
        </div>

        <div class="full-width">
            <input type="submit" name="submit" value="Registrar" />
        </div>

    </form>

    <p style="margin-top: 15px; text-align:center;">
        <a href="../vendedores/vendedores.php">← Voltar para a página inicial</a>
    </p>
</div>

</div> <!-- /conteudo -->

<?php include '../base/rodape.php'; ?>



<?php if (isset($_SESSION['nome_usuario']) && isset($_SESSION['funcao_usuario'])): ?>
    <div class="usuario-logado">
      <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>
    </div>
<?php endif; ?>

</body>
</html>
