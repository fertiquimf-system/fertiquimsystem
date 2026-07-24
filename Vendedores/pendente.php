<?php
require_once '../conexaohost/conexao.php';
session_start();

// Filtrar status se passado
$statusFiltro = isset($_GET['status']) ? $_GET['status'] : '';

// Montar query
$sqlVendas = "SELECT * FROM vendas";
if($statusFiltro=='pendente' || $statusFiltro=='aprovada'){
    $sqlVendas .= " WHERE status = '".$statusFiltro."'";
}
$sqlVendas .= " ORDER BY data_venda DESC";
$resVendas = $conn->query($sqlVendas);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Vendas - Fertiquim</title>
<link rel="stylesheet" href="../css/estilo.css">
<style>
  /* Apenas tabela */
  table { width:100%; border-collapse:collapse; background:rgba(255,255,255,0.95); border-radius:8px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.1);}
  table th, table td { padding:12px; text-align:left; border-bottom:1px solid #ddd; }
  table th { background:#1976d2; color:white; font-weight:normal; }
  table tr:last-child td { border-bottom:none; }

  .btn { padding:6px 12px; margin:2px; border:none; border-radius:4px; cursor:pointer; color:white; font-size:14px; transition:0.2s; }
  .btn:hover { opacity:0.85; }
  .btn-expand { background:#0288d1; }
  .btn-approve { background:#388e3c; }
  .btn-canhoto { background:#f57c00; }
  .btn-remove { background:#d32f2f; }
  .btn[disabled] { background:#9e9e9e; cursor:not-allowed; }

  .item-row { display:none; background:#f9f9f9; }
  .status { padding:4px 8px; border-radius:4px; color:white; font-weight:bold; text-align:center; }
  .status-pendente { background:#fbc02d; }
  .status-aprovada { background:#388e3c; }

  #filtro-status { margin-bottom:15px; padding:6px 10px; border-radius:4px; border:1px solid #ccc; }

  @media(max-width:900px){
    table th, table td { padding:8px; font-size:13px; }
    .btn { font-size:12px; padding:4px 8px; }
  }
</style>
<script>
function toggleItens(id) {
    const rows = document.querySelectorAll('.item-of-'+id);
    rows.forEach(r => r.style.display = r.style.display === 'table-row' ? 'none' : 'table-row');
}

function aprovarVenda(id) {
    const formas = ["Crédito", "Débito", "Dinheiro", "Pix", "Cheque"];
    let opcoes = "Escolha a forma de pagamento:\n";
    formas.forEach((f, idx) => {
        opcoes += (idx+1) + " - " + f + "\n";
    });

    let escolha = prompt(opcoes);
    if(!escolha) return;

    escolha = parseInt(escolha);
    if(escolha < 1 || escolha > formas.length){
        alert("Opção inválida!");
        return;
    }

    const forma_pagamento = formas[escolha-1];

    if(confirm(`Aprovar esta venda com pagamento: ${forma_pagamento}?`)){
        fetch('aprovar_venda.php?id_venda=' + id + '&forma_pagamento=' + encodeURIComponent(forma_pagamento))
        .then(res => res.text())
        .then(res => { alert(res); location.reload(); });
    }
}

function gerarCanhoto(id) {
    window.open('gerar_canhoto.php?id_venda=' + id,'_blank');
}

function removerVenda(id) {
    if(confirm('Deseja remover esta venda e todos os itens?')){
        fetch('remover_venda.php?id_venda=' + id)
        .then(res => res.text())
        .then(res => { alert(res); location.reload(); });
    }
}

function filtrarStatus(select){
    window.location.href = '?status=' + select.value;
}
</script>
</head>
<body>
<?php include '../base/cabecalho.php'; ?>

<div class="container" style="padding:20px;">
<h1 style="text-align:center; color:#333;">Lista de Vendas</h1>

<!-- Filtro de status -->
<div style="margin-bottom:20px; text-align:right;">
  <label for="filtro-status"><strong>Filtrar por Status:</strong></label>
  <select id="filtro-status" onchange="filtrarStatus(this)">
    <option value="">Todos</option>
    <option value="pendente" <?php echo $statusFiltro=='pendente'?'selected':''; ?>>Pendente</option>
    <option value="aprovada" <?php echo $statusFiltro=='aprovada'?'selected':''; ?>>Aprovada</option>
  </select>
</div>

<table>
  <thead>
    <tr>
      <th>Nº Venda</th>
      <th>Cliente</th>
      <th>CPF/CNPJ</th>
      <th>Telefone</th>
      <th>Endereço</th>
      <th>CEP</th>
      <th>Responsável</th>
      <th>Data</th>
      <th>Status</th>
      <th>Ações</th>
    </tr>
  </thead>
  <tbody>
<?php while($v = $resVendas->fetch_assoc()): ?>
    <tr>
      <td><?php echo $v['numero_venda']; ?></td>
      <td><?php echo $v['cliente']; ?></td>
      <td><?php echo $v['cpf_cnpj']; ?></td>
      <td><?php echo $v['telefone']; ?></td>
      <td><?php echo $v['endereco']; ?></td>
      <td><?php echo $v['cep']; ?></td>
      <td><?php echo $v['responsavel']; ?></td>
      <td><?php echo date("d/m/Y H:i", strtotime($v['data_venda'])); ?></td>
      <td>
        <span class="status <?php echo $v['status']=='aprovada' ? 'status-aprovada':'status-pendente'; ?>">
          <?php echo ucfirst($v['status']); ?>
        </span>
        <?php if($v['status']=='aprovada' && !empty($v['forma_pagamento'])): ?>
          <br><small><?php echo $v['forma_pagamento']; ?></small>
        <?php endif; ?>
      </td>
      <td>
        <button class="btn btn-expand" onclick="toggleItens(<?php echo $v['id_venda']; ?>)">Itens</button>
        <button class="btn btn-approve" onclick="aprovarVenda(<?php echo $v['id_venda']; ?>)" <?php echo $v['status']=='aprovada'?'disabled':''; ?>>Aprovar</button>
        <button class="btn btn-canhoto" onclick="gerarCanhoto(<?php echo $v['id_venda']; ?>)">Canhoto</button>
        <button class="btn btn-remove" onclick="removerVenda(<?php echo $v['id_venda']; ?>)">Remover</button>
      </td>
    </tr>
    <?php
      // Buscar itens da venda
      $sqlItens = "SELECT * FROM itens_venda WHERE id_venda = ".$v['id_venda'];
      $resItens = $conn->query($sqlItens);
      while($i = $resItens->fetch_assoc()):
    ?>
    <tr class="item-row item-of-<?php echo $v['id_venda']; ?>">
      <td colspan="2"><strong>Produto:</strong> <?php echo $i['produto']; ?></td>
      <td>Qtd: <?php echo $i['quantidade']; ?></td>
      <td>Unidade: <?php echo $i['unidade']; ?></td>
      <td>Tipo: <?php echo $i['tipo']; ?></td>
      <td>Valor Unit.: <?php echo number_format($i['valor_unitario'],2); ?></td>
      <td colspan="2">Total: <?php echo number_format($i['valor_total'],2); ?></td>
      <td></td>
    </tr>
    <?php endwhile; ?>
<?php endwhile; ?>
  </tbody>
</table>
</div>

<?php include '../base/rodape.php'; ?>
</body>
</html>
