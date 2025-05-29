<?php
require_once '../controllers/StatsController.php';
// Obtener datos demográficos
$personasPorSexo = $dashboard->getPersonasPorSexo();
$hemoglobinaPorSexo = $dashboard->getHemoglobinaPorSexo();
$edadVsHemoglobina = $dashboard->getEdadVsHemoglobina();
$porcentajeAnemiaPorSexo = $dashboard->getPorcentajeAnemiaPorSexo();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Análisis Demográfico - Edad y Sexo</title>
</head>
<body>
    <h1>Análisis por Edad y Sexo</h1>
    
    <div class="demografico">
        <h3>Número de Personas por Sexo</h3>
        <table>
            <tr><th>Sexo</th><th>Cantidad</th></tr>
            <?php foreach($personasPorSexo as $persona): ?>
            <tr>
                <td><?php echo $persona['sexo']; ?></td>
                <td><?php echo number_format($persona['cantidad']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <div id="sexoBarChart"></div>
        
        <h3>Porcentaje de Anemia por Sexo</h3>
        <table>
            <tr><th>Sexo</th><th>Total</th><th>Con Anemia</th><th>% Anemia</th></tr>
            <?php foreach($porcentajeAnemiaPorSexo as $anemia): ?>
            <tr>
                <td><?php echo $anemia['sexo']; ?></td>
                <td><?php echo number_format($anemia['total']); ?></td>
                <td><?php echo number_format($anemia['con_anemia']); ?></td>
                <td><?php echo $anemia['porcentaje_anemia']; ?>%</td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <h3>Distribución de Hemoglobina por Sexo</h3>
        <div id="hemoglobinaHistogram"></div>
        
        <h3>Edad vs Hemoglobina</h3>
        <div id="edadHemoglobinaScatter"></div>
    </div>
    
    <script>
        const personasSexoData = <?php echo json_encode($personasPorSexo); ?>;
        const hemoglobinaSexoData = <?php echo json_encode($hemoglobinaPorSexo); ?>;
        const edadHemoglobinaData = <?php echo json_encode($edadVsHemoglobina); ?>;
        const anemiaSexoData = <?php echo json_encode($porcentajeAnemiaPorSexo); ?>;
        
        // Scripts para generar gráficos
    </script>
</body>
</html>