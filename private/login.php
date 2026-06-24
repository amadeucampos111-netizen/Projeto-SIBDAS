<?php
// 1. Iniciar a sessão com configurações seguras de Cookies
session_start([
    'cookie_httponly' => true,  //Operação de Segurança. Garante que o ID da sessão guardado no navegador não pode ser acedido por scripts maliciosos de JavaScript
    'cookie_secure'   => false, // Altera para TRUE se utilizares HTTPS
    'use_only_cookies' => true  //Força o PHP a usar apenas cookies para gerir o ID da sessão, rejeitando IDs passados de forma insegura via URL.
]);

// Configurar o header para responder estritamente em formato JSON
header('Content-Type: application/json; charset=utf-8');

// Se o utilizador já estiver logado, avisa o JS para redirecionar
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    echo json_encode(['sucesso' => true, 'redirecionar' => '../private/dashboard.php']);
    exit;
}

// =========================================================================
// FUNÇÃO AUXILIAR: REGISTAR LOG DE FALHAS (COM PASSWORD TENTADA)
// =========================================================================
function registar_log_falha($username_tentado, $password_tentada) {
    $ficheiro_log = __DIR__ . '/login_errors.log';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'IP_DESCONHECIDO';  //Captura o endereço IP da máquina do utilizador que tentou entrar.
    $data_hora = date('Y-m-d H:i:s'); //Regista o momento exato (Ano-Mês-Dia Horas:Minutos:Segundos) da tentativa falhada
    
    // Sanitização estrita contra Log Injection (remover quebras de linha nas strings)
    $username_limpo = str_replace(["\r", "\n", "\t"], '', $username_tentado);
    $password_limpa = str_replace(["\r", "\n", "\t"], '', $password_tentada);
    
    // Montar a linha do Log incluindo a password em texto limpo introduzida na tentativa falhada
    $mensagem_log = "[$data_hora] [LOGIN_FAILED] [IP: $ip] [User: $username_limpo] [Password Tentada: $password_limpa]\n";
    
    // Gravação segura concorrente
    file_put_contents($ficheiro_log, $mensagem_log, FILE_APPEND | LOCK_EX); //Escreve a mensagem formatada dentro do ficheiro .log
}
// =========================================================================

// 2. Configurações de Conexão à Base de Dados (PDO)
$host     = 'vsgate-s1.dei.isep.ipp.pt';
$port     = '10464';
$user     = '1240896'; 
$password = 'campos_896';
$dbname   = 'db1240896';
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //Força o PDO a disparar exceções (erros controláveis) caso uma query falhe
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //Configura o formato de resposta da BD para matrizes associativas (onde acedes aos valores pelos nomes das colunas
    PDO::ATTR_EMULATE_PREPARES   => false, //Desativa a emulação de comandos preparados, forçando o MySQL nativo a tratar a segurança das queries, o que blinda o sistema contra ataques de SQL Injection
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (\PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro temporário no servidor. Por favor, tente mais tarde.']);
    exit;
}

// 3. Processamento do Formulário via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_user = isset($_POST['username']) ? trim($_POST['username']) : '';
    $input_pass = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($input_user) && !empty($input_pass)) {
        
        $sql = "SELECT u.id, u.username, u.password_hash, 
                       (a.utilizador_id IS NOT NULL) AS eh_administrador
                FROM utilizadores u
                LEFT JOIN administradores a ON u.id = a.utilizador_id
                WHERE u.username = :username 
                LIMIT 1";

        $stmt = $pdo->prepare($sql); //Prepara a query de forma segura substituindo o valor real por um marcador temporário (:username)
        $stmt->execute(['username' => $input_user]);
        $utilizador = $stmt->fetch();

        // Verificar se as credenciais estão corretas comparando com o password_hash da BD
        if ($utilizador && password_verify($input_pass, $utilizador['password_hash'])) {
            
            // --- LOGIN BEM-SUCEDIDO ---
            session_regenerate_id(true); //Ação Antisequestro. Substitui o ID da sessão atual por um completamente novo e apaga o antigo
            
            $_SESSION['usuario_id']    = $utilizador['id'];
            $_SESSION['usuario_nome']  = $utilizador['username'];
            $_SESSION['logado']        = true;
            $_SESSION['is_admin']      = (bool)$utilizador['eh_administrador'];
            $_SESSION['ultimo_acesso'] = time();

            // Responde ao JavaScript que deu certo e para onde ele deve guiar o utilizador
            echo json_encode(['sucesso' => true, 'redirecionar' => '../private/dashboard.php']); 
            exit;

        } else {
            // --- FALHA DE LOGIN ---
            // Invoca a função passando o utilizador e a password errada digitada
            registar_log_falha($input_user, $input_pass);

            echo json_encode(['sucesso' => false, 'mensagem' => 'Utilizador ou palavra-passe incorretos.']);
            exit;
        }
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Por favor, preencha todos os campos obrigatórios.']);
        exit;
    }
}