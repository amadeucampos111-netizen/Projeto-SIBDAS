<?php
session_start();

$conn = mysqli_connect("vsgate-s1.dei.isep.ipp.pt", "1240896", "campos_896", "db1240896", 10464);

if (!$conn) { 
    die("Falha na ligação: " . mysqli_connect_error()); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $equipamento_id          = intval($_POST['equipamento_id']);
    $entidade_responsavel_id = !empty($_POST['entidade_responsavel_id']) ? intval($_POST['entidade_responsavel_id']) : null;
    $data_inicio_garantia    = !empty($_POST['data_inicio_garantia']) ? trim($_POST['data_inicio_garantia']) : null;
    $data_fim_garantia       = !empty($_POST['data_fim_garantia']) ? trim($_POST['data_fim_garantia']) : null;
    $tem_contrato_manutencao = isset($_POST['tem_contrato_manutencao']) ? 1 : 0;
    $tipo_contrato           = ($tem_contrato_manutencao === 1 && !empty($_POST['tipo_contrato'])) ? trim($_POST['tipo_contrato']) : null;
    $periodicidade           = ($tem_contrato_manutencao === 1 && !empty($_POST['periodicidade'])) ? trim($_POST['periodicidade']) : null;
    $observacoes             = !empty($_POST['observacoes']) ? trim($_POST['observacoes']) : null;

    // Array para acumular mensagens de erro
    $erros = [];

    // 1. Validação do ID do Equipamento
    if ($equipamento_id <= 0) {
        $erros[] = "Selecione um equipamento válido.";
    }

    // ==========================================
    // NOVA SECÇÃO: VALIDAÇÃO DAS DATAS (OPCIONAIS)
    // ==========================================
    $d_inicio = null;
    $d_fim = null;

    // Validar data de início (se preenchida)
    if ($data_inicio_garantia !== null) {
        $d_inicio = DateTime::createFromFormat('Y-m-d', $data_inicio_garantia);
        if (!$d_inicio || $d_inicio->format('Y-m-d') !== $data_inicio_garantia) {
            $erros[] = "A data de início da garantia introduzida é inválida.";
        }
    }

    // Validar data de fim (se preenchida)
    if ($data_fim_garantia !== null) {
        $d_fim = DateTime::createFromFormat('Y-m-d', $data_fim_garantia);
        if (!$d_fim || $d_fim->format('Y-m-d') !== $data_fim_garantia) {
            $erros[] = "A data de fim da garantia introduzida é inválida.";
        }
    }

    // Validar a relação lógica entre ambas (se ambas forem válidas)
    if ($d_inicio && $d_fim) {
        if ($d_fim < $d_inicio) {
            $erros[] = "A data de fim da garantia não pode ser anterior à data de início.";
        }
    }
    // ==========================================

    // 2. Se existirem erros (de data ou de ID), interrompe e redireciona
    if (!empty($erros)) {
        $_SESSION['msg_erro'] = implode("<br>", $erros);
        mysqli_close($conn);
        header("Location: ../garantia_contratos.php");
        exit;
    }

    // 3. Execução da Query SQL (Processo Seguro)
    $sql = "INSERT INTO garantias_contratos (equipamento_id, data_inicio_garantia, data_fim_garantia, tem_contrato_manutencao, tipo_contrato, entidade_responsavel_id, periodicidade, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "issssiss", $equipamento_id, $data_inicio_garantia, $data_fim_garantia, $tem_contrato_manutencao, $tipo_contrato, $entidade_responsavel_id, $periodicidade, $observacoes);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['msg_sucesso'] = "Garantia/Contrato registado com sucesso!";
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header("Location: ../garantia_contratos.php");
            exit;
        } else {
            // Tratamento do erro de chave duplicada (DUPLICATE KEY)
            if (mysqli_errno($conn) == 1062) {
                $_SESSION['msg_erro'] = "Este equipamento já possui um contrato ativo.";
            } else {
                $_SESSION['msg_erro'] = "Erro: " . mysqli_stmt_error($stmt);
            }
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['msg_erro'] = "Erro interno ao preparar a Base de Dados.";
    }

    mysqli_close($conn);
    header("Location: ../garantia_contratos.php");
    exit;
}