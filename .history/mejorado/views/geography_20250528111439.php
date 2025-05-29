<?php
// geography.php - Interfaz de datos geográficos
require_once '../config/database.php';
require_once '../controllers/StatsController.php';

$statsController = new StatsController($mysqli);

// Obtener datos geográficos
$departmentStats = $statsController->getDepartmentStats();
$topProvinces = $statsController->getTopProvinces(10);
$topDistricts = $statsController->getTopDistricts(15);
$totalRecords = $statsController->getTotalRecords();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geografía - Dashboard de Salud</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            color: white;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stats-card {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 24px;
            color: white;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
        }

        .card-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .card-subtitle {
            color: #718096;
            font-size: 0.9rem;
        }

        .chart-container {
            height: 350px;
            margin-top: 20px;
        }

        .data-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .data-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .data-item:hover {
            background: rgba(102, 126, 234, 0.05);
            padding-left: 10px;
            border-radius: 8px;
        }

        .data-item:last-child {
            border-bottom: none;
        }

        .data-label {
            font-weight: 500;
            color: #4a5568;
            flex-grow: 1;
        }

        .data-value {
            font-weight: 600;
            color: #2d3748;
            margin-right: 15px;
        }

        .data-percentage {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            min-width: 60px;
            text-align: center;
        }

        .large-chart {
            grid-column: 1 / -1;
        }

        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .summary-item {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
        }

        .summary-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .summary-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <a href="dashboard.php" class="back-btn">← Volver al Dashboard</a>
    
    <div class="container">
        <div class="header">
            <h1>🌍 Análisis Geográfico</h1>
            <p>Distribución territorial de datos de salud</p>
        </div>

        <!-- Resumen general -->
        <div class="summary-stats">
            <div class="summary-item">
                <div class="summary-number"><?php echo number_format($totalRecords); ?></div>
                <div class="summary-label">Total de Registros</div>
            </div>
            <div class="summary-item">
                <div class="summary-number"><?php echo $departmentStats->num_rows; ?></div>
                <div class="summary-label">Departamentos</div>
            </div>
            <div class="summary-item">
                <div class="summary-number"><?php echo $topProvinces->num_rows; ?></div>
                <div class="summary-label">Provincias Top</div>
            </div>
            <div class="summary-item">
                <div class="summary-number"><?php echo $topDistricts->num_rows; ?></div>
                <div class="summary-label">Distritos Top</div>
            </div>
        </div>

        <div class="stats-grid">
            <!-- Departamentos -->
            <div class="stats-card">
                <div class="card-header">
                    <div class="card-icon">🗺️</div>
                    <div>
                        <div class="card-title">Departamentos</div>
                        <div class="card-subtitle">Distribución por departamento</div>
                    </div>
                </div>
                <div class="data-list">
                    <?php 
                    $departmentData = [];
                    while ($row = $departmentStats->fetch_assoc()): 
                        $departmentData[] = $row;
                    ?>
                        <div class="data-item">
                            <span class="data-label"><?php echo htmlspecialchars($row['departamento']); ?></span>
                            <span class="data-value"><?php echo number_format($row['cantidad']); ?></span>
                            <span class="data-percentage"><?php echo $row['porcentaje']; ?>%</span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Top Provincias -->
            <div class="stats-card">
                <div class="card-header">
                    <div class="card-icon">🏘️</div>
                    <div>
                        <div class="card-title">Top Provincias</div>
                        <div class="card-subtitle">10 provincias con más casos</div>
                    </div>
                </div>
                <div class="data-list">
                    <?php 
                    $provinceData = [];
                    while ($row = $topProvinces->fetch_assoc()): 
                        $provinceData[] = $row;
                    ?>
                        <div class="data-item">
                            <span class="data-label"><?php echo htmlspecialchars($row['provincia']); ?></span>
                            <span class="data-value"><?php echo number_format($row['cantidad']); ?></span>
                            <span class="data-percentage"><?php echo $row['porcentaje']; ?>%</span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Gráfico de Departamentos -->
            <div class="stats-card large-chart">
                <div class="card-header">
                    <div class="card-icon">📊</div>
                    <div>
                        <div class="card-title">Distribución por Departamentos</div>
                        <div class="card-subtitle">Gráfico comparativo</div>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="departmentChart"></canvas>
                </div>
            </div>

            <!-- Top Distritos -->
            <div class="stats-card large-chart">
                <div class="card-header">
                    <div class="card-icon">🏙️</div>
                    <div>
                        <div class="card-title">Top 15 Distritos</div>
                        <div class="card-subtitle">Distritos con mayor concentración</div>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="districtChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Configuración base de gráficos
        const chartConfig = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        };

        // Datos de departamentos desde PHP
        const departmentData = <?php echo json_encode($departmentData); ?>;
        const provinceData = <?php echo json_encode($provinceData); ?>;

        // Gráfico de departamentos
        const departmentCtx = document.getElementById('departmentChart').getContext('2d');
        new Chart(departmentCtx, {
            type: 'bar',
            data: {
                labels: departmentData.map(item => item.departamento),
                datasets: [{
                    label: 'Casos por Departamento',
                    data: departmentData.map(item => parseInt(item.cantidad)),
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                ...chartConfig,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de Casos'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Departamentos'
                        }
                    }
                }
            }
        });

        // Obtener datos de distritos
        <?php 
        $districtLabels = [];
        $districtValues = [];
        while ($row = $topDistricts->fetch_assoc()): 
            $districtLabels[] = $row['distrito'];
            $districtValues[] = (int)$row['cantidad'];
        endwhile; 
        ?>

        // Gráfico de distritos
      const districtCtx = document.getElementById('districtChart').getContext('2d');
new Chart(districtCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($districtLabels); ?>,
        datasets: [{
            label: 'Casos por Distrito',
            data: <?php echo json_encode($districtValues); ?>,
            backgroundColor: 'rgba(255, 107, 107, 0.8)',
            borderColor: 'rgba(255, 107, 107, 1)',
            borderWidth: 1
        }]
    },
    options: {
        indexAxis: 'y',
        scales: {
            x: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Número de Casos'
                }
            }
        }
    }
});

    </script>
</body>
</html>