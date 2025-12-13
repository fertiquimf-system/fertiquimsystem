<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header('Location: ../pglogin/pglogin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Inserir Nota Fiscal</title>
  <link rel="stylesheet" href="../css/estilo.css">
  <style>
    .form-section {
      background: #fff;
      padding: 20px;
      margin-bottom: 30px;
      border-radius: 8px;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }
    .form-section h2 { margin-top: 0; color: #4e4e4e; }
    .form-row { display: flex; flex-wrap: wrap; gap: 20px; }
    .form-group { flex: 1; min-width: 180px; }
    .form-group label { display: block; font-weight: bold; margin-bottom: 4px; }
    .form-group input, .form-group select {
      width: 100%; padding: 6px 8px; border-radius: 5px;
      border: 1px solid #ccc; font-size: 14px;
    }
    #fertilizantes .fertilizante {
      display: flex; flex-wrap: wrap; gap: 10px;
      border: 1px solid #ddd; padding: 10px; border-radius: 5px;
      margin-bottom: 10px; align-items: flex-end;
    }
    .fertilizante .form-group { flex: 1; min-width: 150px; }
    .remove-btn {
      background-color: red; color: white; border: none;
      border-radius: 3px; cursor: pointer; padding: 6px 12px; height: 32px;
    }
    .add-btn, input[type="submit"] {
      background-color: #8bc34a; color: white; padding: 10px 15px;
      border: none; border-radius: 5px; cursor: pointer;
      margin-top: 10px; font-size: 14px;
    }
    @media(max-width: 600px) { .form-row { flex-direction: column; } }
  </style>
</head>
<body>
<?php include '../base/estoque.php'; ?>

  <div class="container">
    <form action="processa_nf.php" method="post">
      
      <div class="form-section">
        <h2>Dados da Nota Fiscal</h2>
        <div class="form-row">
          <div class="form-group">
            <label>Número da NF:</label>
            <input type="text" name="numero_nf" required>
          </div>
          <div class="form-group">
            <label>Nome Fantasia:</label>
            <input type="text" name="nome_fantasia" required>
          </div>
          <div class="form-group">
            <label>CNPJ:</label>
            <input type="text" name="cnpj" required>
          </div>
          <div class="form-group">
            <label>Telefone:</label>
            <input type="text" name="telefone" required>
          </div>
          <div class="form-group">
            <label>Endereço:</label>
            <input type="text" name="endereco" required>
          </div>
          <div class="form-group">
            <label>CEP:</label>
            <input type="text" name="cep" required>
          </div>
          <div class="form-group">
            <label>Responsável:</label>
            <input type="text" name="responsavel_entrega" value="<?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>" readonly>
          </div>
        </div>
      </div>

      <div class="form-section">
        <h2>Material Recebido</h2>
        <div id="fertilizantes">
          <div class="fertilizante">
            <div class="form-group">
              <label>Nome:</label>
              <input type="text" name="fertilizante_nome[]" required>
            </div>
            <div class="form-group">
              <label>Quantidade:</label>
              <input type="number" name="fertilizante_quantidade[]" step="0.01" required>
            </div>
            <div class="form-group">
              <label>Unidade:</label>
              <input type="text" name="fertilizante_unidade[]" required>
            </div>
            <div class="form-group">
              <label>Tipo:</label>
              <select name="fertilizante_tipo[]" required>
                  <option value="">Selecione</option>
                  <option value="Material de informática">Material de informática</option>
                  <option value="Ferramentas">Ferramentas</option>
                  <option value="Matéria-prima">Matéria-prima</option>
                  <option value="Material de Escritorio">Material de Escritório</option>
                  <option value="EPI">EPi's</option>
                  <option value="Saca 50kg">Sacaria</option>
                  <option value="Materiais Elétricos">Materiais elétricos</option>
                  <option value="Materiais de Consumo">Materiais de Consumo</option>
                  <option value="BigBag">BigBag</option>
              </select>
            </div>
            <button type="button" class="remove-btn" onclick="removeFertilizante(this)">Remover</button>
          </div>
        </div>
        <button type="button" class="add-btn" onclick="addFertilizante()">+ Adicionar Material</button>
      </div>

      <input type="submit" value="Salvar Nota Fiscal">
    </form>
  </div>

  <?php 
  include '../base/rodape.php';
  ?>
  <?php if (isset($_SESSION['nome_usuario']) && isset($_SESSION['funcao_usuario'])): ?>
    <div class="usuario-logado">
      <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>
    </div>
  <?php endif; ?>

  <script>
    function addFertilizante() {
      const container = document.getElementById('fertilizantes');
      const div = document.createElement('div');
      div.className = 'fertilizante';
      div.innerHTML = `
        <div class="form-group">
          <label>Nome:</label>
          <input type="text" name="fertilizante_nome[]" required>
        </div>
        <div class="form-group">
          <label>Quantidade:</label>
          <input type="number" name="fertilizante_quantidade[]" step="0.01" required>
        </div>
        <div class="form-group">
          <label>Unidade:</label>
          <input type="text" name="fertilizante_unidade[]" required>
        </div>
        <div class="form-group">
          <label>Tipo:</label>
          <select name="fertilizante_tipo[]" required>
                  <option value="">Selecione</option>
                  <option value="Material de informática">Material de informática</option>
                  <option value="Ferramentas">Ferramentas</option>
                  <option value="Matéria-prima">Matéria-prima</option>
                  <option value="Material de Escritorio">Material de Escritório</option>
                  <option value="EPI">EPi's</option>
                  <option value="Saca 50kg">Sacaria</option>
                  <option value="Materiais Elétricos">Materiais elétricos</option>
                  <option value="Materiais de Consumo">Materiais de Consumo</option>
                  <option value="BigBag">BigBag</option>
          </select>
        </div>
        <button type="button" class="remove-btn" onclick="removeFertilizante(this)">Remover</button>
      `;
      container.appendChild(div);
    }

    function removeFertilizante(btn) {
      btn.parentElement.remove();
    }
  </script>
</body>
</html>
