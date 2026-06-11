<?php
session_start();

// Verificar se o formulário foi submetido via método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Configurações da Base de Dados
    $host = "vsgate-s1.dei.isep.ipp.pt";
    $user = "1240896";
    $pass = "campos_896";
    $dbname = "db1240896";
    $port = 10464;

    $conn = mysqli_connect($host, $user, $pass, $dbname, $port);

    if (!$conn) {
        die("Falha na ligação à base de dados: " . mysqli_connect_error());
    }

    // 1. Recolha e higienização dos dados com base nos NAMES do seu formulário
    $nome_empresa              = trim($_POST['nome_empresa']);
    $nif                       = trim($_POST['nif']);
    $contacto_telefonico       = trim($_POST['contacto_telefonico']);
    $email                     = trim($_POST['email']);
    $morada                    = trim($_POST['morada']);
    $website                   = trim($_POST['website']);
    $pessoa_contacto           = trim($_POST['pessoa_contacto']);
    $telefone_pessoa_contacto  = trim($_POST['telefone_pessoa_contacto']);
    $observacoes               = trim($_POST['observacoes']);

    // 2. Validação dos campos obrigatórios (os que têm 'required' no seu HTML)
    if (empty($nome_empresa) || empty($nif) || empty($email)) {
        $_SESSION['mensagem_erro'] = "Os campos Nome da Empresa, NIF e Email são de preenchimento obrigatório.";
        header("Location: ../fornecedores.php");
        exit;
    }

    // 3. Query SQL atualizada para a nova estrutura de 9 colunas
    $sql = "INSERT INTO fornecedores (nome_empresa, nif, contacto_telefonico, email, morada, website, pessoa_contacto, telefone_pessoa_contacto, observacoes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // "sssssssss" indica que vamos passar exatamente 9 parâmetros do tipo string (texto)
        mysqli_stmt_bind_param($stmt, "sssssssss", 
            $nome_empresa, 
            $nif, 
            $contacto_telefonico, 
            $email, 
            $morada, 
            $website, 
            $pessoa_contacto, 
            $telefone_pessoa_contacto, 
            $observacoes
        );

        // 4. Executar e tratar a resposta da BD
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['mensagem_sucesso'] = "Fornecedor '" . htmlspecialchars($nome_empresa) . "' registado com sucesso!";
        } else {
            // Código 1062 captura tentativas de duplicar o NIF na base de dados
            if (mysqli_errno($conn) == 1062) {
                $_SESSION['mensagem_erro'] = "Erro: Já existe um fornecedor registado com o NIF " . htmlspecialchars($nif) . ".";
            } else {
                $_SESSION['mensagem_erro'] = "Erro técnico ao gravar o fornecedor: " . mysqli_stmt_error($stmt);
            }
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['mensagem_erro'] = "Erro interno ao preparar a base de dados: " . mysqli_error($conn);
    }

    mysqli_close($conn);
    
    // Redireciona de volta para a sua página de listagem/gestão
    header("Location: ../fornecedores.php");
    exit;

} else {
    // Se tentarem aceder diretamente ao script pelo URL, são redirecionados
    header("Location: ../fornecedores.php");
    exit;
}