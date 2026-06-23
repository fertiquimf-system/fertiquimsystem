<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| CADASTRAR ETIQUETA
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $numero = trim($_POST['numero_etiqueta']);

    if (!empty($numero)) {

        $verifica = $conn->prepare("
            SELECT id
            FROM etiquetas
            WHERE numero_etiqueta = ?
        ");

        $verifica->bind_param("s", $numero);
        $verifica->execute();
        $resultado = $verifica->get_result();

        if ($resultado->num_rows == 0) {

            $stmt = $conn->prepare("
                INSERT INTO etiquetas
                (numero_etiqueta)
                VALUES (?)
            ");

            $stmt->bind_param("s", $numero);
            $stmt->execute();
        }
    }

    header("Location: etiquetas.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| FILTRO
|--------------------------------------------------------------------------
*/

$filtro = $_GET['status'] ?? 'Todos';

$sql = "SELECT * FROM etiquetas";

if ($filtro == 'Disponivel') {
    $sql .= " WHERE status = 'Disponivel'";
}

if ($filtro == 'Utilizada') {
    $sql .= " WHERE status = 'Utilizada'";
}

$sql .= " ORDER BY id DESC";

$etiquetas = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Etiquetas</title>
    <link rel="stylesheet" href="../css/estilo.css">

    <style>

        .bloco-cadastro{
            background:#fff;
            padding:20px;
            border-radius:10px;
            margin-bottom:20px;
            box-shadow:0 2px 8px rgba(0,0,0,.1);
        }

        .bloco-cadastro form{
            display:flex;
            gap:10px;
            align-items:center;
            flex-wrap:wrap;
        }

        .bloco-cadastro input{
            padding:10px;
            width:300px;
        }

        .bloco-cadastro button{
            padding:10px 20px;
            cursor:pointer;
        }

        .filtros{
            margin-bottom:20px;
        }

        .filtros a{
            text-decoration:none;
        }

        .filtros button{
            padding:10px 15px;
            margin-right:5px;
            cursor:pointer;
        }

        .tabela-etiquetas{
            width:100%;
            border-collapse:collapse;
            background:#fff;
        }

        .tabela-etiquetas th,
        .tabela-etiquetas td{
            border:1px solid #ddd;
            padding:10px;
            text-align:center;
        }

        .tabela-etiquetas th{
            background:#f2f2f2;
        }

        .status-livre{
            color:green;
            font-weight:bold;
        }

        .status-utilizada{
            color:red;
            font-weight:bold;
        }

    </style>

</head>

<body>

<?php include '../base/cabecalho.php'; ?>

<div class="container">

    <h2 class="titulo">Cadastro de Etiquetas</h2>

    <div class="bloco-cadastro">

        <form method="POST">

            <input
                type="text"
                name="numero_etiqueta"
                placeholder="Digite o número da etiqueta"
                required
                autocomplete="off"
            >

            <button type="submit">
                Cadastrar
            </button>

        </form>

    </div>

    <div class="filtros">

        <a href="etiquetas.php">
            <button type="button">Todas</button>
        </a>

        <a href="etiquetas.php?status=Disponivel">
            <button type="button">Disponíveis</button>
        </a>

        <a href="etiquetas.php?status=Utilizada">
            <button type="button">Utilizadas</button>
        </a>

    </div>

    <table class="tabela-etiquetas">

        <thead>
            <tr>
                <th>ID</th>
                <th>Número da Etiqueta</th>
                <th>Status</th>
                <th>Data Cadastro</th>
            </tr>
        </thead>

        <tbody>

        <?php while($row = mysqli_fetch_assoc($etiquetas)): ?>

            <tr>

                <td><?= $row['id']; ?></td>

                <td><?= htmlspecialchars($row['numero_etiqueta']); ?></td>

                <td>

                    <?php if($row['status'] == 'Disponivel'): ?>

                        <span class="status-livre">
                            Disponível
                        </span>

                    <?php else: ?>

                        <span class="status-utilizada">
                            Utilizada
                        </span>

                    <?php endif; ?>

                </td>

                <td>
                    <?= date('d/m/Y H:i', strtotime($row['data_cadastro'])); ?>
                </td>

            </tr>

        <?php endwhile; ?>

        </tbody>

    </table>

</div>

<?php include '../base/rodape.php'; ?>

<?php if (isset($_SESSION['nome_usuario'])): ?>
    <div class="usuario-logado">
        <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>
    </div>
<?php endif; ?>

</body>
</html>