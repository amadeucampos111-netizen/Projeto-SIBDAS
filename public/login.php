<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISEP-Ginasio</title>

    <!-- Bootstrap CSS & custom CSS -->
    <link rel="stylesheet" href="assets/bootstrap/bootstrap.min.1240896.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/1240896.css">
    <!-- favicon -->
    <link rel="shortcut icon" href="assets/img/hosp_icon.png" type="image/png">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="assets/fontawesome/1240896.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;700&display=swap" rel="stylesheet">

</head>
<body>
    <!-- Bootstrap JS and custom JS -->
<script src="assets/bootstrap/bootstrap.bundle.1240896.js"></script> 

<div class="container-fluid mt5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-6 col-sm-8 col-10">
            <div class="card p-4">
                <div class="bng-navbar align-items-center justify-content-center my-4">

                <a href="index.php"><img src="assets/img/hosp_icon.png"></a>
                <a class="nav-link" href="index.php"><h4><strong>Sistema Hospitalar</strong></h4></a>

                </div>
        <div class="row">
                <div class="col">
                    <!-- Formulário -->
                     <form action="../private/index.php" method="post">
                            <div class="mb-3">
                                <!-- Utilizador -->
                                 <label for="email" class="form-label">Utilizador</label>
                                <input type="email" name="email" id="email" class="form-control">
                            </div>

                            <div class="mb-3">
                                <!-- Password -->
                                 <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>

                            <div class="mb-3 text-center">
                                <!-- Submit -->
                                 <button type="submit" class="btn btn-secondary px-4">Entrar
                                    <i class="fa-solid fa-right-to-bracket ms-2"></i>
                                 </button>
                            </div>

                            <!-- Substitui a div do erro por isto no teu ficheiro HTML/PHP de login -->
                        <?php if (isset($_GET['erro'])): ?>
                            <div class="alert alert-danger p-2 text-center">
                                <div><?php echo htmlspecialchars($_GET['erro']); ?></div>
                            </div>
                        <?php endif; ?>
                    </form>

                </div>
        </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>