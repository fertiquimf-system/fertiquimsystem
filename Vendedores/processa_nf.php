<?php
require_once '../conexaohost/conexao.php';
session_start();

// Receber dados da venda
$numero_venda   = $_POST['numero_venda'];
$cliente        = $_POST['nome'];
$cpf_cnpj       = $_POST['tipo_cpf_cnpj'];
$telefone       = $_POST['telefone'];
$endereco       = $_POST['endereco'];
$cep            = $_POST['cep'];
$responsavel    = $_POST['responsavel_entrega'];
<<<<<<< HEAD
$matricula  = $_POST['matricula'];
=======
$revendedor_id  = $_POST['revendedor_id'];
>>>>>>> 673bbf3f7f8fc79d627488a2e2703f5f0ee8f600

// Iniciar transação
$conn->begin_transaction();

try {

    // Inserir venda
    $sqlVenda = "INSERT INTO vendas
    (
        numero_venda,
        cliente,
<<<<<<< HEAD
        matricula,
=======
        revendedor_id,
>>>>>>> 673bbf3f7f8fc79d627488a2e2703f5f0ee8f600
        cpf_cnpj,
        telefone,
        endereco,
        cep,
        responsavel,
        data_venda,
        status
    )
    VALUES
    (
        ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pendente'
    )";

    $stmt = $conn->prepare($sqlVenda);

    if (!$stmt) {
        throw new Exception("Erro ao preparar venda: " . $conn->error);
    }

    $stmt->bind_param(
        "isisssss",
        $numero_venda,
        $cliente,
        $revendedor_id,
        $cpf_cnpj,
        $telefone,
        $endereco,
        $cep,
        $responsavel
    );

    $stmt->execute();

    $idVenda = $stmt->insert_id;

    // Receber itens
    $produtos    = $_POST['produto'];
    $quantidades = $_POST['quantidade'];
    $unidades    = $_POST['unidade'];
    $tipos       = $_POST['tipo'];
    $valoresUnit = $_POST['valor_unitario'];
    $valoresTot  = $_POST['valor_total'];

    // Inserir itens
    $sqlItem = "INSERT INTO itens_venda
    (
        id_venda,
        produto,
        quantidade,
        unidade,
        tipo,
        valor_unitario,
        valor_total
    )
    VALUES
    (
        ?, ?, ?, ?, ?, ?, ?
    )";

    $stmtItem = $conn->prepare($sqlItem);

    if (!$stmtItem) {
        throw new Exception("Erro ao preparar item: " . $conn->error);
    }

    for ($i = 0; $i < count($produtos); $i++) {

        $produto   = $produtos[$i];
        $qtd       = $quantidades[$i];
        $unidade   = $unidades[$i];
        $tipo      = $tipos[$i];
        $valorUnit = $valoresUnit[$i];
        $valorTot  = $valoresTot[$i];

        $stmtItem->bind_param(
            "isdssdd",
            $idVenda,
            $produto,
            $qtd,
            $unidade,
            $tipo,
            $valorUnit,
            $valorTot
        );

        $stmtItem->execute();
    }

    // Confirmar transação
    $conn->commit();

    echo "<script>
        alert('Venda salva com sucesso!');
        window.location.href='pendente.php';
    </script>";

} catch (Exception $e) {

    $conn->rollback();

    die("Erro ao salvar venda: " . $e->getMessage());
}
?>