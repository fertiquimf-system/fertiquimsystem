<?php
session_start();
require_once '../conexaohost/conexao.php';

// Buscar último número de venda
$sqlUltima = "SELECT MAX(numero_venda) as ultima FROM vendas";
$resUltima = $conn->query($sqlUltima);
if($resUltima && $resUltima->num_rows > 0){
    $rowUltima = $resUltima->fetch_assoc();
    $proximaVenda = $rowUltima['ultima'] ? $rowUltima['ultima'] + 1 : 1;
} else {
    $proximaVenda = 1;
}

// Buscar clientes cadastrados
$sqlClientes = "SELECT id, nome_razao, cpf_cnpj, telefone, endereco, cep FROM clientes ORDER BY nome_razao";
$resClientes = $conn->query($sqlClientes);

// Buscar produtos do estoque (apenas Saca 50kg)
$sqlProdutos = "SELECT id, nome_produto, unidade, tipo 
                FROM deposito 
                WHERE tipo = 'toneladas/BigBag'
                ORDER BY nome_produto";
$resProdutos = $conn->query($sqlProdutos);

$produtos = [];
while($row = $resProdutos->fetch_assoc()){
  $produtos[] = $row;
}

$sqlRevendas = "SELECT id, matricula, nome FROM revenda ORDER BY nome";

