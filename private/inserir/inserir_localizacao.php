<?php
// 1. Iniciar a sessão para podermos usar as mensagens flash (sucesso/erro)
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Verificar se o formulário foi submetido via método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 3. Configurações da Base de Dados (Padrão Laragon)
    $host = "vsgate-s1.dei.isep.ipp.pt";
    $user = "1240896";
    $pass = "campos_896";
    $dbname = "db1240896";

    $conn = mysqli_connect($host, $user, $pass, $dbname);

    if (!$conn) {
        die("Falha na ligação à base de dados: " . mysqli_connect_error());
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // 4. Recolher os dados do formulário e remover espaços brancos desnecessários
    $edificio             = trim($_POST['edificio']);
    $piso                 = trim($_POST['piso']);
    $servico_departamento = trim($_POST['servico_departamento']);
    $sala_gabinete        = trim($_POST['sala_gabinete']);

    // Validação simples para garantir que nenhum campo obrigatório vai vazio
    if (empty($edificio) || empty($piso) || empty($servico_departamento) || empty($sala_gabinete)) {
        $_SESSION['mensagem_erro'] = "Todos os campos do formulário são obrigatórios.";
        header("Location: ../localizacao.php"); // Altera para o nome da tua página
        exit;
    }

    // 5. Preparar a Query SQL utilizando Prepared Statements para total segurança
    $sql = "INSERT INTO localizaciones (edificio, piso, servico_departamento, sala_gabinete) 
            VALUES (?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // "ssss" indica que vamos passar 4 variáveis do tipo String (texto)
        mysqli_stmt_bind_param($stmt, "ssss", $edificio, $piso, $servico_departamento, $sala_gabinete);

        // 6. Executar a Query e definir a mensagem de feedback
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['mensagem_sucesso'] = "Nova localização hospitalar adicionada com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao gravar a localização: " . mysqli_stmt_error($stmt);
        }

        // Fechar o statement técnico
        mysqli_stmt_close($stmt);

    } else {
        $_SESSION['mensagem_erro'] = "Erro na preparação da base de dados: " . mysqli_error($conn);
    }

    // Fechar a ligação geral
    mysqli_close($conn);

    // 7. Redirecionar de volta para a página de gestão de localizações
    header("Location: ../localizacao.php"); // Altera para o nome da tua página
    exit;

} else {
    // Se alguém tentar aceder ao ficheiro diretamente pelo URL, é expulso para a página principal
    header("Location: ../localizacao.php"); // Altera para o nome da tua página
    exit;
}