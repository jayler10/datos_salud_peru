<?php
// Configuración para mostrar errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
$mysqli = new mysqli('localhost', 'root', '', 'salud_puno');
$mysqli->set_charset('utf8mb4');

if ($mysqli->connect_error) {
    die('Error de conexión: ' . $mysqli->connect_error);
}

// Función para formatear números
function formatearNumero($numero) {
    return number_format($numero, 2, '.', ',');
}

// Obtener estadísticas generales
$totalRegistros = $mysqli->query("SELECT COUNT(*) as total FROM datos_salud")->fetch_assoc()['total'];

// Estadísticas por sexo
$estadisticasSexo = $mysqli->query("
    SELECT 
        sexo,
        COUNT(*) as cantidad,
        ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM datos_salud), 2) as porcentaje
    FROM datos_salud 
    GROUP BY sexo
    ORDER BY cantidad DESC
");

// Estadísticas por departamento
$estadisticasDepartamento = $mysqli->query("
    SELECT 
        departamento,
        COUNT(*) as cantidad,
        ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM datos_salud), 2) as porcentaje
    FROM datos_salud 
    GROUP BY departamento
    ORDER BY cantidad DESC
");

// Estadísticas por provincia (top 10)
$estadisticasProvincia = $mysqli->query("
    SELECT 
        provincia,
        COUNT(*) as cantidad,
        ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM datos_salud), 2) as porcentaje
    FROM datos_salud 
    GROUP BY provincia
    ORDER BY cantidad DESC
    LIMIT 10
");

// Estadísticas por distrito (top 15)
$estadisticasDistrito = $mysqli->query("
    SELECT 
        distrito,
        COUNT(*) as cantidad,
        ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM datos_salud), 2) as porcentaje
    FROM datos_salud 
    WHERE distrito != '' AND distrito IS NOT NULL
    GROUP BY distrito
    ORDER BY cantidad DESC
    LIMIT 15
");

// Estadísticas por diagnóstico de anemia
$estadisticasAnemia = $mysqli->query("
    SELECT 
        dx_anemia,
        COUNT(*) as cantidad,
        ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM datos_salud), 2) as porcentaje
    FROM datos_salud 
    WHERE dx_anemia != '' AND dx_anemia IS NOT NULL
    GROUP BY dx_anemia
    ORDER BY cantidad DESC
");

