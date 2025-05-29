<?php
// views/dashboard.php - Vista del panel principal
$summary = $controller->getDashboardSummary();
?>

<div class="dashboard-grid">
    <!-- Total de Registros -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <div class="dashboard-card-icon">
                <i class="fas fa-database"></i>
            </div>
            <div class="dashboard-card-title">Total de Registros</div>
        </div>
        <div class="dashboard-card-value"><?php echo number_format($summary['total_records']); ?></div>
        <div class="dashboard-card-subtitle">Registros en la base de datos</div>
    </div>

    <!-- Distribución por Sexo -->
    <?php foreach($summary['gender'] as $gender): ?>
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <div class="dashboard-card-icon">
                <i class="fas <?php echo $gender['sexo'] === 'M' ? 'fa-mars' : 'fa-venus'; ?>"></i>
            </div>
            <div class="dashboard-card-title"><?php echo formatSex($gender['sexo']); ?></div>
        </div>
        <div class="dashboard-card-value"><?php echo number_format($gender['cantidad']); ?></div>
        <div class="dashboard-card-subtitle"><?php echo formatearNumero($gender['porcentaje']); ?>% del total</div>
    </div>
    <?php endforeach; ?>

    <!-- Hemoglobina Promedio -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <div class="dashboard-card-icon">
                <i class="fas fa-tint"></i>
            </div>
            <div class="dashboard-card-title">Hemoglobina Promedio</div>
        </div>
        <div class="dashboard-card-value"><?php echo formatearNumero($summary['hemoglobin']['hb_promedio'], 1); ?></div>
        <div class="dashboard-card-subtitle">g/dL (<?php echo number_format($summary['hemoglobin']['total_con_hb']); ?> registros)</div>
    </div>
</div>

<?php if (isset($summary['anemia']) && is_array($summary['anemia'])): ?>
    <?php foreach($summary['anemia'] as $anemia): ?>
        <?php if (is_array($anemia)): ?>
        <div class="stats-card <?php echo getAnemiaClass($anemia['dx_anemia']); ?>">
            <h4><?php echo sanitizeOutput($anemia['dx_anemia']); ?></h4>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                <span class="metric-value"><?php echo number_format($anemia['cantidad']); ?> casos</span>
                <span class="badge badge-info"><?php echo formatearNumero($anemia['porcentaje']); ?>%</span>
            </div>
            <div class="progress">
                <div class="progress-bar" data-width="<?php echo $anemia['porcentaje']; ?>"></div>
            </div>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php else: ?>
    <p>No hay datos disponibles sobre anemia.</p>
<?php endif; ?>



<!-- Estadísticas Rápidas -->
<div class="stats-container">
    <h2 class="stats-title">📊 Estadísticas Rápidas</h2>
    <div class="metrics-grid">
        <div class="metric">
            <span>Hemoglobina Mínima:</span>
            <span class="metric-value"><?php echo formatearNumero($summary['hemoglobin']['hb_min']); ?> g/dL</span>
        </div>
        <div class="metric">
            <span>Hemoglobina Máxima:</span>
            <span class="metric-value"><?php echo formatearNumero($summary['hemoglobin']['hb_max']); ?> g/dL</span>
        </div>
        <div class="metric">
            <span>Desviación Estándar:</span>
            <span class="metric-value"><?php echo formatearNumero($summary['hemoglobin']['hb_desviacion_tipica']); ?> g/dL</span>
        </div>
        <div class="metric">
            <span>Coeficiente de Variación:</span>
            <span class="metric-value">
                <?php 
                $cv = ($summary['hemoglobin']['hb_desviacion_tipica'] / $summary['hemoglobin']['hb_promedio']) * 100;
                echo formatearNumero($cv); 
                ?>%
            </span>
        </div>
    </div>
</div>

<!-- Acciones Rápidas -->
<div class="stats-container">
    <h2 class="stats-title">⚡ Acciones Rápidas</h2>
    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        <button class="btn btn-refresh" onclick="loadSection('statistics')">
            <i class="fas fa-chart-line"></i> Ver Estadísticas Completas
        </button>
        <button class="btn btn-refresh" onclick="loadSection('health')">
            <i class="fas fa-heartbeat"></i> Análisis de Salud
        </button>
        <button class="btn btn-refresh" onclick="loadSection('geography')">
            <i class="fas fa-map"></i> Distribución Geográfica
        </button>
        <button class="btn btn-refresh" onclick="printReport()">
            <i class="fas fa-print"></i> Imprimir Reporte
        </button>
    </div>
</div>
<div>
    hola
</div>

<script>
// Inicializar animaciones del dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Animar barras de progreso
    setTimeout(() => {
        document.querySelectorAll('.progress-bar').forEach((bar, index) => {
            setTimeout(() => {
                const width = bar.getAttribute('data-width') || '0';
                bar.style.width = width + '%';
            }, index * 200);
        });
    }, 500);
});
</script>