<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| SALVAR DOCUMENTO
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $ticket = trim($_POST['ticket_pesagem']);

    if (!empty($ticket) && isset($_POST['etiquetas']) && count($_POST['etiquetas']) > 0) {

        mysqli_begin_transaction($conn);

        try {

            $stmt = $conn->prepare("
                INSERT INTO documentos
                (ticket_pesagem)
                VALUES (?)
            ");

            $stmt->bind_param("s", $ticket);
            $stmt->execute();

            $documento_id = $conn->insert_id;

            foreach ($_POST['etiquetas'] as $etiqueta_id) {

                $etiqueta_id = (int)$etiqueta_id;

                mysqli_query(
                    $conn,
                    "INSERT INTO documento_etiquetas
                    (documento_id, etiqueta_id)
                    VALUES
                    ($documento_id, $etiqueta_id)"
                );

                mysqli_query(
                    $conn,
                    "UPDATE etiquetas
                    SET status='Utilizada'
                    WHERE id=$etiqueta_id"
                );
            }

            mysqli_commit($conn);

            header("Location: documento.php?sucesso=1");
            exit;

        } catch (Exception $e) {

            mysqli_rollback($conn);

            header("Location: documento.php?erro=1");
            exit;
        }
    }
}

/*
|--------------------------------------------------------------------------
| ETIQUETAS DISPONÍVEIS
|--------------------------------------------------------------------------
*/

$filtroCor = $_GET['cor'] ?? 'Todas';

$sqlEtiquetas = "
    SELECT *
    FROM etiquetas
    WHERE status='Disponivel'
";

if ($filtroCor == 'Verde') {
    $sqlEtiquetas .= " AND cor_etiqueta='Verde'";
}

if ($filtroCor == 'Amarela') {
    $sqlEtiquetas .= " AND cor_etiqueta='Amarela'";
}

$sqlEtiquetas .= " ORDER BY numero_etiqueta ASC";

$etiquetas = mysqli_query($conn, $sqlEtiquetas);

$total_etiquetas = mysqli_num_rows($etiquetas);

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Novo Documento</title>

<link rel="stylesheet" href="../css/estilo.css">

<style>

body{
    background:#f5f5f5 !important;
}

.container{
    max-width:1100px;
    margin:auto;
}

.card-documento{
    background:#fff;
    padding:25px;
    border-radius:12px;
    margin-bottom:20px;
    box-shadow:0 3px 12px rgba(0,0,0,.08);
}

.ticket-input,
.busca-etiqueta{
    width:100%;
    max-width:400px;
    padding:12px;
    border:1px solid #ccc;
    border-radius:8px;
    font-size:15px;
}

.info{
    font-size:16px;
    font-weight:bold;
    color:#1d6b0b;
    margin-bottom:15px;
}

.lista-etiquetas{
    border:1px solid #ddd;
    border-radius:8px;
    max-height:400px;
    overflow-y:auto;
    background:#fafafa;
}

.item-etiqueta{
    padding:12px;
    border-bottom:1px solid #eee;
    transition:.2s;
}

.item-etiqueta:hover{
    background:#f1f1f1;
}

.item-etiqueta label{
    display:flex;
    align-items:center;
    gap:10px;
    cursor:pointer;
}

.btn-salvar{
    background:#1d6b0b;
    color:#fff;
    border:none;
    padding:14px 25px;
    border-radius:8px;
    font-size:15px;
    font-weight:bold;
    cursor:pointer;
}

.btn-salvar:hover{
    opacity:.9;
}

.alerta-sucesso{
    background:#d4edda;
    color:#155724;
    padding:12px;
    border-radius:8px;
    margin-bottom:20px;
}

.alerta-erro{
    background:#f8d7da;
    color:#721c24;
    padding:12px;
    border-radius:8px;
    margin-bottom:20px;
}

.resumo{
    background:#eef8ec;
    border-left:5px solid #1d6b0b;
    padding:15px;
    border-radius:8px;
    margin-bottom:20px;
}

.resumo strong{
    color:#1d6b0b;
}

</style>

</head>

<body>

<?php include '../base/cabecalho.php'; ?>

<div class="container">


<h2 class="titulo">Novo Documento</h2>

<?php if(isset($_GET['sucesso'])): ?>
    <div class="alerta-sucesso">
        Documento salvo com sucesso.
    </div>
<?php endif; ?>

<?php if(isset($_GET['erro'])): ?>
    <div class="alerta-erro">
        Erro ao salvar documento.
    </div>
<?php endif; ?>

<div class="resumo">
    <strong>Etiquetas Disponíveis:</strong>
    <?= $total_etiquetas ?>
    &nbsp;&nbsp;|&nbsp;&nbsp;
    <strong>Selecionadas:</strong>
    <span id="contadorSelecionadas">0</span>
</div>

<div style="margin-bottom:20px;">

    <a href="documento.php">
        <button type="button">Todas</button>
    </a>

    <a href="documento.php?cor=Verde">
        <button type="button">🟢 Verdes</button>
    </a>

    <a href="documento.php?cor=Amarela">
        <button type="button">🟡 Amarelas</button>
    </a>

</div>

<form method="POST">

    <div class="card-documento">

        <label><strong>Ticket da Pesagem</strong></label>

        <br><br>

        <input
            type="text"
            name="ticket_pesagem"
            class="ticket-input"
            placeholder="Digite o número do ticket"
            required
            autocomplete="off"
        >

    </div>

    <div class="card-documento">

        <div class="info">
            Selecione as Etiquetas Utilizadas
        </div>

        <input
            type="text"
            id="filtroEtiqueta"
            class="busca-etiqueta"
            placeholder="Pesquisar etiqueta..."
        >

        <br><br>

        <div class="lista-etiquetas">

            <?php while($row = mysqli_fetch_assoc($etiquetas)): ?>

                <div class="item-etiqueta">

                    <label>

                        <input
                            type="checkbox"
                            class="checkEtiqueta"
                            name="etiquetas[]"
                            value="<?= $row['id']; ?>"
                        >

                        <?php
$iconeCor =
    $row['cor_etiqueta'] == 'Verde'
    ? '🟢'
    : '🟡';
?>

<?= $iconeCor . ' ' . htmlspecialchars($row['numero_etiqueta']); ?>

                    </label>

                </div>

            <?php endwhile; ?>

        </div>

    </div>

    <button type="submit" class="btn-salvar">
        Salvar Documento
    </button>

</form>


</div>

<?php include '../base/rodape.php'; ?>

<?php if (isset($_SESSION['nome_usuario'])): ?>

<div class="usuario-logado">
    <?= htmlspecialchars($_SESSION['nome_usuario']); ?>
</div>
<?php endif; ?>

<script>

document
.getElementById('filtroEtiqueta')
.addEventListener('keyup', function(){

    let filtro = this.value.toLowerCase();

    document
    .querySelectorAll('.item-etiqueta')
    .forEach(function(item){

        if(item.textContent.toLowerCase().includes(filtro)){
            item.style.display = '';
        }else{
            item.style.display = 'none';
        }

    });

});

function atualizarContador(){

    let total =
        document.querySelectorAll(
            '.checkEtiqueta:checked'
        ).length;

    document.getElementById(
        'contadorSelecionadas'
    ).innerText = total;
}

document
.querySelectorAll('.checkEtiqueta')
.forEach(function(item){

    item.addEventListener(
        'change',
        atualizarContador
    );

});

</script>

</body>
</html>