$resRevendas = $conn->query($sqlRevendas);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Nova Venda</title>
  <link rel="stylesheet" href="../css/estilo.css">
  <style>
    .form-section { background:#fff; padding:20px; margin-bottom:30px; border-radius:8px; box-shadow:0 0 8px rgba(0,0,0,0.1);}
    .form-section h2 { margin-top:0; color:#4e4e4e;}
    .form-row { display:flex; flex-wrap:wrap; gap:20px;}
    .form-group { flex:1; min-width:180px;}
    .form-group label { display:block; font-weight:bold; margin-bottom:4px;}
    .form-group input,.form-group select { width:100%; padding:6px 8px; border-radius:5px; border:1px solid #ccc; font-size:14px;}
    #itens .item { display:flex; flex-wrap:wrap; gap:10px; border:1px solid #ddd; padding:10px; border-radius:5px; margin-bottom:10px; align-items:flex-end;}
    .item .form-group { flex:1; min-width:150px;}
    .remove-btn { background-color:red; color:white; border:none; border-radius:3px; cursor:pointer; padding:6px 12px; height:32px;}
    .add-btn,input[type="submit"] { background-color:#8bc34a; color:white; padding:10px 15px; border:none; border-radius:5px; cursor:pointer; margin-top:10px; font-size:14px;}
    table { width:100%; border-collapse:collapse; margin-top:20px;}
    table th, table td { border:1px solid #ccc; padding:8px; text-align:center;}
    table th { background:#f5f5f5;}
    @media(max-width:600px){.form-row{flex-direction:column;}}
  </style>
</head>
<body>
<?php include '../base/cabecalho.php'; ?>

<div class="container">
  <form action="processa_nf.php" method="post">
    
    <div class="form-section">
      <h2>Dados da Venda</h2>
      <div class="form-row">
        <div class="form-group">
          <label>Número da Venda:</label>
          <input type="text" name="numero_venda" value="<?php echo $proximaVenda; ?>" readonly>
        </div>
        <div class="form-group">
          <label>Cliente:</label>
          <select name="cliente_id" id="clienteSelect" required onchange="preencherCliente(this)">
            <option value="">Selecione</option>
            <?php while($c = $resClientes->fetch_assoc()): ?>
              <option value="<?php echo $c['id']; ?>"
                      data-cpf="<?php echo $c['cpf_cnpj']; ?>"
                      data-tel="<?php echo $c['telefone']; ?>"
                      data-endereco="<?php echo $c['endereco']; ?>"
                      data-cep="<?php echo $c['cep']; ?>">
                <?php echo $c['nome_razao']; ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

             <div class="form-group">
               <label>Revendedor:</label> 
               <select name="revendedor_id" required>
                 <option value="">Selecione</option> 
                 <?php while($r = $resRevendas->fetch_assoc()): ?> 
                  <option value="<?php echo $r['id']; ?>">
                     <?php echo $r['nome']; ?> - Mat: <?php echo $r['matricula']; ?> </option> <?php endwhile; ?> 
                    </select> 
                  </div>
        
        <div class="form-group">
          <label>CPF/CNPJ:</label>
          <input type="text" id="cpf_cnpj" readonly>
        </div>
        <div class="form-group">
          <label>Telefone:</label>
          <input type="text" id="telefone" readonly>
        </div>
        <div class="form-group">
          <label>Endereço:</label>
          <input type="text" id="endereco" readonly>
        </div>
        <div class="form-group">
          <label>CEP:</label>
          <input type="text" id="cep" readonly>
        </div>
        <div class="form-group">
          <label>Responsável:</label>
          <input type="text" name="responsavel_entrega" value="<?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>" readonly>
        </div>
      </div>

      <!-- Hidden inputs para enviar ao processa_nf.php -->
      <input type="hidden" name="nome" id="nome_cliente">
      <input type="hidden" name="tipo_cpf_cnpj" id="hidden_cpf_cnpj">
      <input type="hidden" name="telefone" id="hidden_telefone">
      <input type="hidden" name="endereco" id="hidden_endereco">
      <input type="hidden" name="cep" id="hidden_cep">
    </div>

    <div class="form-section">
      <h2>Itens da Venda</h2>
      <div id="itens">
        <div class="item">
          <div class="form-group">
            <label>Produto:</label>
            <select name="produto[]" required onchange="preencherCampos(this)">
              <option value="">Selecione</option>
              <?php foreach($produtos as $p): ?>
                <option value="<?php echo $p['id']; ?>" 
                        data-unidade="<?php echo $p['unidade']; ?>" 
                        data-tipo="<?php echo $p['tipo']; ?>">
                  <?php echo $p['nome_produto']; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Quantidade:</label>
            <input type="number" name="quantidade[]" step="0.01" required oninput="atualizarTotal(this)">
          </div>
          <div class="form-group">
            <label>Unidade:</label>
            <input type="text" name="unidade[]" readonly>
          </div>
          <div class="form-group">
            <label>Tipo:</label>
            <input type="text" name="tipo[]" readonly>
          </div>
          <div class="form-group">
            <label>Valor p/ Tonelada:</label>
            <input type="number" name="valor_unitario[]" step="0.01" required oninput="atualizarTotal(this)">
          </div>
          <div class="form-group">
            <label>Total:</label>
            <input type="number" name="valor_total[]" step="0.01" readonly>
          </div>
          <button type="button" class="remove-btn" onclick="removeItem(this)">Remover</button>
        </div>
      </div>
      <button type="button" class="add-btn" onclick="addItem()">+ Adicionar Produto</button>
    </div>

    <input type="submit" value="Salvar Venda">
  </form>
</div>

  <?php 
  include '../base/rodape.php';
  ?>
  
<script>
function addItem() {
  const container = document.getElementById('itens');
  const div = document.createElement('div');
  div.className = 'item';
  div.innerHTML = `
    <div class="form-group">
      <label>Produto:</label>
      <select name="produto[]" required onchange="preencherCampos(this)">
        <option value="">Selecione</option>
        <?php foreach($produtos as $p): ?>
          <option value="<?php echo $p['id']; ?>" 
                  data-unidade="<?php echo $p['unidade']; ?>" 
                  data-tipo="<?php echo $p['tipo']; ?>">
            <?php echo $p['nome_produto']; ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label>Quantidade:</label>
      <input type="number" name="quantidade[]" step="0.01" required oninput="atualizarTotal(this)">
    </div>
    <div class="form-group">
      <label>Unidade:</label>
      <input type="text" name="unidade[]" readonly>
    </div>
    <div class="form-group">
      <label>Tipo:</label>
      <input type="text" name="tipo[]" readonly>
    </div>
    <div class="form-group">
      <label>Valor Unitário:</label>
      <input type="number" name="valor_unitario[]" step="0.01" required oninput="atualizarTotal(this)">
    </div>
    <div class="form-group">
      <label>Total:</label>
      <input type="number" name="valor_total[]" step="0.01" readonly>
    </div>
    <button type="button" class="remove-btn" onclick="removeItem(this)">Remover</button>
  `;
  container.appendChild(div);
}

function removeItem(btn) {
  btn.parentElement.remove();
}

function preencherCampos(select) {
  const option = select.options[select.selectedIndex];
  const itemDiv = select.closest('.item');
  itemDiv.querySelector('input[name="unidade[]"]').value = option.getAttribute('data-unidade');
  itemDiv.querySelector('input[name="tipo[]"]').value = option.getAttribute('data-tipo');
  atualizarTotal(itemDiv.querySelector('input[name="quantidade[]"]'));
}

function atualizarTotal(el) {
  const itemDiv = el.closest('.item');
  const qtd = parseFloat(itemDiv.querySelector('input[name="quantidade[]"]').value) || 0;
  const valorUnit = parseFloat(itemDiv.querySelector('input[name="valor_unitario[]"]').value) || 0;
  const total = qtd * valorUnit;
  itemDiv.querySelector('input[name="valor_total[]"]').value = total.toFixed(2);
}

function preencherCliente(select){
  const opt = select.options[select.selectedIndex];
  document.getElementById('cpf_cnpj').value = opt.getAttribute('data-cpf');
  document.getElementById('telefone').value = opt.getAttribute('data-tel');
  document.getElementById('endereco').value = opt.getAttribute('data-endereco');
  document.getElementById('cep').value = opt.getAttribute('data-cep');

  // hidden para enviar ao PHP
  document.getElementById('nome_cliente').value = opt.text;
  document.getElementById('hidden_cpf_cnpj').value = opt.getAttribute('data-cpf');
  document.getElementById('hidden_telefone').value = opt.getAttribute('data-tel');
  document.getElementById('hidden_endereco').value = opt.getAttribute('data-endereco');
  document.getElementById('hidden_cep').value = opt.getAttribute('data-cep');
}
</script>
</body>
</html>
