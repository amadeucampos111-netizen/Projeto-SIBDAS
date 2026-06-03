<?php
session_start();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: gestao_equip.php");
    exit;
}

$id = intval($_GET['id']);

// Ligação à Base de Dados
$host = "localhost"; $user = "root"; $pass = ""; $dbname = "medtrack_db";
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Query com INNER JOIN para trazer a localização real por extenso
$sql = "SELECT e.*, l.servico_departamento, l.sala_gabinete 
        FROM equipamentos e
        INNER JOIN localizaciones l ON e.localizacao_id = l.id 
        WHERE e.id = ?";
        
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) !== 1) {
    header("Location: listar/listar_equipamentos.php");
    exit;
}

$eq = mysqli_fetch_assoc($result);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Ficha Técnica - <?php echo htmlspecialchars($eq['codigo_interno']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Titillium Web', sans-serif; background-color: #f8f9fa; }
        .label-detalhe { font-weight: 700; color: #495057; font-size: 0.85rem; text-transform: uppercase; }
        .valor-detalhe { background-color: #ffffff; padding: 10px; border-radius: 6px; border-left: 4px solid #289224; box-shadow:  0 2px 5px rgba(0,0,0,0.02); }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <div class="card shadow-sm border-0 rounded-3 p-4">
                <!-- Cabeçalho da Ficha -->
                <div class="border-bottom pb-3 mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-success mb-1"><?php echo htmlspecialchars($eq['categoria']); ?></span>
                        <h3 class="fw-bold text-dark mb-0">
                            <i class="fa-solid fa-file-medical text-success me-2"></i>
                            <?php echo htmlspecialchars($eq['designacao']); ?>
                        </h3>
                        <small class="text-muted">Código de Inventário: <strong><?php echo htmlspecialchars($eq['codigo_interno']); ?></strong></small>
                    </div>
                    <a href="listar/listar_equipamentos.php" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Voltar à Lista</a>
                </div>

                <!-- Grelha de Informações -->
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="label-detalhe">Marca</div>
                        <div class="valor-detalhe"><?php echo htmlspecialchars($eq['marca']); ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="label-detalhe">Modelo</div>
                        <div class="valor-detalhe"><?php echo htmlspecialchars($eq['modelo']); ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="label-detalhe">Nº de Série (S/N)</div>
                        <div class="valor-detalhe"><?php echo htmlspecialchars($eq['numero_serie']); ?></div>
                    </div>

                    <div class="col-md-6">
                        <div class="label-detalhe">Fabricante Oficial</div>
                        <div class="valor-detalhe"><?php echo htmlspecialchars($eq['fabricante']); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="label-detalhe">Ano de Fabrico</div>
                        <div class="valor-detalhe"><?php echo htmlspecialchars($eq['ano_fabrico']); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="label-detalhe">Tipo de Entrada</div>
                        <div class="valor-detalhe"><?php echo htmlspecialchars($eq['tipo_entrada']); ?></div>
                    </div>

                    <div class="col-md-4">
                        <div class="label-detalhe">Data de Aquisição</div>
                        <div class="valor-detalhe"><?php echo date('d/m/Y', strtotime($eq['data_aquisicao'])); ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="label-detalhe">Custo de Aquisição</div>
                        <div class="valor-detalhe"><?php echo number_format($eq['custo_aquisicao'], 2, ',', ' '); ?> €</div>
                    </div>
                    <div class="col-md-4">
                        <div class="label-detalhe">Grau de Criticidade</div>
                        <div class="valor-detalhe"><i class="fa-solid fa-triangle-exclamation text-danger me-1"></i> <?php echo htmlspecialchars($eq['criticidade']); ?></div>
                    </div>

                    <div class="col-md-8">
                        <div class="label-detalhe">Localização Hospitalar Alocada</div>
                        <div class="valor-detalhe">
                            <i class="fa-solid fa-hospital text-muted me-1"></i> 
                            <?php echo htmlspecialchars($eq['servico_departamento'] . " — " . $eq['sala_gabinete']); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="label-detalhe">Estado Funcional Atual</div>
                        <div class="valor-detalhe fw-bold text-success"><?php echo htmlspecialchars($eq['estado_atual']); ?></div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="label-detalhe">Observações Técnicas e Histórico</div>
                        <div class="p-3 bg-light rounded border" style="min-height: 80px;">
                            <?php echo nl2br(htmlspecialchars($eq['observacoes'] ?: "Nenhuma observação técnica registada para este dispositivo.")); ?>
                        </div>
                    </div>
                </div>

            </div>
            
        </div>
    </div>
</div>

</body>
</html>