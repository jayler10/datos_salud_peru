<?php
// views/dashboard.php - Resumen general del sistema
require_once '../config/database.php';
require_once '../controllers/StatsController.php';

$statsController = new StatsController($mysqli);

// Obtener datos para el dashboard
$totalRecords = $statsController->getTotalRecords();
$totalEstablishments = $statsController->getTotalEstablishments();
$totalAnemiaPercentage = $statsController->getTotalAnemiaPercentage();
$hemoglobinStats = $statsController->getHemoglobinStats();
$genderStats = $statsController->getGenderStats();
$departmentStats = $statsController->getDepartmentStats();
$geographicCoverage = $statsController->getGeographicCoverage();

// Convertir resultados a arrays para JavaScript
$genderData = [];
while($row = $genderStats->fetch_assoc()) {
    $genderData[] = $row;
}

$departmentData = [];
$count = 0;
while($row = $departmentStats->fetch_assoc() && $count < 10) {
    $departmentData[] = $row;
    $count++;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Salud</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .stat-card.success {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
        }
        .stat-card.warning {
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
        }
        .stat-card.danger {
            background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
        }
        .stat-card.info {
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
        }
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .coverage-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="text-center mb-4">
                    <i class="fas fa-chart-bar text-primary"></i> 
                    Dashboard General - Sistema de Salud
                </h1>
            </div>
        </div>

        <!-- Tarjetas de Resumen -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0"><?php echo number_format($totalRecords); ?></h3>
                            <p class="mb-0">Total de Registros</p>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-card info">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0"><?php echo number_format($totalEstablishments); ?></h3>
                            <p class="mb-0">Establecimientos</p>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-hospital fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-card success">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0"><?php echo number_format($hemoglobinStats['hb_promedio'], 2); ?> g/dL</h3>
                            <p class="mb-0">Hemoglobina Promedio</p>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-heartbeat fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-card danger">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0"><?php echo number_format($totalAnemiaPercentage, 1); ?>%</h3>
                            <p class="mb-0">Casos con Anemia</p>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cobertura Geográfica -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="chart-container">
                    <h4 class="mb-3">
                        <i class="fas fa-map-marked-alt text-primary"></i> 
                        Cobertura Geográfica
                    </h4>
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="coverage-item">
                                <h5 class="text-primary"><?php echo $geographicCoverage['total_departamentos']; ?></h5>
                                <small>Departamentos</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="coverage-item">
                                <h5 class="text-success"><?php echo $geographicCoverage['total_provincias']; ?></h5>
                                <small>Provincias</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="coverage-item">
                                <h5 class="text-warning"><?php echo $geographicCoverage['total_distritos']; ?></h5>
                                <small>Distritos</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="coverage-item">
                                <h5 class="text-info"><?php echo $geographicCoverage['total_establecimientos']; ?></h5>
                                <small>Establecimientos</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row">
            <!-- Distribución por Sexo -->
            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-venus-mars text-primary"></i> 
                        Distribución por Sexo
                    </h5>
                    <canvas id="genderChart" width="400" height="300"></canvas>
                </div>
            </div>

            <!-- Top Departamentos -->
            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-map text-primary"></i> 
                        Top 10 Departamentos
                    </h5>
                    <canvas id="departmentChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Navegación -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="text-center">
                    <a href="poblacion.php" class="btn btn-primary btn-lg mx-2">
                        <i class="fas fa-users"></i> Ver Población
                    </a>
                    <a href="salud.php" class="btn btn-success btn-lg mx-2">
                        <i class="fas fa-heartbeat"></i> Ver Salud
                    </a>
                </div>
            </div>
        </div>
    </div>

    
</body>
</html>