// Estadísticas de hemoglobina
$estadisticasHemoglobina = $mysqli->query("
    SELECT 
        MIN(hemoglobina) as hb_min,
        MAX(hemoglobina) as hb_max,
        AVG(hemoglobina) as hb_promedio,
        VARIANCE(hemoglobina) as hb_varianza,
        STDDEV(hemoglobina) as hb_desviacion_tipica,
        COUNT(hemoglobina) as total_con_hb
    FROM datos_salud 
    WHERE hemoglobina IS NOT NULL AND hemoglobina > 0
")->fetch_assoc();

// Estadísticas de altura
$estadisticasAltura = $mysqli->query("
    SELECT 
        MIN(altura_msnm) as alt_min,
        MAX(altura_msnm) as alt_max,
        AVG(altura_msnm) as alt_promedio,
        VARIANCE(altura_msnm) as alt_varianza,
        STDDEV(altura_msnm) as alt_desviacion_tipica,
        COUNT(altura_msnm) as total_con_altura
    FROM datos_salud 
    WHERE altura_msnm IS NOT NULL AND altura_msnm > 0
")->fetch_assoc();

// Rangos de hemoglobina
$rangosHemoglobina = $mysqli->query("
    SELECT 
        CASE 
            WHEN hemoglobina < 7 THEN 'Muy Baja (< 7.0)'
            WHEN hemoglobina >= 7 AND hemoglobina < 10 THEN 'Baja (7.0 - 9.9)'
            WHEN hemoglobina >= 10 AND hemoglobina < 12 THEN 'Normal Baja (10.0 - 11.9)'
            WHEN hemoglobina >= 12 AND hemoglobina < 15 THEN 'Normal (12.0 - 14.9)'
            WHEN hemoglobina >= 15 THEN 'Alta (≥ 15.0)'
            ELSE 'No Clasificado'
        END as rango_hb,
        COUNT(*) as cantidad,
        ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM datos_salud WHERE hemoglobina IS NOT NULL), 2) as porcentaje
    FROM datos_salud 
    WHERE hemoglobina IS NOT NULL
    GROUP BY rango_hb
    ORDER BY 
        CASE 
            WHEN rango_hb = 'Muy Baja (< 7.0)' THEN 1
            WHEN rango_hb = 'Baja (7.0 - 9.9)' THEN 2
            WHEN rango_hb = 'Normal Baja (10.0 - 11.9)' THEN 3
            WHEN rango_hb = 'Normal (12.0 - 14.9)' THEN 4
            WHEN rango_hb = 'Alta (≥ 15.0)' THEN 5
            ELSE 6
        END
");

// Rangos de altitud
$rangosAltura = $mysqli->query("
    SELECT 
        CASE 
            WHEN altura_msnm < 1000 THEN 'Costa (< 1000m)'
            WHEN altura_msnm >= 1000 AND altura_msnm < 2500 THEN 'Yunga (1000-2499m)'
            WHEN altura_msnm >= 2500 AND altura_msnm < 3500 THEN 'Quechua (2500-3499m)'
            WHEN altura_msnm >= 3500 AND altura_msnm < 4000 THEN 'Suni (3500-3999m)'
            WHEN altura_msnm >= 4000 AND altura_msnm < 4800 THEN 'Puna (4000-4799m)'
            WHEN altura_msnm >= 4800 THEN 'Janca (≥ 4800m)'
            ELSE 'No Clasificado'
        END as rango_altura,
        COUNT(*) as cantidad,
        ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM datos_salud WHERE altura_msnm IS NOT NULL), 2) as porcentaje
    FROM datos_salud 
    WHERE altura_msnm IS NOT NULL
    GROUP BY rango_altura
    ORDER BY 
        CASE 
            WHEN rango_altura = 'Costa (< 1000m)' THEN 1
            WHEN rango_altura = 'Yunga (1000-2499m)' THEN 2
            WHEN rango_altura = 'Quechua (2500-3499m)' THEN 3
            WHEN rango_altura = 'Suni (3500-3999m)' THEN 4
            WHEN rango_altura = 'Puna (4000-4799m)' THEN 5
            WHEN rango_altura = 'Janca (≥ 4800m)' THEN 6
            ELSE 7
        END
");

// Estadísticas por edad (calculada desde fecha_nacimiento)
$estadisticasEdad = $mysqli->query("
    SELECT 
        MIN(TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE())) as edad_min,
        MAX(TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE())) as edad_max,
        AVG(TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE())) as edad_promedio,
        VARIANCE(TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE())) as edad_varianza,
        STDDEV(TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE())) as edad_desviacion_tipica,
        COUNT(*) as total_con_fecha
    FROM datos_salud 
    WHERE fecha_nacimiento IS NOT NULL
")->fetch_assoc();

