<?php
// 1. Iniciar a sessão para podermos enviar mensagens de sucesso ou erro
session_start();

// 2. Verificar se o ficheiro foi acedido através do método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 3. Configurações da Base de Dados (Padrão do Laragon)
    $host = "vsgate-s1.dei.isep.ipp.pt";
    $user = "1240896";
    $pass = "campos_896";
    $dbname = "db1240896";
    $port = 10464;

    $conn = mysqli_connect($host, $user, $pass, $dbname, $port);

    if (!$conn) {
        die("Falha na ligação à base de dados: " . mysqli_connect_error());
    }

    // 4. Recolher e sanitizar os dados vindos do formulário (mapeados pelos atributos 'name')
    $codigo_interno   = trim($_POST['codigo_interno']);
    $designacao       = trim($_POST['designacao']);
    $categoria        = $_POST['categoria'];
    $marca            = trim($_POST['marca']);
    $modelo           = trim($_POST['modelo']);
    $numero_serie     = trim($_POST['numero_serie']);
    $fabricante       = trim($_POST['fabricante']);
    $data_aquisicao   = $_POST['data_aquisicao'];
    $ano_fabrico      = intval($_POST['ano_fabrico']);
    $custo_aquisicao  = floatval($_POST['custo_aquisicao']);
    $tipo_entrada     = $_POST['tipo_entrada'];
    $localizacao_id   = intval($_POST['localizacao_id']);
    $estado_atual     = $_POST['estado_atual'];
    $criticidade      = $_POST['criticidade'];
    $observacoes      = trim($_POST['observacoes']);

    // 5. Preparar a Query SQL utilizando placeholders (?) para segurança total
    $sql = "INSERT INTO equipamentos (
                codigo_interno, designacao, categoria, marca, modelo, 
                numero_serie, fabricante, data_aquisicao, ano_fabrico, 
                custo_aquisicao, tipo_entrada, localizacao_id, estado_atual, 
                criticidade, observacoes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        /* 
           6. Associar as variáveis aos placeholders (?)
           As letras representam os tipos de dados correspondentes:
           s = string, i = inteiro, d = double/float
        */
        mysqli_stmt_bind_param(
            $stmt, 
            "ssssssssidsisss", 
            $codigo_interno, $designacao, $categoria, $marca, $modelo,
            $numero_serie, $fabricante, $data_aquisicao, $ano_fabrico,
            $custo_aquisicao, $tipo_entrada, $localizacao_id, $estado_atual,
            $criticidade, $observacoes
        );

        // 7. Executar a Query
        if (mysqli_stmt_execute($stmt)) {
            // Sucesso! Guardamos uma mensagem na sessão
            $_SESSION['mensagem_sucesso'] = "Equipamento registado com sucesso no inventário!";
        } else {
            // Se falhar (por exemplo, se o código_interno já existir e for uma chave única)
            $_SESSION['mensagem_erro'] = "Erro ao gravar: " . mysqli_stmt_error($stmt);
        }

        // Fechar o statement
        mysqli_stmt_close($stmt);

    } else {
        $_SESSION['mensagem_erro'] = "Erro na preparação da base de dados: " . mysqli_error($conn);
    }

    // Fechar a ligação geral
    mysqli_close($conn);

    // 8. Redirecionar o utilizador de volta para a página de Equipamentos
    header("Location: ../gestao_equip.php");
    exit;

} else {
    // Se tentarem aceder a este script diretamente via URL, bloqueia e redireciona
    header("Location: ../gestao_equip.php");
    exit;
}