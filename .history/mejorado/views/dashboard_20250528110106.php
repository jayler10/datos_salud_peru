<?php
// views/dashboard.php - Resumen general del sistema
require_once(__DIR__ . '/../config/database.php');

require_once(__DIR__ . '/../controllers/StatsController.php');
// Asumiendo que StatsController ya está instanciado como $statsController
$totalRegistros = $statsController->getTotalRegistros();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Datos de Salud Puno</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .stats-container {
            margin: 20px 0;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stats-title {
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }
        .highlight {
            color: #2c3e50;
            font-weight: bold;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        .nav-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .nav-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        .nav-card:hover {
            border-color: #3498db;
            transform: translateY(-2px);
        }
        .nav-card a {
            text-decoration: none;
            color: #2c3e50;
            font-weight: bold;
            font-size: 18px;
        }
        .nav-card p {
            color: #7f8c8d;
            margin-top: 10px;
        }
        .nav-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Dashboard - Datos de Salud Puno</h1>
        
        <!-- Resumen General -->
        <div class="stats-container">
            <h2 class="stats-title">📋 Resumen General</h2>
            <p>Total de registros: <span class="highlight"><?php echo number_format($totalRegistros); ?></span></p>
        </div>

        <!-- Menú de Navegación -->
        <div class="nav-menu">
            <div class="nav-card">
                <div class="nav-icon">👥</div>
                <a href="demografia.php">Demografía</a>
                <p>Distribución por sexo, departamento, provincias y distritos</p>
            </div>
            
            <div class="nav-card">
                <div class="nav-icon">🩺</div>
                <a href="salud.php">Salud</a>
                <p>Diagnósticos de anemia, hemoglobina, edad y pisos altitudinales</p>
            </div>
            
            <div class="nav-card">
                <div class="nav-icon">🏥</div>
                <a href="establecimientos.php">Establecimientos</a>
                <p>Top 10 establecimientos de salud con más registros</p>
            </div>
        </div>
    </div>
</body>
</html>