<?php
session_start();

$conn = mysqli_connect("vsgate-s1.dei.isep.ipp.pt", "1240896", "campos_896", "db1240896", 10464);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $equipamento_id          = intval($_POST['equipamento_id']);
    $entidade_responsavel_id = !empty($_POST['entidade_responsavel_id']) ? intval($_POST['entidade_responsavel_id']) : null;
    $data_inicio_garantia    = !empty($_POST['data_inicio_garantia']) ? trim($_POST['data_inicio_garantia']) : null;
    $data_fim_garantia       = !empty($_POST['data_fim_garantia']) ? trim($_POST['data_fim_garantia']) : null;
    $tem_contrato_manutencao = isset($_POST['tem_contrato_manutencao']) ? 1 : 0;
    $tipo_contrato           = ($tem_contrato_manutencao === 1 && !empty($_POST['tipo_contrato'])) ? trim($_POST['tipo_contrato']) : null;
    $periodicidade           = ($tem_contrato_manutencao === 1 && !empty($_POST['periodicidade'])) ? trim($_POST['periodicidade']) : null;
    $observacoes             = !empty($_POST['observacoes']) ? trim($_POST['observacoes']) : null;

    if ($equipamento_id > 0) {
        $sql = "INSERT INTO garantias_contratos (equipamento_id, data_inicio_garantia, data_fim_garantia, tem_contrato_manutencao, tipo_contrato, entidade_responsavel_id, periodicidade, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "issssiss", $equipamento_id, $data_inicio_garantia, $data_fim_garantia, $tem_contrato_manutencao, $tipo_contrato, $entidade_responsavel_id, $periodicidade, $observacoes);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['msg_sucesso'] = "Garantia/Contrato registado com sucesso!";
                header("Location: ../garantia_contratos.php");
                exit;
            } else {
                $erro = (mysqli_errno($conn) == 1062) ? "Este equipamento já possui um contrato ativo." : "Erro: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $erro = "Selecione um equipamento válido.";
    }
}