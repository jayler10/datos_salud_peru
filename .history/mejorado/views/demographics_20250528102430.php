<?php
// views/poblacion.php - Datos demográficos y geográficos
require_once(__DIR__ . '/../config/database.php');

require_once(__DIR__ . '/../controllers/StatsController.php');

$statsController = new StatsController($mysqli);

// Obtener datos para población
$ageStats = $statsController->getAgeStats();
$ageGroups = $statsController->getAgeGroups();
$genderStats = $statsController->getGenderStats();
$altitudeRanges = $statsController->getAltitudeRanges();
$departmentStats = $statsController->getDepartmentStats();
$topProvinces = $statsController->getTopProvinces(15);
$geographicCoverage = $statsController->getGeographicCoverage();

// Convertir resultados a arrays para JavaScript
$ageGroupsData = [];
while($row = $ageGroups->fetch_assoc()) {
    $ageGroupsData[] = $row;
}

$genderData = [];
while($row = $genderStats->fetch_assoc()) {
    $genderData[] = $row;
}

$altitudeData = [];
while($row = $altitudeRanges->fetch_assoc()) {
    $altitudeData[] = $row;
}

$departmentData = [];
while($row = $departmentStats->fetch_assoc()) {
    $departmentData[] = $row;
}

$provincesData = [];
while($row = $topProvinces->fetch_assoc()) {
    $provincesData[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Población - Sistema de Salud</title>
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
        .stat-card.demographic {
            background: linear-gradient(135deg, #2196F3 0%, #21CBF3 100%);
        }
        .stat-card.geographic {
            background: linear-gradient(135deg, #4CAF50 0%, #8BC34A 100%);
        }
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .pyramid-container {
            max-height: 600px;
            overflow-y: auto;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1>
                        <i class="fas fa-users text-primary"></i> 
                        Análisis de Población
                    </h1>
                    <a href="dashboard.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Estadísticas Demográficas -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card demographic">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0"><?php echo number_format($ageStats['edad_promedio'], 1); ?></h3>
                            <p class="mb-0">Edad Promedio</p>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-calendar fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-card demographic">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0"><?php echo $ageStats['edad_min']; ?> - <?php echo $ageStats['edad_max']; ?></h3>
                            <p class="mb-0">Rango de Edad</p>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-sort-numeric-up fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-card geographic">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0"><?php echo $geographicCoverage['total_departamentos']; ?></h3>
                            <p class="mb-0">Departamentos</p>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-map fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-card geographic">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0"><?php echo $geographicCoverage['total_provincias']; ?></h3>
                            <p class="mb-0">Provincias</p>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-map-marked-alt fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos Demográficos -->
        <div class="row mb-4">
            <!-- Pirámide Poblacional -->
            <div class="col-lg-8">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-chart-bar text-primary"></i> 
                        Pirámide Poblacional por Grupos de Edad
                    </h5>
                    <div class="pyramid-container">
                        <canvas id="populationPyramid" width="800" height="500"></canvas>
                    </div>
                </div>
            </div>

            <!-- Distribución por Sexo -->
            <div class="col-lg-4">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-venus-mars text-primary"></i> 
                        Distribución por Sexo
                    </h5>
                    <canvas id="genderChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráficos Geográficos -->
        <div class="row mb-4">
            <!-- Distribución por Altitud -->
            <div class="col-lg-6">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-mountain text-primary"></i> 
                        Distribución por Altitud (Regiones Naturales)
                    </h5>
                    <canvas id="altitudeChart" width="400" height="400"></canvas>
                </div>
            </div>

            <!-- Top Departamentos -->
            <div class="col-lg-6">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-map text-primary"></i> 
                        Distribución por Departamentos
                    </h5>
                    <canvas id="departmentChart" width="400" height="400"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Provincias -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-list-ol text-primary"></i> 
                        Top 15 Provincias con Mayor Población
                    </h5>
                    <canvas id="provincesChart" width="1200" height="400"></canvas>
                </div>
            </div>
        </div>

        <!-- Navegación -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="text-center">
                    <a href="dashboard.php" class="btn btn-primary btn-lg mx-2">
                        <i class="fas fa-chart-bar"></i> Dashboard
                    </a>
                    <a href="salud.php" class="btn btn-success btn-lg mx-2">
                        <i class="fas fa-heartbeat"></i> Indicadores de Salud
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Datos para gráficos
        const ageGroupsData = <?php echo json_encode($ageGroupsData); ?>;
        const genderData = <?php echo json_encode($genderData); ?>;
        const altitudeData = <?php echo json_encode($altitudeData); ?>;
        const departmentData = <?php echo json_encode($departmentData); ?>;
        const provincesData = <?php echo json_encode($provincesData); ?>;

        // Preparar datos para pirámide poblacional
        const ageGroups = [...new Set(ageGroupsData.map(item => item.grupo_edad))];
        const maleData = [];
        const femaleData = [];

        ageGroups.forEach(group => {
            const maleCount = ageGroupsData.find(item => item.grupo_edad === group && item.sexo === 'M')?.cantidad || 0;
            const femaleCount = ageGroupsData.find(item => item.grupo_edad === group && item.sexo === 'F')?.cantidad || 0;
            
            maleData.push(-maleCount); // Negativo para mostrar a la izquierda
            femaleData.push(femaleCount);
        });

        // Pirámide poblacional
        const ctxPyramid = document.getElementById('populationPyramid').getContext('2d');
        new Chart(ctxPyramid, {
            type: 'horizontalBar',
            data: {
                labels: ageGroups,
                datasets: [{
                    label: 'Hombres',
                    data: maleData,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }, {
                    label: 'Mujeres',
                    data: femaleData,
                    backgroundColor: 'rgba(255, 99, 132, 0.8)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return Math.abs(value).toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = Math.abs(context.parsed.x);
                                return `${context.dataset.label}: ${value.toLocaleString()}`;
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de distribución por sexo
        const ctxGender = document.getElementById('genderChart').getContext('2d');
        new Chart(ctxGender, {
            type: 'doughnut',
            data: {
                labels: genderData.map(item => item.sexo === 'M' ? 'Masculino' : 'Femenino'),
                datasets: [{
                    data: genderData.map(item => item.cantidad),
                    backgroundColor: [
                        '#36A2EB',
                        '#FF6384'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const percentage = genderData[context.dataIndex].porcentaje;
                                return `${label}: ${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de distribución por altitud
        const ctxAltitude = document.getElementById('altitudeChart').getContext('2d');
        new Chart(ctxAltitude, {
            type: 'pie',
            data: {
                labels: altitudeData.map(item => item.rango_altura),
                datasets: [{
                    data: altitudeData.map(item => item.cantidad),
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const percentage = altitudeData[context.dataIndex].porcentaje;
                                return `${label}: ${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de departamentos
        const ctxDepartment = document.getElementById('departmentChart').getContext('2d');
        new Chart(ctxDepartment, {
            type: 'doughnut',
            data: {
                labels: departmentData.slice(0, 8).map(item => item.departamento),
                datasets: [{
                    data: departmentData.slice(0, 8).map(item => item.cantidad),
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                        '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Gráfico de provincias
        const ctxProvinces = document.getElementById('provincesChart').getContext('2d');
        new Chart(ctxProvinces, {
            type: 'bar',
            data: {
                labels: provincesData.map(item => item.provincia),
                datasets: [{
                    label: 'Cantidad de Casos',
                    data: provincesData.map(item => item.cantidad),
                    backgroundColor: 'rgba(75, 192, 192, 0.8)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>