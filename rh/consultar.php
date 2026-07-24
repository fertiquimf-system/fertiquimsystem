<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

$resultado = $conn->query("SELECT * FROM cadastro_funcionario ORDER BY nome ASC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Funcionários - Fertiquim</title>
  <link rel="stylesheet" href="../css/estilo.css">

  <style>
    body {  
      margin: 0;
      font-family: Arial, Helvetica, sans-serif;
    }

    .container {
      max-width: 1200px;
      margin: 40px auto;
      background: #fff;
      padding: 80px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
       
    }

    .titulo {
      text-align: center;
      margin-bottom: 25px;
      font-size: 26px;
      color: #225B0B;
      font-weight: bold;
    }

    .tabela-funcionarios {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0 20px;
    }

    .tabela-funcionarios thead tr th {
      padding: 12px;
      text-align: left;
      background: #e8f3d5;
      font-weight: bold;
      border-radius: 6px;
      color: #225B0B;
    }

    .linha {
      background: #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
      border-radius: 6px;
    }

    .linha td {
      padding: 14px;
    }

    .btn-expandir {
      padding: 6px 12px;
      background: #A7D129;
      border: none;
      color: #fff;
      border-radius: 5px;
      cursor: pointer;
      transition: .2s;
    }

    .btn-expandir:hover {
      background: #225B0B;
    }

    /* Detalhes expandido */
    .detalhes-funcionario {
      display: none;
      background: #f9fff1;
      padding: 20px;
      border-radius: 6px;
      box-shadow: inset 0 0 8px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }

    .detalhes-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 15px 20px;
    }

    .detalhes-funcionario label {
      font-weight: bold;
      color: #225B0B;
    }

    .detalhes-funcionario input {
      padding: 8px;
      width: 100%;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    .acoes {
      margin-top: 20px;
      display: flex;
      gap: 10px;
    }

    .btn-acao {
      background: #4CAF50;
      border: none;
      padding: 10px 18px;
      color: #fff;
      border-radius: 5px;
      cursor: pointer;
      transition: .2s;
    }

    .btn-acao:hover {
      background: #3a923f;
    }

    .btn-remover {
      background: #e74c3c !important;
    }

    /* usuário logado */
    .usuario-logado {
      position: fixed;
      bottom: 10px;
      left: 15px;
      font-size: 14px;
      color: #333;
      background: #e8f3d5;
      padding: 8px 12px;
      border-radius: 5px;
    }

    @media(max-width:900px) {
      .detalhes-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media(max-width:600px) {
      .detalhes-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>

  <script>
    function toggleDetalhes(id) {
      const block = document.getElementById('detalhes-' + id);
      block.style.display = block.style.display === 'none' ? 'block' : 'none';
    }

    function salvarEdicao(event, id) {
      event.preventDefault();
      const form = event.target;
      const data = new FormData(form);
      data.append('id', id);

      fetch('editar_funcionario.php', { method: 'POST', body: data })
      .then(r => r.text())
      .then(t => alert(t.includes('sucesso') ? 'Atualizado!' : 'Erro ao atualizar'));
    }

    function removerFuncionario(id, nome) {
      if (!confirm(`Deseja remover ${nome}?`)) return;

      fetch('remover_funcionario.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + id
      })
      .then(r => r.text())
      .then(t => {
        if (t.includes('sucesso')) {
          alert("Removido!");
          document.getElementById('linha-' + id).remove();
          document.getElementById('detalhes-' + id).remove();
        } else {
          alert("Erro ao remover");
        }
      });
    }
  </script>

</head>
<body>

<!-- ❗ MANTEVE O CABEÇALHO ANTIGO EXATAMENTE COMO PEDIU -->
<header>
  <h1>FERTIQUIM Fertilizantes</h1>
  <nav>
    <a href="../pginicial/pginicial.php">Início</a>
    <a href="../pginicial/pginicial.php">Voltar</a>
    <a href="../pglogin/pglogin.php">Sair</a>
  </nav>
</header>

<div class="container">

  <h2 class="titulo">Funcionários Cadastrados</h2>

  <table class="tabela-funcionarios">
    <thead>
      <tr>
        <th>Nome</th>
        <th>Matrícula</th>
        <th>Função</th>
        <th style="width:120px;">Ações</th>
      </tr>
    </thead>
    <tbody>

      <?php while ($f = $resultado->fetch_assoc()): ?>
      <tr class="linha" id="linha-<?= $f['id'] ?>">
        <td><?= htmlspecialchars($f['nome']) ?></td>
        <td><?= htmlspecialchars($f['matricula_funcionario']) ?></td>
        <td><?= htmlspecialchars($f['funcao']) ?></td>
        <td>
          <button class="btn-expandir" onclick="toggleDetalhes(<?= $f['id'] ?>)">Detalhes</button>
        </td>
      </tr>

      <tr>
        <td colspan="4">
          <div class="detalhes-funcionario" id="detalhes-<?= $f['id'] ?>">

            <form onsubmit="salvarEdicao(event, <?= $f['id'] ?>)">
              <div class="detalhes-grid">

    <div>
        <label>Nome</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($f['nome']) ?>">
    </div>

    <div>
        <label>Matrícula Filial</label>
        <input type="text" name="matricula_filial" value="<?= htmlspecialchars($f['matricula_filial']) ?>">
    </div>

    <div>
        <label>Matrícula Funcionário</label>
        <input type="text" name="matricula_funcionario" value="<?= htmlspecialchars($f['matricula_funcionario']) ?>">
    </div>

    <div>
        <label>CPF</label>
        <input type="text" name="cpf" value="<?= htmlspecialchars($f['cpf']) ?>">
    </div>

    <div>
        <label>Data de Nascimento</label>
        <input type="date" name="data_nascimento" value="<?= htmlspecialchars($f['data_nascimento']) ?>">
    </div>

    <div>
        <label>Endereço</label>
        <input type="text" name="endereco" value="<?= htmlspecialchars($f['endereco']) ?>">
    </div>

    <div>
        <label>Número</label>
        <input type="text" name="numero_casa" value="<?= htmlspecialchars($f['numero_casa']) ?>">
    </div>

    <div>
        <label>CEP</label>
        <input type="text" name="cep" value="<?= htmlspecialchars($f['cep']) ?>">
    </div>

    <div>
        <label>UF</label>
        <input type="text" name="uf" value="<?= htmlspecialchars($f['uf']) ?>">
    </div>

    <div>
        <label>Função</label>
        <input type="text" name="funcao" value="<?= htmlspecialchars($f['funcao']) ?>">
    </div>

    <div>
        <label>Salário</label>
        <input type="password" name="salario" placeholder="********">
    </div>

</div>

              <div class="acoes">
                <button class="btn-acao" type="submit">Salvar</button>
                <button class="btn-acao btn-remover" type="button"
                onclick="removerFuncionario(<?= $f['id'] ?>, '<?= htmlspecialchars($f['nome']) ?>')">
                  Remover
                </button>
              </div>

            </form>

          </div>
        </td>
      </tr>
      <?php endwhile; ?>

    </tbody>
  </table>

</div>

<?php include '../base/rodape.php'; ?>

<?php if (isset($_SESSION['nome_usuario'])): ?>
<div class="usuario-logado">
  <?= htmlspecialchars($_SESSION['nome_usuario']) ?>
</div>
<?php endif; ?>

</body>
</html>
