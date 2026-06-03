<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "medtrack_db");

$sql = "SELECT gc.*, e.designacao AS eq_nome, e.numero_serie, f.nome_empresa AS forn_nome 
        FROM garantias_contratos gc
        INNER JOIN equipamentos e ON gc.equipamento_id = e.id
        LEFT JOIN fornecedores f ON gc.entidade_responsavel_id = f.id
        ORDER BY e.designacao ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>MedTrack | Lista de Contratos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <?php if(isset($_SESSION['msg_sucesso'])): ?> <div class="alert alert-success"><?php echo $_SESSION['msg_sucesso']; unset($_SESSION['msg_sucesso']); ?></div> <?php endif; ?>
    <?php if(isset($_SESSION['msg_erro'])): ?> <div class="alert alert-danger"><?php echo $_SESSION['msg_erro']; unset($_SESSION['msg_erro']); ?></div> <?php endif; ?>

    <div class="card p-4 shadow-sm border-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-clock-history text-success me-2"></i>Controlo de Garantias e Contratos</h5>
            <a href="garantia_contratos.php" class="btn btn-success text-white fw-semibold"><i class="fa-solid fa-plus me-1"></i> Novo Contrato</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Equipamento</th>
                        <th>Garantia Base</th>
                        <th>Contrato Manutenção</th>
                        <th>Entidade Responsável</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): 
                        $expirado = (!empty($row['data_fim_garantia']) && $row['data_fim_garantia'] < date('Y-m-d'));
                    ?>
                    <tr>
                        <td>
                            <div class="fw-bold"><?php echo htmlspecialchars($row['eq_nome']); ?></div>
                            <small class="text-muted">S/N: <?php echo htmlspecialchars($row['numero_serie']); ?></small>
                        </td>
                        <td>
                            <?php if(!empty($row['data_fim_garantia'])): ?>
                                <small>Até: <?php echo date('d/m/Y', strtotime($row['data_fim_garantia'])); ?></small>
                                <?php echo $expirado ? "<span class='badge bg-danger ms-1'>Expirada</span>" : "<span class='badge bg-success ms-1'>Ativa</span>"; ?>
                            <?php else: echo "<em>Não definida</em>"; endif; ?>
                        </td>
                        <td>
                            <?php if($row['tem_contrato_manutencao']): ?>
                                <span class="badge bg-info text-dark"><?php echo htmlspecialchars($row['tipo_contrato']); ?></span>
                                <small class="d-block text-muted">Ações: <?php echo htmlspecialchars($row['periodicidade']); ?></small>
                            <?php else: echo "<span class='text-muted'>Não possui</span>"; endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['forn_nome'] ?? 'Gestão Interna'); ?></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="editar/editar_garantia.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-warning text-dark"><i class="fa-solid fa-pen"></i></a>
                                <a href="eliminar/eliminar_garantia.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('Apagar este registo de cobertura técnica?');"><i class="fa-solid fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>