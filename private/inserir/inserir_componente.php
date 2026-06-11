<?php
// 1. Iniciar a sessão para podermos devolver mensagens de feedback
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Verificar se o formulário foi submetido via método POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // 3. Configurações da Base de Dados
    $host = "vsgate-s1.dei.isep.ipp.pt";
    $user = "1240896";
    $pass = "campos_896";
    $dbname = "db1240896";

    $conn = mysqli_connect($host, $user, $pass, $dbname);

    if (!$conn) {
        $_SESSION['mensagem_erro'] = "Falha na ligação à base de dados: " . mysqli_connect_error();
        header("Location: ../gestao_equip.php");
        exit();
    }

    // 4. Recolher e sanitizar os dados enviados pelo formulário
    // Usamos o operador de coalescência nula (??) para evitar erros caso algum campo não venha preenchido
    $equipamento_pai_id  = isset($_POST['equipamento_pai_id']) ? intval($_POST['equipamento_pai_id']) : 0;
    $designacao_componente = trim($_POST['designacao_componente'] ?? '');
    $numero_serie_componente    = trim($_POST['numero_serie_componente'] ?? '');
    $codigo_componente         = trim($_POST['codigo_componente'] ?? '');
    $observacoes     = trim($_POST['observacoes'] ?? '');

    // 5. Validação básica dos campos obrigatórios
    if ($equipamento_pai_id === 0 || empty($designacao_componente)) {
        $_SESSION['mensagem_erro'] = "Por favor, preencha todos os campos obrigatórios (Equipamento e Designação do Componente).";
        mysqli_close($conn);
        header("Location: ../gestao_equip.php");
        exit();
    }

    // 6. Preparar a Query SQL utilizando Prepared Statements (Segurança contra SQL Injection)
    $sql = "INSERT INTO componentes_associados (equipamento_pai_id, codigo_componente, designacao_componente, numero_serie_componente, observacoes) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // "issss" indica os tipos de dados: i = inteiro, s = string (texto)
        mysqli_stmt_bind_param($stmt, "issss", $equipamento_pai_id,$codigo_componente, $designacao_componente, $numero_serie_componente, $observacoes);
        
        // Executar a query
        if (mysqli_stmt_execute($stmt)) {
            // Sucesso! Define a mensagem que vai aparecer no topo da gestao_equip.php
            $_SESSION['mensagem_sucesso'] = "Componente \"<strong>" . htmlspecialchars($designacao_componente) . "</strong>\" adicionado com sucesso ao equipamento!";
        } else {
            // Erro na execução
            $_SESSION['mensagem_erro'] = "Erro ao gravar o componente na base de dados: " . mysqli_stmt_error($stmt);
        }
        
        // Fechar a declaração preparada
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['mensagem_erro'] = "Erro na preparação da consulta SQL: " . mysqli_error($conn);
    }

    // 7. Fechar a ligação à base de dados
    mysqli_close($conn);

    // 8. Redirecionar de volta para a página de gestão de equipamentos
    header("Location: ../gestao_equip.php");
    exit();

} else {
    // Se alguém tentar aceder a este ficheiro diretamente pela URL, é mandado de volta
    header("Location: ../gestao_equip.php");
    exit();
}
?>