// Top 10 establecimientos
$topEstablecimientos = $mysqli->query("
    SELECT 
        establecimiento,
        COUNT(*) as cantidad,
        ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM datos_salud), 2) as porcentaje
    FROM datos_salud 
    WHERE establecimiento != '' AND establecimiento IS NOT NULL
    GROUP BY establecimiento
    ORDER BY cantidad DESC
    LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas Descriptivas - Datos de Salud Puno</title>
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
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .stats-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .stats-table th, .stats-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .stats-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .highlight {
            color: #2c3e50;
            font-weight: bold;
        }
        .metric {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .metric:last-child {
            border-bottom: none;
        }
        .anemia-normal { background-color: #d4edda; }
        .anemia-leve { background-color: #fff3cd; }
        .anemia-moderada { background-color: #f8d7da; }
        .anemia-severa { background-color: #f5c6cb; }
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Estadísticas Descriptivas - Datos de Salud Puno</h1>
        
        <!-- Resumen General -->
        <div class="stats-container">
            <h2 class="stats-title">📋 Resumen General</h2>
            <p>Total de registros: <span class="highlight"><?php echo number_format($totalRegistros); ?></span></p>
        </div>

        <!-- Estadísticas Demográficas -->
        <div class="stats-grid">
            <!-- Sexo -->
            <div class="stats-container">
                <h2 class="stats-title">👥 Distribución por Sexo</h2>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Sexo</th>
                            <th>Cantidad</th>
                            <th>Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $estadisticasSexo->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['sexo'] == 'M' ? 'Masculino' : 'Femenino'; ?></td>
                            <td><?php echo number_format($row['cantidad']); ?></td>
                            <td><?php echo formatearNumero($row['porcentaje']); ?>%</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Departamento -->
            <div class="stats-container">
                <h2 class="stats-title">🗺️ Distribución por Departamento</h2>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Departamento</th>
                            <th>Cantidad</th>
                            <th>Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $estadisticasDepartamento->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['departamento']); ?></td>
                            <td><?php echo number_format($row['cantidad']); ?></td>
                            <td><?php echo formatearNumero($row['porcentaje']); ?>%</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Distribución Geográfica -->
        <div class="stats-grid">
            <!-- Top Provincias -->
            <div class="stats-container">
                <h2 class="stats-title">🏘️ Top 10 Provincias</h2>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Provincia</th>
                            <th>Cantidad</th>
                            <th>Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $estadisticasProvincia->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['provincia']); ?></td>
                            <td><?php echo number_format($row['cantidad']); ?></td>
                            <td><?php echo formatearNumero($row['porcentaje']); ?>%</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Top Distritos -->
            <div class="stats-container">
                <h2 class="stats-title">🏘️ Top 15 Distritos</h2>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Distrito</th>
                            <th>Cantidad</th>
                            <th>Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $estadisticasDistrito->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['distrito']); ?></td>
                            <td><?php echo number_format($row['cantidad']); ?></td>
                            <td><?php echo formatearNumero($row['porcentaje']); ?>%</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Estadísticas de Salud -->
        <div class="stats-container">
            <h2 class="stats-title">🩺 Diagnóstico de Anemia</h2>
            <table class="stats-table">
                <thead>
                    <tr>
                        <th>Diagnóstico</th>
                        <th>Cantidad</th>
                        <th>Porcentaje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $estadisticasAnemia->fetch_assoc()): ?>
                    <tr class="<?php 
                        if(stripos($row['dx_anemia'], 'normal') !== false) echo 'anemia-normal';
                        elseif(stripos($row['dx_anemia'], 'leve') !== false) echo 'anemia-leve';
                        elseif(stripos($row['dx_anemia'], 'moderada') !== false) echo 'anemia-moderada';
                        elseif(stripos($row['dx_anemia'], 'severa') !== false) echo 'anemia-severa';
                    ?>">
                        <td><?php echo htmlspecialchars($row['dx_anemia']); ?></td>
                        <td><?php echo number_format($row['cantidad']); ?></td>
                        <td><?php echo formatearNumero($row['porcentaje']); ?>%</td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Estadísticas Numéricas -->
        <div class="stats-grid">
            <!-- Hemoglobina -->
            <div class="stats-container">
                <h2 class="stats-title">🩸 Estadísticas de Hemoglobina</h2>
                <div class="metric">
                    <span>Registros con datos:</span>
                    <span class="highlight"><?php echo number_format($estadisticasHemoglobina['total_con_hb']); ?></span>
                </div>
                <div class="metric">
                    <span>Mínima:</span>
                    <span class="highlight"><?php echo formatearNumero($estadisticasHemoglobina['hb_min']); ?> g/dL</span>
                </div>
                <div class="metric">
                    <span>Máxima:</span>
                    <span class="highlight"><?php echo formatearNumero($estadisticasHemoglobina['hb_max']); ?> g/dL</span>
                </div>
                <div class="metric">
                    <span>Promedio:</span>
                    <span class="highlight"><?php echo formatearNumero($estadisticasHemoglobina['hb_promedio']); ?> g/dL</span>
                </div>
                <div class="metric">
                    <span>Desviación Típica:</span>
                    <span class="highlight"><?php echo formatearNumero($estadisticasHemoglobina['hb_desviacion_tipica']); ?> g/dL</span>
                </div>
                <div class="metric">
                    <span>Varianza:</span>
                    <span class="highlight"><?php echo formatearNumero($estadisticasHemoglobina['hb_varianza']); ?></span>
                </div>
            </div>

            <!-- Altitud -->
            <div class="stats-container">
                <h2 class="stats-title">⛰️ Estadísticas de Altitud</h2>
                <div class="metric">
                    <span>Registros con datos:</span>
                    <span class="highlight"><?php echo number_format($estadisticasAltura['total_con_altura']); ?></span>
                </div>
                <div class="metric">
                    <span>Mínima:</span>
                    <span class="highlight"><?php echo number_format($estadisticasAltura['alt_min']); ?> msnm</span>
                </div>
                <div class="metric">
                    <span>Máxima:</span>
                    <span class="highlight"><?php echo number_format($estadisticasAltura['alt_max']); ?> msnm</span>
                </div>
                <div class="metric">
                    <span>Promedio:</span>
                    <span class="highlight"><?php echo formatearNumero($estadisticasAltura['alt_promedio']); ?> msnm</span>
                </div>
                <div class="metric">
                    <span>Desviación Típica:</span>
                    <span class="highlight"><?php echo formatearNumero($estadisticasAltura['alt_desviacion_tipica']); ?> msnm</span>
                </div>
                <div class="metric">
                    <span>Varianza:</span>
                    <span class="highlight"><?php echo formatearNumero($estadisticasAltura['alt_varianza']); ?></span>
                </div>
            </div>

            <!-- Edad -->
            <?php if($estadisticasEdad['total_con_fecha'] > 0): ?>
            <div class="stats-container">
                <h2 class="stats-title">👶 Estadísticas de Edad</h2>
                <div class="metric">
                    <span>Registros con fecha:</span>
                    <span class="highlight"><?php echo number_format($estadisticasEdad['total_con_fecha']); ?></span>
                </div>
                <div class="metric">
                    <span>Edad Mínima:</span>
                    <span class="highlight"><?php echo $estadisticasEdad['edad_min']; ?> años</span>
                </div>
                <div class="metric">
                    <span>Edad Máxima:</span>
                    <span class="highlight"><?php echo $estadisticasEdad['edad_max']; ?> años</span>
                </div>
                <div class="metric">
                    <span>Edad Promedio:</span>
                    <span class="highlight"><?php echo formatearNumero($estadisticasEdad['edad_promedio']); ?> años</span>
                </div>
                <div class="metric">
                    <span>Desviación Típica:</span>
                    <span class="highlight"><?php echo formatearNumero($estadisticasEdad['edad_desviacion_tipica']); ?> años</span>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Rangos -->
        <div class="stats-grid">
            <!-- Rangos de Hemoglobina -->
            <div class="stats-container">
                <h2 class="stats-title">📊 Rangos de Hemoglobina</h2>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Rango</th>
                            <th>Cantidad</th>
                            <th>Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $rangosHemoglobina->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['rango_hb']); ?></td>
                            <td><?php echo number_format($row['cantidad']); ?></td>
                            <td><?php echo formatearNumero($row['porcentaje']); ?>%</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Rangos de Altitud -->
            <div class="stats-container">
                <h2 class="stats-title">🏔️ Pisos Altitudinales</h2>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Piso Altitudinal</th>
                            <th>Cantidad</th>
                            <th>Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $rangosAltura->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['rango_altura']); ?></td>
                            <td><?php echo number_format($row['cantidad']); ?></td>
                            <td><?php echo formatearNumero($row['porcentaje']); ?>%</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Establecimientos -->
        <div class="stats-container">
            <h2 class="stats-title">🏥 Top 10 Establecimientos de Salud</h2>
            <table class="stats-table">
                <thead>
                    <tr>
                        <th>Establecimiento</th>
                        <th>Cantidad</th>
                        <th>Porcentaje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $topEstablecimientos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['establecimiento']); ?></td>
                        <td><?php echo number_format($row['cantidad']); ?></td>
                        <td><?php echo formatearNumero($row['porcentaje']); ?>%</td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php $mysqli->close(); ?>