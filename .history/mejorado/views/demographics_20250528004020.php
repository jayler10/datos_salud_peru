<?php
require_once '../controllers/StatsController.php';
require_once '../config/database.php';

$StatsController = new StatsController();
$demographicsController = new StatsController();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Análisis Demográfico - Sistema de Salud</title>
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
                    <p><?php echo $ageStats['edad_promedio']; ?> años</p>
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
                    <td><?php echo $row['grupo_edad']; ?></td>
                    <td><?php echo number_format($row['cantidad']); ?></td>
                    <td><?php echo $row['porcentaje']; ?>%</td>
                    <td><?php echo $row['hemoglobina_promedio']; ?> g/dL</td>
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
                    <td><?php echo $row['sexo']; ?></td>
                    <td><?php echo number_format($row['total_casos']); ?></td>
                    <td><?php echo $row['porcentaje_total']; ?>%</td>
                    <td><?php echo $row['hemoglobina_promedio']; ?></td>
                    <td><?php echo $row['hemoglobina_minima']; ?></td>
                    <td><?php echo $row['hemoglobina_maxima']; ?></td>
                    <td><?php echo number_format($row['casos_anemia']); ?></td>
                    <td><?php echo $row['porcentaje_anemia']; ?>%</td>
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
                    <td><?php echo $row['rango_edad']; ?></td>
                    <td><?php echo $row['sexo']; ?></td>
                    <td><?php echo number_format($row['cantidad']); ?></td>
                    <td><?php echo $row['hemoglobina_promedio']; ?> g/dL</td>
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
                    <td><?php echo $row['año_nacimiento']; ?></td>
                    <td><?php echo number_format($row['nacimientos']); ?></td>
                    <td><?php echo $row['hemoglobina_promedio']; ?> g/dL</td>
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
                    <td><?php echo $row['nombre_mes']; ?></td>
                    <td><?php echo number_format($row['nacimientos']); ?></td>
                    <td><?php echo $row['hemoglobina_promedio']; ?> g/dL</td>
                    <td><?php echo number_format($row['casos_anemia']); ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
