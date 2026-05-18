<?php
// 1. Iniciar a sessão para que o sistema se lembre que o utilizador fez login
session_start();

// 2. Verificar se o formulário foi realmente enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 3. Recolher e limpar os dados (evita espaços em branco extra)
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // 4. Validação básica: verificar se os campos estão vazios
    if (empty($email) || empty($password)) {
        header("Location: ../public/login.php?erro=Por favor, preencha todos os campos.");
        exit;
    }

    // 5. Simulação de base de dados (Substitui isto pela tua consulta SQL mais tarde)
    $utilizador_correto = "1240896@isep.ipp.pt";
    $password_correta = "123456"; // Em produção, usa password_verify() com hashes!

    // 6. Validar as credenciais
    if ($email === $utilizador_correto && $password === $password_correta) {
        
        // LOGIN COM SUCESSO: Guarda os dados na sessão
        $_SESSION['utilizador_logado'] = $email;
        $_SESSION['perfil'] = 'Administrador';

        // Redireciona para a página restrita do hospital
        header("Location: ../private/index.php");
        exit;

    } else {
        // ERRO: Credenciais erradas. Redireciona de volta com mensagem de erro
        header("Location: ../public/login.php?erro=Utilizador ou password incorretos.");
        exit;
    }

} else {
    // Se tentarem aceder a este ficheiro diretamente sem ser pelo formulário
    header("Location: ../public/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MedTrack</title>

    <!-- favicon   -->
     <link rel="shortcut icon" href="assets/img/hosp_icon.png" type="image/png">

     <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:ital,wght@0,300;0,700;1,400&display=swap"rel="stylesheet"> <!-- Font Awesome -->
    <!-- Fontawsome-->
    <link rel="stylesheet" href="assets/fontawesome/all.min.css">

     <!-- estilos da pag  -->
      <link rel="stylesheet" href="assets/css/admin.css">

</head>

<body>

<!-- Navbar -->

<header class="bng-navbar-menu">

    <div>
        <!-- Logo e Nome -->
        <a href="area_pessoal.html">
        <img src="assets/img/hosp_icon.png" alt="Logo do MedTrack" height="50">
        </a>
        <h3>MedTrack</h3>
    </div>

    <div>
        <button>Utilizador</button>
    </div>

</header>

<!-- Sidebar -->

    <aside class="sidebar">

    <h4>Menu</h4>

    <nav>
        <a href="views/clientes/lista.html"> <i class="fas fa-users"></i> &ensp;Clientes</a>
        <a href="views/agendamento/agendamento.html"> <i class="fas fa-calendar-alt"></i> &ensp; Agendamento de treinos</a>
        <a href="views/planos/plano_treino.html"> <i class="fas fa-dumbbell"></i> &ensp; Planos de Treino</a>
        <a href="views/equipamentos/equipamentos.html"> <i class="fas fa-cogs"></i> &ensp; Equipamentos</a>
        <a href="views/produtos-servicos/produtos_servicos.html"> <i class="fas fa-box-open"></i>&ensp;Produtos e Serviços</a>
    </nav>

</aside>

<!-- Conteúdo Principal -->

<main class="content">

    <section>
        <h2>MedTrack - Sistema Hospitalar</h2>
        <p>Escolhe uma opção no menu lateral para continuar.</p>
    </section>

</main> 

</body>

</html>