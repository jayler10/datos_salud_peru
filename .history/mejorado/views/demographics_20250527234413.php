<?php
// views/demographics.php - Vista de estadísticas demográficas
$estadisticasSexo = $controller->getGenderStats();
$estadisticasEdad = $controller->getAgeStats();
?>

<div class="stats-container">
    <h2 class="stats-title">👥 Estadísticas Demográficas</h2>
    
    <div class="stats-grid">
        <!-- Distribución por Sexo -->
        <div class="stats-card">
            <h3 class="card-title">
                <i class="fas fa-users"></i> Distribución por Sexo
            </h3>
            <div class="chart-container">
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Sexo</th>
                            <th>Cantidad</th>
                            <th>Porcentaje</th>
                            <th>Visual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $estadisticasSexo->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <span class="gender-icon">
                                    <i class="fas <?php echo $row['sexo'] == 'M' ? 'fa-mars' : 'fa-venus'; ?>"></i>
                                    <?php echo $row['sexo'] == 'M' ? 'Masculino' : 'Femenino'; ?>
                                </span>
                            </td>
                            <td><strong><?php echo number_format($row['cantidad']); ?></strong></td>
                            <td><?php echo formatearNumero($row['porcentaje']); ?>%</td>
                            <td>
                                <div class="progress-mini">
                                    <div class="progress-bar-mini <?php echo $row['sexo'] == 'M' ? 'male' : 'female'; ?>" 
                                         style="width: <?php echo $row['porcentaje']; ?>%"></div>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Estadísticas de Edad -->
        <?php if($estadisticasEdad && $estadisticasEdad['total_con_fecha'] > 0): ?>
        <div class="stats-card">
            <h3 class="card-title">
                <i class="fas fa-birthday-cake"></i> Estadísticas de Edad
            </h3>
            <div class="metrics-container">
                <div class="metric-item">
                    <div class="metric-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="metric-content">
                        <span class="metric-label">Registros con Fecha</span>
                        <span class="metric-value"><?php echo number_format($estadisticasEdad['total_con_fecha']); ?></span>
                    </div>
                </div>
                
                <div class="metric-item">
                    <div class="metric-icon">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div class="metric-content">
                        <span class="metric-label">Edad Mínima</span>
                        <span class="metric-value"><?php echo $estadisticasEdad['edad_min']; ?> años</span>
                    </div>
                </div>
                
                <div class="metric-item">
                    <div class="metric-icon">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <div class="metric-content">
                        <span class="metric-label">Edad Máxima</span>
                        <span class="metric-value"><?php echo $estadisticasEdad['edad_max']; ?> años</span>
                    </div>
                </div>
                
                <div class="metric-item">
                    <div class="metric-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="metric-content">
                        <span class="metric-label">Edad Promedio</span>
                        <span class="metric-value"><?php echo formatearNumero($estadisticasEdad['edad_promedio']); ?> años</span>
                    </div>
                </div>
                
                <div class="metric-item">
                    <div class="metric-icon">
                        <i class="fas fa-wave-square"></i>
                    </div>
                    <div class="metric-content">
                        <span class="metric-label">Desviación Estándar</span>
                        <span class="metric-value"><?php echo formatearNumero($estadisticasEdad['edad_desviacion_tipica']); ?> años</span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Gráfico Visual de Distribución por Sexo -->
<div class="stats-container">
    <h2 class="stats-title">📊 Visualización de Distribución por Sexo</h2>
    <div class="visual-chart">
        <?php 
        $estadisticasSexo->data_seek(0); // Reset pointer
        while($row = $estadisticasSexo->fetch_assoc()): 
        ?>
        <div class="chart-segment <?php echo $row['sexo'] == 'M' ? 'male-segment' : 'female-segment'; ?>">
            <div class="segment-info">
                <h4><?php echo $row['sexo'] == 'M' ? 'Masculino' : 'Femenino'; ?></h4>
                <div class="segment-stats">
                    <span class="count"><?php echo number_format($row['cantidad']); ?></span>
                    <span class="percentage"><?php echo formatearNumero($row['porcentaje']); ?>%</span>
                </div>
            </div>
            <div class="segment-bar" style="width: <?php echo $row['porcentaje']; ?>%"></div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<style>
.gender-icon {
    display: flex;
    align-items: center;
    gap: 8px;
}

.gender-icon i.fa-mars {
    color: #3498db;
}

.gender-icon i.fa-venus {
    color: #e74c3c;
}

.progress-mini {
    width: 100px;
    height: 8px;
    background-color: #f1f1f1;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar-mini {
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s ease;
}

.progress-bar-mini.male {
    background-color: #3498db;
}

.progress-bar-mini.female {
    background-color: #e74c3c;
}

.metrics-container {
    display: grid;
    grid-template-columns: 1fr;
    gap: 15px;
}

.metric-item {
    display: flex;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #3498db;
}

.metric-icon {
    width: 40px;
    height: 40px;
    background: #3498db;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 15px;
}

.metric-content {
    flex: 1;
}

.metric-label {
    display: block;
    font-size: 14px;
    color: #666;
    margin-bottom: 5px;
}

.metric-value {
    display: block;
    font-size: 20px;
    font-weight: bold;
    color: #2c3e50;
}

.visual-chart {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-top: 20px;
}

.chart-segment {
    display: flex;
    align-items: center;
    gap: 15px;
}

.segment-info {
    min-width: 200px;
}

.segment-info h4 {
    margin: 0 0 5px 0;
    color: #2c3e50;
}

.segment-stats {
    display: flex;
    gap: 10px;
}

.count {
    font-weight: bold;
    color: #2c3e50;
}

.percentage {
    color: #7f8c8d;
}

.segment-bar {
    height: 30px;
    border-radius: 15px;
    transition: width 0.5s ease;
    position: relative;
    min-width: 20px;
}

.male-segment .segment-bar {
    background: linear-gradient(90deg, #3498db, #5dade2);
}

.female-segment .segment-bar {
    background: linear-gradient(90deg, #e74c3c, #ec7063);
}

@media (max-width: 768px) {
    .chart-segment {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .segment-bar {
        width: 100% !important;
        max-width: 300px;
    }
}
</style>