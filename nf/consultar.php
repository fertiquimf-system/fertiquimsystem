<?php
require_once '../conexaohost/conexao.php';
session_start();

// Filtro da busca
$filtro = "";
if (!empty($_GET['busca'])) {
    $busca = $conn->real_escape_string($_GET['busca']);
    $filtro = "WHERE numero_nf LIKE '%$busca%' 
            OR data_confirmacao LIKE '%$busca%' 
            OR cnpj LIKE '%$busca%'
            OR nome_fantasia LIKE '%$busca%'";
}

// Consulta NF aceitas
$sql = "SELECT id, numero_nf, nome_fantasia, cnpj, telefone, endereco, cep, data_confirmacao, responsavel_entrega, cpf_responsavel
        FROM nf_aceitas 
        $filtro
        ORDER BY data_confirmacao DESC";
$result = $conn->query($sql);

$dados = [];
if ($result && $result->num_rows > 0) {
    while($nf = $result->fetch_assoc()) {
        $nf_id = (int)$nf['id'];
        $fertilizantes = [];
        $res_fert = $conn->query("SELECT nome_produto, quantidade, unidade, tipo FROM fertilizantes_aceitos WHERE nf_id = $nf_id");
        if ($res_fert && $res_fert->num_rows > 0) {
            while($f = $res_fert->fetch_assoc()) {
                $fertilizantes[] = $f;
            }
        }
        $nf['fertilizantes'] = $fertilizantes;
        $dados[] = $nf;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Notas Fiscais Aceitas</title>
<link rel="stylesheet" href="../css/estilo.css">
<style>
    body { font-family: Arial, sans-serif; background: #f4f6f9; }
    .container { max-width: 1200px; margin: auto; padding: 20px; }
    .search-box { margin-top: 20px; display: flex; gap: 10px; }
    .search-box input { flex: 1; padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc; }
    .search-box button { padding: 8px 16px; border: none; border-radius: 6px; background: #4CAF50; color: #fff; cursor: pointer; }
    .search-box button:hover { background: #45a049; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    th, td { border: 1px solid #eee; padding: 10px 12px; text-align: left; }
    th { background-color: #4CAF50; color: white; }
    tr:nth-child(even) { background: #f9f9f9; }
    tr:hover { background: #f1f7ff; }
    .expand-btn { background-color: #2196F3; color: white; border: none; padding: 6px 10px; cursor: pointer; border-radius: 5px; font-size: 14px; }
    .expand-btn:hover { background-color: #1976D2; }
    .detalhes-nf td { padding: 0; }
    .detalhes-conteudo {
        background: #fff;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 15px;
        margin: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        font-size: 14px;
        line-height: 1.6;
    }
</style>
<script>
function toggleExpand(id) {
    var content = document.getElementById('expand_' + id);
    if(content.style.display === 'none' || content.style.display === '') {
        content.style.display = 'table-row';
    } else {
        content.style.display = 'none';
    }
}
</script>
</head>
<body>
<?php include '../base/estoque.php'; ?>

<div class="container">
    <form method="get" class="search-box">
        <input type="text" name="busca" placeholder="🔍 Buscar por Data, Nome ou CNPJ..." value="<?php echo isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : ''; ?>">
        <button type="submit">Buscar</button>
    </form>

    <?php if (count($dados) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Número NF</th>
                    <th>Nome Fantasia</th>
                    <th>CNPJ</th>
                    <th>Telefone</th>
                    <th>Endereço</th>
                    <th>CEP</th>
                    <th>Data Confirmação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($dados as $nf): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($nf['id']); ?></td>
                        <td><?php echo htmlspecialchars($nf['numero_nf']); ?></td>
                        <td><?php echo htmlspecialchars($nf['nome_fantasia']); ?></td>
                        <td><?php echo htmlspecialchars($nf['cnpj']); ?></td>
                        <td><?php echo htmlspecialchars($nf['telefone']); ?></td>
                        <td><?php echo htmlspecialchars($nf['endereco']); ?></td>
                        <td><?php echo htmlspecialchars($nf['cep']); ?></td>
                        <td><?php echo htmlspecialchars($nf['data_confirmacao']); ?></td>
                        <td>
                            <button class="expand-btn" onclick="toggleExpand(<?php echo $nf['id']; ?>)">⬇️ Detalhes</button>
                        </td>
                    </tr>
                    <tr id="expand_<?php echo $nf['id']; ?>" class="detalhes-nf" style="display:none;">
                        <td colspan="9">
                            <div class="detalhes-conteudo">
                                <strong>📌 Conteúdo da NF:</strong><br>
                                Número: <?php echo htmlspecialchars($nf['numero_nf']); ?><br>
                                Nome Fantasia: <?php echo htmlspecialchars($nf['nome_fantasia']); ?><br>
                                CNPJ: <?php echo htmlspecialchars($nf['cnpj']); ?><br>
                                Telefone: <?php echo htmlspecialchars($nf['telefone']); ?><br>
                                Endereço: <?php echo htmlspecialchars($nf['endereco']); ?><br>
                                CEP: <?php echo htmlspecialchars($nf['cep']); ?><br>
                                Data de Confirmação: <?php echo htmlspecialchars($nf['data_confirmacao']); ?><br><br>

                                <strong>👤 Responsável pela conferência:</strong> <?php echo htmlspecialchars($nf['responsavel_entrega']); ?><br><br>

                                <strong>📦 Materiais nesta NF:</strong><br>
                                <?php if (!empty($nf['fertilizantes'])): ?>
                                    <ul>
                                    <?php foreach($nf['fertilizantes'] as $f): ?>
                                        <li>
                                            <?php echo htmlspecialchars($f['nome_produto']); ?> — 
                                            Quantidade: <?php echo htmlspecialchars($f['quantidade']); ?> 
                                            <?php echo htmlspecialchars($f['unidade']); ?> 
                                            — Tipo: <?php echo htmlspecialchars($f['tipo']); ?>
                                        </li>
                                    <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    Nenhum fertilizante encontrado.
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhuma nota fiscal aceita encontrada.</p>
    <?php endif; ?>
</div>

<footer>
    <?php if (isset($_SESSION['nome_usuario']) && isset($_SESSION['funcao_usuario'])): ?>
        <div class="usuario-logado">
            <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>
        </div>
    <?php endif; ?>
</footer>
  <?php 
  include '../base/rodape.php';
  ?>
</body>
</html>
<?php $conn->close(); ?>
