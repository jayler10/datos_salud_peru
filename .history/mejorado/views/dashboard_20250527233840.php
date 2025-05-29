<?php
require_once '../controllers/StatsController.php';

$dashboard = new StatsController($mysqli);

// Obtener datos para el dashboard
$totalRegistros = $dashboard->getTotalRegistros();
$porcentajeAnemia = $dashboard->getPorcentajeAnemia();
$promedioHemoglobina = $dashboard->getPromedioHemoglobina();
$rangoAlturas = $dashboard->getRangoAlturas();
$tendenciaHemoglobina = $dashboard->getTendenciaHemoglobinaPorEdad();
$proporcionSexos = $dashboard->getProporcionSexos();
$ultimosRegistros = $dashboard->getUltimosRegistrosPorProvincia();
$relacionAnemia = $dashboard->getRelacionAnemiaHemoglobina();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Análisis de Anemia</title>
</head>
<body>
    <h1>Dashboard General - Indicadores Clave</h1>
    
    <div class="kpis">
        <div class="kpi-card">
            <h3>Total de Registros</h3>
            <p><?php echo number_format($totalRegistros); ?></p>
        </div>
        
        <div class="kpi-card">
            <h3>% de Anemia Detectada</h3>
            <p><?php echo $porcentajeAnemia; ?>%</p>
        </div>
        
        <div class="kpi-card">
            <h3>Promedio de Hemoglobina</h3>
            <p><?php echo $promedioHemoglobina; ?> g/dL</p>
        </div>
        
        <div class="kpi-card">
            <h3>Rango de Alturas</h3>
            <p><?php echo $rangoAlturas['minimo']; ?> - <?php echo $rangoAlturas['maximo']; ?> msnm</p>
        </div>
    </div>
    
    <div class="charts">
        <h3>Tendencia de Hemoglobina por Edad</h3>
        <div id="tendenciaChart"></div>
        
        <h3>Proporción por Sexos</h3>
        <div id="sexosChart"></div>
        
        <h3>Últimos Registros por Provincia</h3>
        <table>
            <tr><th>Provincia</th><th>Registros</th></tr>
            <?php foreach($ultimosRegistros as $registro): ?>
            <tr>
                <td><?php echo $registro['provincia']; ?></td>
                <td><?php echo $registro['registros']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    
    <script>
        // Datos para gráficos
        const tendenciaData = <?php echo json_encode($tendenciaHemoglobina); ?>;
        const sexosData = <?php echo json_encode($proporcionSexos); ?>;
        
        // Aquí irían los scripts para generar los gráficos con Chart.js o similar
    </script>
</body>
</html>