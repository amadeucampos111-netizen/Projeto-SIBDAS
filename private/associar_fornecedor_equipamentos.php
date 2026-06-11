<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Configurações da Base de Dados
    $host = "vsgate-s1.dei.isep.ipp.pt";
    $user = "1240896";
    $pass = "campos_896";
    $dbname = "db1240896";

    $conn = mysqli_connect($host, $user, $pass, $dbname);

    if (!$conn) {
        die("Falha na ligação à base de dados: " . mysqli_connect_error());
    }

    // Recolha e conversão dos dados recebidos
    $equipamento_id  = intval($_POST['equipamento_id']);
    $fornecedor_id   = intval($_POST['fornecedor_id']);
    $tipo_fornecedor = trim($_POST['tipo_fornecedor']);

    // Validar se os dados mínimos existem
    if ($equipamento_id <= 0 || $fornecedor_id <= 0 || empty($tipo_fornecedor)) {
        $_SESSION['mensagem_erro'] = "Todos os campos de associação são obrigatórios.";
        header("Location: fornecedores.php");
        exit;
    }

    // Array de controlo para validar o ENUM e mitigar adulterações no HTML
    $enums_validos = [
        'Fabricante', 
        'Distribuidor ou fornecedor comercial', 
        'Empresa de assistência técnica', 
        'Fornecedor de consumíveis ou acessórios'
    ];

    if (!in_array($tipo_fornecedor, $enums_validos)) {
        $_SESSION['mensagem_erro'] = "O tipo de fornecedor selecionado é inválido.";
        header("Location: fornecedores.php");
        mysqli_close($conn);
        exit;
    }

    // Query SQL direcionada à tabela intermédia equipamento_fornecedor
    $sql = "INSERT INTO equipamento_fornecedor (equipamento_id, fornecedor_id, tipo_fornecedor) 
            VALUES (?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // "iis" -> integer, integer, string
        mysqli_stmt_bind_param($stmt, "iis", $equipamento_id, $fornecedor_id, $tipo_fornecedor);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['mensagem_sucesso'] = "Associação contratual/técnica registada com sucesso!";
        } else {
            // Código 1062 deteta violação da Chave Primária Composta (Associação duplicada)
            if (mysqli_errno($conn) == 1062) {
                $_SESSION['mensagem_erro'] = "Aviso: Este fornecedor já está associado a este equipamento com essa mesma função.";
            } else {
                $_SESSION['mensagem_erro'] = "Erro técnico ao associar: " . mysqli_stmt_error($stmt);
            }
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['mensagem_erro'] = "Erro de preparação na base de dados: " . mysqli_error($conn);
    }

    mysqli_close($conn);
    header("Location: fornecedores.php"); // Redireciona para a página de gestão
    exit;

} else {
    header("Location: fornecedores.php");
    exit;
}