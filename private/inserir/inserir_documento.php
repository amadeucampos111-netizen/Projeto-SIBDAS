<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Configurações da Base de Dados
    $host = "vsgate-s1.dei.isep.ipp.pt";
    $user = "1240896";
    $pass = "campos_896";
    $dbname = "db1240896";
    $port = 10464;

    $conn = mysqli_connect($host, $user, $pass, $dbname, $port);
    if (!$conn) { die("Falha na ligação: " . mysqli_connect_error()); }

    // 1. Recolha e Higienização de todos os campos (agora todos via $_POST)
    $equipamento_id         = intval($_POST['equipamento_id']);
    $tipo_documento         = trim($_POST['tipo_documento']);
    $nome_documento         = trim($_POST['nome_documento']);
    $nome_ficheiro_caminho  = trim($_POST['nome_ficheiro_caminho']); // Captura a string do caminho do PC
    $data_documento         = trim($_POST['data_documento']);
    $data_validade          = !empty($_POST['data_validade']) ? trim($_POST['data_validade']) : null;

    // Array de Controlo para o ENUM da tabela
    $enums_validos = [
        'Manual de utilizador', 'Manual de serviço', 'Certificado de calibração', 
        'Contrato de manutenção', 'Fatura ou guia de aquisição', 
        'Declaração de conformidade', 'Relatório técnico'
    ];

    // 2. Validações de consistência de dados
    if ($equipamento_id <= 0 || empty($nome_documento) || empty($nome_ficheiro_caminho) || empty($data_documento) || !in_array($tipo_documento, $enums_validos)) {
        $_SESSION['mensagem_erro'] = "Erro: Todos os campos obrigatórios (incluindo o caminho do ficheiro) devem ser preenchidos.";
        header("Location: ../documentacao.php");
        exit;
    }

    // ==========================================
    // NOVA SECÇÃO: VALIDAÇÃO DAS DATAS
    // ==========================================
    $d_doc = null;
    $d_val = null;

    // 1. Validar data_documento (Obrigatória)
    if (!empty($data_documento)) {
        $d_doc = DateTime::createFromFormat('Y-m-d', $data_documento);
        if (!$d_doc || $d_doc->format('Y-m-d') !== $data_documento) {
            $erros[] = "A data do documento introduzida é inválida.";
        } elseif ($d_doc > new DateTime()) {
            $erros[] = "A data do documento não pode ser uma data futura.";
        }
    }

    // 2. Validar data_validade (Opcional - só valida se o utilizador preencheu)
    if ($data_validade !== null) {
        $d_val = DateTime::createFromFormat('Y-m-d', $data_validade);
        if (!$d_val || $d_val->format('Y-m-d') !== $data_validade) {
            $erros[] = "A data de validade introduzida é inválida.";
        }
    }

    // 3. Validar a relação entre ambas (se ambas forem objetos DateTime válidos)
    if ($d_doc && $d_val) {
        if ($d_val < $d_doc) {
            $erros[] = "A data de validade não pode ser anterior à data do documento.";
        }
    }
    // ==========================================

    // 3. Query SQL direcionada à estrutura exata da vossa tabela
    $sql = "INSERT INTO documentacao (tipo_documento, nome_documento, nome_ficheiro_caminho, data_documento, data_validade, equipamento_id) 
            VALUES (?, ?, ?, ?, ?, ?)";
            
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        // "sssssi" -> tipo, nome_doc, caminho_string, data_doc, data_val, eq_id
        mysqli_stmt_bind_param($stmt, "sssssi", 
            $tipo_documento, 
            $nome_documento, 
            $nome_ficheiro_caminho, 
            $data_documento, 
            $data_validade, 
            $equipamento_id
        );
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['mensagem_sucesso'] = "O caminho para o documento '" . htmlspecialchars($nome_documento) . "' foi indexado com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro técnico ao gravar na Base de Dados: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['mensagem_erro'] = "Erro interno ao preparar os parâmetros da Base de Dados.";
    }

    mysqli_close($conn);
    header("Location: ../documentacao.php");
    exit;

} else {
    header("Location: ../documentacao.php");
    exit;
}