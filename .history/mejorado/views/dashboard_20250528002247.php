<?php
// views/dashboard.php
$summary = $controller->getDashboardSummary();
?>

// views/dashboard.php - Interfaz Panel Principal
?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel Principal - Sistema de Salud</title>
</head>
<body>
    <div class="container">
        <h1>Panel Principal</h1>
        
        <!-- Resumen General -->
        <div class="summary-section">
            <h2>Resumen General</h2>
            <?php 
            $summary = $dashboardController->getGeneralSummary();
            ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Registros</h3>
                    <p><?php echo number_format($summary['total_registros']); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Departamentos</h3>
                    <p><?php echo $summary['total_departamentos']; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Hemoglobina Promedio</h3>
                    <p><?php echo $summary['hemoglobina_promedio']; ?> g/dL</p>
                </div>
                <div class="stat-card">
                    <h3>Casos de Anemia</h3>
                    <p><?php echo number_format($summary['casos_anemia']); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Casos por Mes -->
        <div class="chart-section">
            <h2>Casos por Mes</h2>
            <table>
                <tr><th>Mes</th><th>Casos</th></tr>
                <?php 
                $monthlyData = $dashboardController->getCasesByMonth();
                while($row = $monthlyData->fetch_assoc()): 
                ?>
                <tr>
                    <td><?php echo $row['mes']; ?></td>
                    <td><?php echo number_format($row['casos']); ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
        
        <!-- Top Departamentos -->
        <div class="ranking-section">
            <h2>Top 5 Departamentos</h2>
            <table>
                <tr><th>Departamento</th><th>Casos</th><th>Hemoglobina Promedio</th></tr>
                <?php 
                $topDepts = $dashboardController->getTopDepartments();
                while($row = $topDepts->fetch_assoc()): 
                ?>
                <tr>
                    <td><?php echo $row['departamento']; ?></td>
                    <td><?php echo number_format($row['casos']); ?></td>
                    <td><?php echo $row['hemoglobina_promedio']; ?> g/dL</td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
        
        <!-- Alertas -->
        <div class="alerts-section">
            <h2>Alertas del Sistema</h2>
            <table>
                <tr><th>Tipo de Alerta</th><th>Cantidad</th></tr>
                <?php 
                $alerts = $dashboardController->getAlerts();
                while($row = $alerts->fetch_assoc()): 
                ?>
                <tr>
                    <td><?php echo $row['tipo_alerta']; ?></td>
                    <td><?php echo number_format($row['cantidad']); ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
        
        <!-- Estadísticas por Género -->
        <div class="gender-section">
            <h2>Resumen por Género</h2>
            <table>
                <tr><th>Género</th><th>Total</th><th>Hemoglobina Promedio</th><th>Con Anemia</th></tr>
                <?php 
                $genderStats = $dashboardController->getQuickGenderStats();
                while($row = $genderStats->fetch_assoc()): 
                ?>
                <tr>
                    <td><?php echo $row['sexo']; ?></td>
                    <td><?php echo number_format($row['total']); ?></td>
                    <td><?php echo $row['hemoglobina_promedio']; ?> g/dL</td>
                    <td><?php echo number_format($row['con_anemia']); ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
<?php
