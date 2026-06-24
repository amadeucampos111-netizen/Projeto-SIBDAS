<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: ../../public/login.html?erro=restrito");
    exit;
}

$host = "vsgate-s1.dei.isep.ipp.pt";
    $user = "1240896";
    $pass = "campos_896";
    $dbname = "db1240896";
    $port = 10464;

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_documento  = $_POST['tipo_documento'] ?? '';
    $nome_documento  = trim($_POST['nome_documento'] ?? '');
    $data_documento  = $_POST['data_documento'] ?? '';
    $data_validade   = !empty($_POST['data_validade']) ? $_POST['data_validade'] : null;
    $equipamento_id  = intval($_POST['equipamento_id'] ?? 0);

    // Validação elementar de campos textuais
    if (empty($tipo_documento) || empty($nome_documento) || empty($data_documento) || $equipamento_id <= 0) {
        $_SESSION['mensagem_erro'] = "Por favor, preencha todos os campos obrigatórios.";
        header("Location: ../documentacao.php");
        exit;
    }

    // --- PROCESSAMENTO DO UPLOAD DO FICHEIRO ---
    //Verifica se o ficheiro foi realmente enviado e se o código de erro é UPLOAD_ERR_OK (indica que o upload temporário para o servidor correu bem)
    if (isset($_FILES['documento_media']) && $_FILES['documento_media']['error'] === UPLOAD_ERR_OK) {
        
        $fileTmpPath = $_FILES['documento_media']['tmp_name'];
        $fileName    = $_FILES['documento_media']['name'];
        $fileSize    = $_FILES['documento_media']['size'];
        
        // Extrair e validar a extensão do ficheiro
        //Divide o nome do ficheiro pelo ponto para extrair a extensão.
        // Converte-a para minúsculas e valida se ela pertence à "lista branca" (pdf, jpg, jpeg, png)
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        $extensoes_permitidas = ['pdf', 'jpg', 'jpeg', 'png'];
        
        if (in_array($fileExtension, $extensoes_permitidas)) {
            // Limitar o upload a 5MB por segurança hospitalar
            if ($fileSize <= 5242880) {
                
                // Criar o diretório de uploads caso ele não exista no teu servidor
                $uploadFileDir = '../../uploads/documentos/';
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                // Gerar uma Hash única baseada no tempo para o nome do ficheiro (previne duplicados)
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $dest_path = $uploadFileDir . $newFileName;

                
                //Transfere o ficheiro da pasta temporária do sistema operacional para o seu destino final e definitivo no servidor
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    
                    // Guardamos o caminho relativo ideal para ser lido no frontend em qualquer listagem
                    $caminho_bd = 'uploads/documentos/' . $newFileName;

                    // Inserir os dados na tabela de documentação
                    $sql = "INSERT INTO documentacao (tipo_documento, nome_documento, nome_ficheiro_caminho, data_documento, data_validade, equipamento_id, estado) 
                            VALUES (?, ?, ?, ?, ?, ?, 'Ativo')";
                    $stmt = $conn->prepare($sql);
                    $sucesso = $stmt->execute([
                        $tipo_documento,
                        $nome_documento,
                        $caminho_bd,
                        $data_documento,
                        $data_validade,
                        $equipamento_id
                    ]);

                    if ($sucesso) {
                        $_SESSION['mensagem_sucesso'] = "Documento carregado e registado com sucesso!";
                    } else {
                        $_SESSION['mensagem_erro'] = "Erro ao guardar as informações do registo na Base de Dados.";
                    }

                } else {
                    $_SESSION['mensagem_erro'] = "Erro de permissão ao mover o ficheiro para o diretório de destino.";
                }
            } else {
                $_SESSION['mensagem_erro'] = "O ficheiro excede o limite máximo permitido de 5MB.";
            }
        } else {
            $_SESSION['mensagem_erro'] = "Extensão inválida. Apenas são permitidos ficheiros PDF, JPG e PNG.";
        }
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao efetuar o upload do ficheiro ou nenhum ficheiro selecionado.";
    }

    header("Location: ../documentacao.php");
    exit;
}
?>