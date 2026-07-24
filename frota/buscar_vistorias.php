<?php
require_once '../conexaohost/conexao.php';

$veiculo_id = intval($_GET['veiculo_id']);

$sql = "SELECT id, data_vistoria, responsavel_vistoria 
        FROM vistorias_veiculos
        WHERE veiculo_id = ?
        ORDER BY data_vistoria DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $veiculo_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<p>Nenhuma vistoria encontrada para este veículo.</p>";
    exit;
}
?>

<table>
<thead>
<tr>
  <th>Data da Vistoria</th>
  <th>Responsável</th>
  <th>Ações</th>
</tr>
</thead>
<tbody>

<?php while ($row = $result->fetch_assoc()) { ?>
<tr>
  <td><?= date('d/m/Y H:i', strtotime($row['data_vistoria'])) ?></td>
  <td><?= $row['responsavel_vistoria'] ?></td>
  <td>
    <a href="ver_vistoria.php?id=<?= $row['id'] ?>" target="_blank">Ver PDF</a>
  </td>
</tr>
<?php } ?>

</tbody>
</table>
