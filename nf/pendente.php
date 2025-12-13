<?php
session_start();
require_once '../conexaohost/conexao.php';

if (!isset($_SESSION['nome_usuario'])) {
    header('Location: ../pglogin/pglogin.php');
    exit;
}


$nf_result = $conn->query("SELECT * FROM nf_pendente ORDER BY data_registro DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>NF's Pendentes</title>
  <link rel="stylesheet" href="../css/estilo.css">
  <style>
    .nf-card {
      background: #fff;
      border: 1px solid #ccc;
      margin-bottom: 20px;
      padding: 15px;
      border-radius: 5px;
    }

    .nf-card h3 {
      margin: 0;
      color: #4e4e4e;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    table, th, td {
      border: 1px solid #ccc;
    }

    th, td {
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #a3d133;
    }

    .confirm-btn {
      background-color: green;
      color: white;
      padding: 8px 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  
<?php include '../base/estoque.php'; ?>

  <div class="container">
    <?php if ($nf_result && $nf_result->num_rows > 0): ?>
      <?php while($nf = $nf_result->fetch_assoc()): ?>
        <div class="nf-card">
          <h3>Número da NF: <?= htmlspecialchars($nf['numero_nf']) ?> | <?= htmlspecialchars($nf['nome_fantasia']) ?></h3>
          <p><strong>CNPJ:</strong> <?= $nf['cnpj'] ?> | <strong>Telefone:</strong> <?= $nf['telefone'] ?></p>
          <p><strong>Endereço:</strong> <?= $nf['endereco'] ?> - <?= $nf['cep'] ?></p>
          <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($nf['data_registro'])) ?></p>

          <?php
            $nf_id = (int)$nf['id'];
            $fert_result = $conn->query("SELECT * FROM fertilizantes_pendentes WHERE nf_id = $nf_id");

            if (!$fert_result) {
              echo "<p style='color:red;'>Erro ao buscar fertilizantes da NF #$nf_id: " . $conn->error . "</p>";
              continue;
            }
          ?>

          <table>
            <thead>
              <tr>
                <th>Nome</th>
                <th>Quantidade</th>
                <th>Unidade</th>
              </tr>
            </thead>
            <tbody>
              <?php while($fert = $fert_result->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($fert['nome']) ?></td>
                  <td><?= $fert['quantidade'] ?></td>
                  <td><?= $fert['unidade'] ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>

          <form action="confirmar_nf.php" method="post">
            <input type="hidden" name="nf_id" value="<?= $nf['id'] ?>">
            <button type="submit" class="confirm-btn" onclick="return confirm('Confirmar recebimento desta NF?')">Confirmar Recebimento</button>
          </form>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>Nenhuma nota fiscal pendente encontrada.</p>
    <?php endif; ?>
  </div>

    <?php if (isset($_SESSION['nome_usuario']) && isset($_SESSION['funcao_usuario'])): ?>
    <div class="usuario-logado">
      <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>
    </div>

    <?php endif; ?>
    
    <?php 
  include '../base/rodape.php';
  ?>
</body>
</html>
