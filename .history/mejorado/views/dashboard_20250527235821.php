<?php
// views/dashboard.php
$summary = $controller->getDashboardSummary();
?>

<div>
    <h2>📋 Panel Principal</h2>
    
    <!-- Resumen General -->
    <div>
        <h3>📊 Resumen General</h3>
        <table border="1">
            <tr>
                <td><strong>Total de Registros:</strong></td>
                <td><?php echo number_format($summary['total_records']); ?></td>
            </tr>
            <tr>
                <td><strong>Hemoglobina Promedio:</strong></td>
                <td><?php echo formatearNumero($summary['hemoglobin']['hb_promedio'], 1); ?> g/dL</td>
            </tr>
            <tr>
                <td><strong>Registros con Hemoglobina:</strong></td>
                <td><?php echo number_format($summary['hemoglobin']['total_con_hb']); ?></td>
            </tr>
        </table>
    </div>

    <!-- Distribución por Sexo -->
    <div>
        <h3>👥 Distribución por Sexo</h3>
        <table border="1">
            <thead>
                <tr>
                    <th>Sexo</th>
                    <th>Cantidad</th>
                    <th>Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($summary['gender'] as $gender): ?>
                <tr>
                    <td><?php echo formatSex($gender['sexo']); ?></td>
                    <td><?php echo number_format($gender['cantidad']); ?></td>
                    <td><?php echo formatearNumero($gender['porcentaje']); ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Diagnósticos de Anemia -->
    <div>
        <h3>🩺 Principales Diagnósticos de Anemia</h3>
        <table border="1">
            <thead>
                <tr>
                    <th>Diagnóstico</th>
                    <th>Casos</th>
                    <th>Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($summary['anemia'] as $anemia): ?>
                <tr>
                    <td><?php echo sanitizeOutput($anemia['dx_anemia']); ?></td>
                    <td><?php echo number_format($anemia['cantidad']); ?></td>
                    <td><?php echo formatearNumero($anemia['porcentaje']); ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Estadísticas de Hemoglobina -->
    <div>
        <h3>🩸 Estadísticas de Hemoglobina</h3>
        <table border="1">
            <tr>
                <td><strong>Valor Mínimo:</strong></td>
                <td><?php echo formatearNumero($summary['hemoglobin']['hb_min']); ?> g/dL</td>
            </tr>
            <tr>
                <td><strong>Valor Máximo:</strong></td>
                <td><?php echo formatearNumero($summary['hemoglobin']['hb_max']); ?> g/dL</td>
            </tr>
            <tr>
                <td><strong>Desviación Estándar:</strong></td>
                <td><?php echo formatearNumero($summary['hemoglobin']['hb_desviacion_tipica']); ?> g/dL</td>
            </tr>
            <tr>
                <td><strong>Coeficiente de Variación:</strong></td>
                <td>
                    <?php 
                    $cv = ($summary['hemoglobin']['hb_desviacion_tipica'] / $summary['hemoglobin']['hb_promedio']) * 100;
                    echo formatearNumero($cv);
                    ?>%
                </td>
            </tr>
        </table>
    </div>

    <!-- Acciones Rápidas -->
    <div>
        <h3>⚡ Acciones Rápidas</h3>
        <button onclick="loadSection('statistics')">📈 Ver Estadísticas Completas</button>
        <button onclick="loadSection('health')">💓 Análisis de Salud</button>
        <button onclick="loadSection('geography')">🗺️ Distribución Geográfica</button>
        <button onclick="printReport()">🖨️ Imprimir Reporte</button>
    </div>
</div>

<?php
// views/demographics.php
?>
<div>
    <h2>👥 Análisis Demográfico</h2>
    
    <!-- Distribución por Sexo -->
    <div>
        <h3>Distribución por Sexo</h3>
        <table border="1">
            <thead>
                <tr>
                    <th>Sexo</th>
                    <th>Cantidad</th>
                    <th>Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $genderStats = $controller->getGenderStats();
                while($row = $genderStats->fetch_assoc()): 
                ?>
                <tr>
                    <td><?php echo formatSex($row['sexo']); ?></td>
                    <td><?php echo number_format($row['cantidad']); ?></td>
                    <td><?php echo formatearNumero($row['porcentaje']); ?>%</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Estadísticas de Edad -->
    <div>
        <h3>📅 Estadísticas de Edad</h3>
        <?php $ageStats = $controller->getAgeStats(); ?>
        <table border="1">
            <tr>
                <td><strong>Edad Mínima:</strong></td>
                <td><?php echo number_format($ageStats['edad_min']); ?> años</td>
            </tr>
            <tr>
                <td><strong>Edad Máxima:</strong></td>
                <td><?php echo number_format($ageStats['edad_max']); ?> años</td>
            </tr>
            <tr>
                <td><strong>Edad Promedio:</strong></td>
                <td><?php echo formatearNumero($ageStats['edad_promedio'], 1); ?> años</td>
            </tr>
            <tr>
                <td><strong>Desviación Estándar:</strong></td>
                <td><?php echo formatearNumero($ageStats['edad_desviacion_tipica'], 1); ?> años</td>
            </tr>
            <tr>
                <td><strong>Total con Fecha de Nacimiento:</strong></td>
                <td><?php echo number_format($ageStats['total_con_fecha']); ?></td>
            </tr>
        </table>
    </div>
</div>

<?php
