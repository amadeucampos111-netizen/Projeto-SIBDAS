<?php
// 1. Iniciar a sessão com configurações seguras de Cookies
session_start([
    'cookie_httponly' => true,  // Impede que scripts JavaScript acedam ao cookie de sessão (Proteção contra XSS)
    'cookie_secure'   => false, // Altera para TRUE se o teu servidor já usar HTTPS (Obrigatório em produção)
    'use_only_cookies' => true  // Força o uso exclusivo de cookies para gerir a sessão
]);

// Se o utilizador já estiver logado, redireciona diretamente para o painel principal
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header("Location: dashboard.php");
    exit;
}

// 2. Configurações de Conexão à Base de Dados (PDO)
$host     = 'vsgate-s1.dei.isep.ipp.pt';
$port     = '10464';
$user     = '1240896'; 
$password = 'campos_896';
$dbname   = 'db1240896';
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lança exceções em erros de SQL
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna os dados como array associativo
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa prepared statements nativos para evitar SQL Injection
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (\PDOException $e) {
    // Em ambiente de produção, nunca mostres o erro real ($e->getMessage()) para não expor a infraestrutura
    die("Erro temporário no servidor. Por favor, tente mais tarde.");
}

// 3. Processamento do Formulário quando enviado via POST
$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obter e limpar inputs do formulário
    $input_user = isset($_POST['username']) ? trim($_POST['username']) : '';
    $input_pass = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($input_user) && !empty($input_pass)) {
        
        // ALTERADO: Procurar o utilizador fazendo LEFT JOIN para verificar se ele consta na tabela de administradores
        $sql = "SELECT u.id, u.username, u.password_hash, 
                       (a.utilizador_id IS NOT NULL) AS eh_administrador
                FROM utilizadores u
                LEFT JOIN administradores a ON u.id = a.utilizador_id
                WHERE u.username = :username 
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $input_user]);
        $utilizador = $stmt->fetch();

        // Verificar se o utilizador existe e se a password introduzida bate certo com a hash guardada
        if ($utilizador && password_verify($input_pass, $utilizador['password_hash'])) {
            
            // --- LOGIN BEM-SUCEDIDO ---
            
            // Prevenção de Session Fixation: Regenera o ID da sessão após o login válido
            session_regenerate_id(true);
            
            // Guardar dados necessários na sessão
            $_SESSION['usuario_id']    = $utilizador['id'];
            $_SESSION['usuario_nome']  = $utilizador['username'];
            $_SESSION['logado']        = true;
            
            // ADICIONADO: Guardar o privilégio de administrador na sessão (true/false)
            $_SESSION['is_admin']      = (bool)$utilizador['eh_administrador'];
            
            $_SESSION['ultimo_acesso'] = time(); // Grava a hora atual para controlo de timeout

            // Redirecionar o utilizador para a página restrita do sistema
            header("Location: dashboard.php");
            exit;

        } else {
            // Mensagem genérica para evitar dar pistas (Segurança por Obscuridade)
            $erro = "Utilizador ou palavra-passe incorretos.";
            header("Location: ../public/login.html?erro=" . urlencode($erro));
            exit;
        }
    } else {
        $erro = "Por favor, preencha todos os campos obrigatórios.";
        header("Location: ../public/login.html?erro=" . urlencode($erro));
        exit;
    }
}
?>