<?php
// establishments.php - Interface para establecimientos
require_once '../controllers/StatsController.php';
require_once '../config/database.php';

// Instanciamos los controladores
$StatsController = new StatsController();
$demographicsController = new StatsController(); // Cambia esto si hay un controlador distinto para demografía
?>
<!DOCTYPE html>
<html>
<head>
    <title>Análisis Demográfico - Sistema de Salud</title>
    <style>
        /* Agrego estilos básicos para mejor presentación */
        .container {
            width: 90%;
            margin: auto;
            font-family: Arial, sans-serif;
        }
        h1, h2 {
            color: #2c3e50;
        }
        .stats-grid {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #ecf0f1;
            padding: 15px;
            border-radius: 8px;
            flex: 1;
            text-align: center;
            box-shadow: 1px 1px 6px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th, td {
            padding: 8px 12px;
            border: 1px solid #bdc3c7;
            text-align: center;
        }
        th {
            background-color: #34495e;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Análisis Demográfico</h1>
        
        <!-- Estadísticas de Edad -->
        <div class="age-stats-section">
            <h2>Estadísticas de Edad</h2>
            <?php 
            $ageStats = $StatsController->getCurrentAgeStats();
            ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Edad Promedio</h3>
                    <p><?php echo htmlspecialchars($ageStats['edad_promedio']); ?> años</p>
                </div>
                <div class="stat-card">
                    <h3>Menores de Edad</h3>
                    <p><?php echo number_format($ageStats['menores_edad']); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Mayores de Edad</h3>
                    <p><?php echo number_format($ageStats['mayores_edad']); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Grupos Etarios -->
        <div class="age-groups-section">
            <h2>Distribución por Grupos Etarios</h2>
            <table>
                <tr><th>Grupo de Edad</th><th>Cantidad</th><th>Porcentaje</th><th>Hemoglobina Promedio</th></tr>
                <?php 
                $ageGroups = $demographicsController->getAgeGroups();
                while($row = $ageGroups->fetch_assoc()): 
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['grupo_edad']); ?></td>
                    <td><?php echo number_format($row['cantidad']); ?></td>
                    <td><?php echo htmlspecialchars($row['porcentaje']); ?>%</td>
                    <td><?php echo htmlspecialchars($row['hemoglobina_promedio']); ?> g/dL</td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
        
        <!-- Análisis por Sexo -->
        <div class="gender-analysis-section">
            <h2>Análisis Detallado por Sexo</h2>
            <table>
                <tr><th>Sexo</th><th>Total</th><th>%</th><th>Hb Promedio</th><th>Hb Min</th><th>Hb Max</th><th>Casos Anemia</th><th>% Anemia</th></tr>
                <?php 
                $genderAnalysis = $demographicsController->getDetailedGenderAnalysis();
                while($row = $genderAnalysis->fetch_assoc()): 
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['sexo']); ?></td>
                    <td><?php echo number_format($row['total_casos']); ?></td>
                    <td><?php echo htmlspecialchars($row['porcentaje_total']); ?>%</td>
                    <td><?php echo htmlspecialchars($row['hemoglobina_promedio']); ?></td>
                    <td><?php echo htmlspecialchars($row['hemoglobina_minima']); ?></td>
                    <td><?php echo htmlspecialchars($row['hemoglobina_maxima']); ?></td>
                    <td><?php echo number_format($row['casos_anemia']); ?></td>
                    <td><?php echo htmlspecialchars($row['porcentaje_anemia']); ?>%</td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
        
        <!-- Rangos de Edad Específicos por Sexo -->
        <div class="specific-ranges-section">
            <h2>Distribución Detallada por Edad y Sexo</h2>
            <table>
                <tr><th>Rango de Edad</th><th>Sexo</th><th>Cantidad</th><th>Hemoglobina Promedio</th></tr>
                <?php 
                $specificRanges = $demographicsController->getSpecificAgeRanges();
                while($row = $specificRanges->fetch_assoc()): 
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['rango_edad']); ?></td>
                    <td><?php echo htmlspecialchars($row['sexo']); ?></td>
                    <td><?php echo number_format($row['cantidad']); ?></td>
                    <td><?php echo htmlspecialchars($row['hemoglobina_promedio']); ?> g/dL</td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
        
        <!-- Análisis por Año de Nacimiento -->
        <div class="birth-year-section">
            <h2>Análisis por Año de Nacimiento</h2>
            <table>
                <tr><th>Año</th><th>Nacimientos</th><th>Hb Promedio</th><th>Masculino</th><th>Femenino</th></tr>
                <?php 
                $birthYears = $demographicsController->getBirthYearAnalysis();
                while($row = $birthYears->fetch_assoc()): 
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['año_nacimiento']); ?></td>
                    <td><?php echo number_format($row['nacimientos']); ?></td>
                    <td><?php echo htmlspecialchars($row['hemoglobina_promedio']); ?> g/dL</td>
                    <td><?php echo number_format($row['masculino']); ?></td>
                    <td><?php echo number_format($row['femenino']); ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
        
        <!-- Patrones por Mes de Nacimiento -->
        <div class="birth-month-section">
            <h2>Patrones por Mes de Nacimiento</h2>
            <table>
                <tr><th>Mes</th><th>Nacimientos</th><th>Hb Promedio</th><th>Casos Anemia</th></tr>
                <?php 
                $birthMonths = $demographicsController->getBirthMonthPatterns();
                while($row = $birthMonths->fetch_assoc()): 
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nombre_mes']); ?></td>
                    <td><?php echo number_format($row['nacimientos']); ?></td>
                    <td><?php echo htmlspecialchars($row['hemoglobina_promedio']); ?> g/dL</td>
                    <td><?php echo number_format($row['casos_anemia']); ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
