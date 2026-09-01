<?php

require_once '../conexaohost/conexao.php';

session_start();

try {

    // Receber dados da venda
    $numero_venda = $_POST['numero_venda'] ?? null;
    $cliente      = $_POST['nome'] ?? null;
    $cpf_cnpj     = $_POST['tipo_cpf_cnpj'] ?? null;
    $telefone     = $_POST['telefone'] ?? null;
    $endereco     = $_POST['endereco'] ?? null;
    $cep          = $_POST['cep'] ?? null;
    $responsavel  = $_POST['responsavel_entrega'] ?? null;
    $matricula    = $_POST['matricula'] ?? null;


    // Iniciar transação
    $conn->begin_transaction();


    // =========================
    // INSERIR VENDA
    // =========================

    $sqlVenda = "
        INSERT INTO vendas
        (
            numero_venda,
            cliente,
            matricula,
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
        )
    ";

    $stmt = $conn->prepare($sqlVenda);

    if (!$stmt) {
        throw new Exception(
            "Erro ao preparar venda: " . $conn->error
        );
    }

    $stmt->bind_param(
        "isisssss",
        $numero_venda,
        $cliente,
        $matricula,
        $cpf_cnpj,
        $telefone,
        $endereco,
        $cep,
        $responsavel
    );

    if (!$stmt->execute()) {
        throw new Exception(
            "Erro ao inserir venda: " . $stmt->error
        );
    }

    // Recuperar ID da venda criada
    $idVenda = $stmt->insert_id;


    // =========================
    // RECEBER ITENS DA VENDA
    // =========================

    $produtos    = $_POST['produto'] ?? [];
    $quantidades = $_POST['quantidade'] ?? [];
    $unidades    = $_POST['unidade'] ?? [];
    $tipos       = $_POST['tipo'] ?? [];
    $valoresUnit = $_POST['valor_unitario'] ?? [];
    $valoresTot  = $_POST['valor_total'] ?? [];


    // =========================
    // INSERIR ITENS
    // =========================

    $sqlItem = "
        INSERT INTO itens_venda
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
        )
    ";

    $stmtItem = $conn->prepare($sqlItem);

    if (!$stmtItem) {
        throw new Exception(
            "Erro ao preparar item: " . $conn->error
        );
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


        if (!$stmtItem->execute()) {
            throw new Exception(
                "Erro ao inserir item: " . $stmtItem->error
            );
        }
    }


    // =========================
    // CONFIRMAR TRANSAÇÃO
    // =========================

    $conn->commit();


    echo "
        <script>
            alert('Venda salva com sucesso!');
            window.location.href = 'pendente.php';
        </script>
    ";


} catch (Exception $e) {

    // Desfazer tudo se ocorrer algum erro
    $conn->rollback();

    die(
        'Erro ao salvar venda: ' . $e->getMessage()
    );
}

?>