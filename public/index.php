<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedTrack</title>
    <link rel="shortcut icon" href="assets/img/hosp_icon.png" type="image/png">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <!-- Font Awesome -->
    <link rel="stylesheet" href="assets/fontawesome/1240896.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        /* Estilos customizados integrados para garantir a estética ideal */
        body {
            font-family: 'Titillium Web', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #1e7225 0%, #289224 100%);
            color: white;
            padding: 100px 0;
        }

        .section-padding {
            padding: 80px 0;
        }

        .bg-light-blue {
            background-color: #f0f4f8;
        }

        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 10px;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }

        .feature-card a {
            text-decoration: none;
            color: #2a5298;
        }

        .equip-img {
            height: 180px;
            object-fit: contain;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
        }

        footer {
            background-color: #289224;
            color: #a0a0a0;
            padding: 30px 0;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="#">
            <img src ="assets/img/hosp_icon.png" width="150" height="100">
        </a>
        
        <button class="navbar-dark navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="#">Início</a></li>
                <li class="nav-item"><a class="nav-link" href="#sobre">Sobre</a></li>
                <li class="nav-item"><a class="nav-link" href="#funcionalidades">Funcionalidades</a></li>
                <li class="nav-item"><a class="nav-link" href="#equipamentos">Equipamentos</a></li>
                <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
                <li class="nav-item"><a class="btn btn-primary btn-sm ms-lg-3 px-3 target-btn" href="login.php">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- HERO -->
<header class="hero-section text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="display-4 fw-bold mb-3">Gestão Inteligente de Equipamentos Médicos</h1>
                <p class="lead mb-4">Sistema web avançado para inventário hospitalar com rastreabilidade, relatórios e controlo total do parque tecnológico.</p>
                <a href="login.php" class="btn btn-light btn-lg px-4 fw-bold text-primary shadow">Aceder ao Sistema</a>
            </div>
        </div>
    </div>
</header>

<!-- SOBRE -->
<section class="section-padding" id="sobre">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-md-8">
                <h2 class="fw-bold mb-3">Sobre o Sistema</h2>
                <div class="h-line bg-primary mx-auto mb-4" style="width: 60px; height: 3px;"></div>
                <p class="text-secondary fs-5">
                    O MedTrack é uma solução web moderna desenvolvida para apoiar instituições de saúde na gestão rigorosa do ciclo de vida de dispositivos médicos, otimizando a localização, documentação e assistência técnica.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- FUNCIONALIDADES -->
<section class="section-padding bg-light-blue" id="funcionalidades">
    <div class="container">
        <h2 class="text-center fw-bold mb-2">Funcionalidades</h2>
        <div class="h-line bg-primary mx-auto mb-5" style="width: 60px; height: 3px;"></div>

        <!-- Corrigido o espaçamento das colunas usando as classes g-4 do Bootstrap -->
        <div class="row g-4">

            <div class="col-md-4">
                <div class="card p-4 shadow-sm h-100 feature-card">
                    <div class="text-primary mb-3"><i class="fas fa-microscope fa-2x"></i></div>
                    <a href="login.php"><h5>Gestão de Equipamentos</h5></a>
                    <p class="text-secondary mb-0">Registo, edição e consulta detalhada de dispositivos médicos.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-4 shadow-sm h-100 feature-card">
                    <div class="text-primary mb-3"><i class="fas fa-map-marker-alt fa-2x"></i></div>
                    <a href="login.php"><h5>Localização</h5></a>
                    <p class="text-secondary mb-0">Organização em tempo real por serviço, sala e edifício.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-4 shadow-sm h-100 feature-card">
                    <div class="text-primary mb-3"><i class="fas fa-truck fa-2x"></i></div>
                    <a href="login.php"><h5>Fornecedores</h5></a>
                    <p class="text-secondary mb-0">Gestão de fabricantes e histórico de assistência técnica externa.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-4 shadow-sm h-100 feature-card">
                    <div class="text-primary mb-3"><i class="fas fa-file-medical fa-2x"></i></div>
                    <a href="login.php"><h5>Documentação</h5></a>
                    <p class="text-secondary mb-0">Armazenamento seguro de manuais, contratos e certificados de calibração.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-4 shadow-sm h-100 feature-card">
                    <div class="text-primary mb-3"><i class="fas fa-search fa-2x"></i></div>
                    <a href="login.php"><h5>Pesquisa Avançada</h5></a>
                    <p class="text-secondary mb-0">Filtros inteligentes para encontrar qualquer equipamento em segundos.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-4 shadow-sm h-100 feature-card">
                    <div class="text-primary mb-3"><i class="fas fa-chart-pie fa-2x"></i></div>
                    <a href="login.php"><h5>Dashboard</h5></a>
                    <p class="text-secondary mb-0">Indicadores globais e métricas do estado do parque hospitalar.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- EQUIPAMENTOS EXEMPLO -->
<section class="section-padding" id="equipamentos">
    <div class="container">
        <h2 class="text-center fw-bold mb-2">Equipamentos Suportados</h2>
        <div class="h-line bg-primary mx-auto mb-5" style="width: 60px; height: 3px;"></div>

        <div class="row text-center g-4">

            <div class="col-md-3">
                <div class="card p-3 shadow-sm h-100 border-0">
                    <img src="assets/img/monitor_sinalvital.jpg" class="card-img-top equip-img" alt="Monitor">
                    <div class="card-body px-0 pb-0">
                        <h6 class="fw-bold mb-0">Monitor de Sinais Vitais</h6>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-3 shadow-sm h-100 border-0">
                    <img src="assets/img/vent_pulmonar.jpeg" class="card-img-top equip-img" alt="Ventilador">
                    <div class="card-body px-0 pb-0">
                        <h6 class="fw-bold mb-0">Ventilador Pulmonar</h6>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-3 shadow-sm h-100 border-0">
                    <img src="assets/img/bomb_infus.jpg" class="card-img-top equip-img" alt="Bomba">
                    <div class="card-body px-0 pb-0">
                        <h6 class="fw-bold mb-0">Bomba de Infusão</h6>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-3 shadow-sm h-100 border-0">
                    <img src="assets/img/desfibrilhador.png" class="card-img-top equip-img" alt="Desfibrilhador">
                    <div class="card-body px-0 pb-0">
                        <h6 class="fw-bold mb-0">Desfibrilhador</h6>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- CONTACTO -->
<section class="section-padding bg-light-blue" id="contacto">
    <div class="container">
        <h2 class="text-center fw-bold mb-2">Contacto</h2>
        <div class="h-line bg-primary mx-auto mb-5" style="width: 60px; height: 3px;"></div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4 shadow-sm border-0">
                    <form>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Nome</label>
                            <input type="text" class="form-control" placeholder="O seu nome">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Email</label>
                            <input type="email" class="form-control" placeholder="exemplo@hospital.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Mensagem</label>
                            <textarea class="form-control" rows="4" placeholder="Como podemos ajudar?"></textarea>
                        </div>
                        <button class="btn btn-primary w-100 fw-bold shadow-sm py-2">Enviar Mensagem</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="text-center">
    <div class="container">
        <p class="mb-0">© 2026 MedTrack - Projeto SIBDAS. Todos os direitos reservados.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.1240896.js"></script>

</body>
</html>