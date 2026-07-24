<?php
require_once '../conexaohost/conexao.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['nome_usuario'])) {
    echo json_encode([
        "status" => false,
        "mensagem" => "Sessão expirada",
        "pastas" => []
    ]);
    exit;
}

$pastaPai = isset($_GET['pasta_pai']) && $_GET['pasta_pai'] !== ''
    ? intval($_GET['pasta_pai'])
    : null;

$pastas = [];

try {

    if ($pastaPai === null) {

        $sql = "
        SELECT
            p.*,
            (SELECT COUNT(*) FROM drive_pastas f WHERE f.pasta_pai = p.id) AS total_pastas,
            (SELECT COUNT(*) FROM drive_arquivos a WHERE a.pasta_id = p.id) AS total_arquivos
        FROM drive_pastas p
        WHERE p.pasta_pai IS NULL
        ORDER BY p.nome
        ";

        $resultado = $conexao->query($sql);

    } else {

        $stmt = $conexao->prepare("
        SELECT
            p.*,
            (SELECT COUNT(*) FROM drive_pastas f WHERE f.pasta_pai = p.id) AS total_pastas,
            (SELECT COUNT(*) FROM drive_arquivos a WHERE a.pasta_id = p.id) AS total_arquivos
        FROM drive_pastas p
        WHERE p.pasta_pai = ?
        ORDER BY p.nome
        ");

        $stmt->bind_param("i", $pastaPai);
        $stmt->execute();

        $resultado = $stmt->get_result();
    }

    if (!$resultado) {
        throw new Exception("Erro na query");
    }

    while ($linha = $resultado->fetch_assoc()) {

        $pastas[] = [
            "id" => $linha["id"],
            "nome" => $linha["nome"],
            "criado_por" => $linha["criado_por"],
            "data" => date("d/m/Y H:i", strtotime($linha["data_criacao"])),
            "subpastas" => $linha["total_pastas"],
            "arquivos" => $linha["total_arquivos"]
        ];
    }

    echo json_encode([
        "status" => true,
        "pastas" => $pastas
    ]);

} catch (Exception $e) {

    echo json_encode([
        "status" => false,
        "mensagem" => "Erro ao carregar pastas",
        "pastas" => []
    ]);
}