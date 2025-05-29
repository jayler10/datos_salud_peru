<?php
require_once __DIR__ . '/../controllers/StatsController.php';
require_once __DIR__ . '/../config/database.php';


$statsController = new StatsController($mysqli);
$anemiaStats = $statsController->getAnemiaStats();
$hemoglobinStats = $statsController->getHemoglobinStats();
$ageStats = $statsController->getAgeStats();


$casos = $stats->getAnemiaCasesBySex();
$total = array_sum($casos);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de Salud</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">📊 Datos de Salud</h1>
        
        <!-- Estadísticas de Hemoglobina -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-red-600">Estadísticas de Hemoglobina</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded">
                    <div class="text-2xl font-bold text-blue-600"><?= number_format($hemoglobinStats['hb_min'], 1) ?></div>
                    <div class="text-sm text-gray-600">Mínima (g/dL)</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded">
                    <div class="text-2xl font-bold text-green-600"><?= number_format($hemoglobinStats['hb_max'], 1) ?></div>
                    <div class="text-sm text-gray-600">Máxima (g/dL)</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded">
                    <div class="text-2xl font-bold text-purple-600"><?= number_format($hemoglobinStats['hb_promedio'], 1) ?></div>
                    <div class="text-sm text-gray-600">Promedio (g/dL)</div>
                </div>
                <div class="text-center p-4 bg-orange-50 rounded">
                    <div class="text-2xl font-bold text-orange-600"><?= number_format($hemoglobinStats['hb_desviacion_tipica'], 1) ?></div>
                    <div class="text-sm text-gray-600">Desv. Estándar</div>
                </div>
            </div>
        </div>

        <!-- Estadísticas de Edad -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-blue-600">Estadísticas de Edad</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-indigo-50 rounded">
                    <div class="text-2xl font-bold text-indigo-600"><?= $ageStats['edad_min'] ?></div>
                    <div class="text-sm text-gray-600">Edad Mínima</div>
                </div>
                <div class="text-center p-4 bg-teal-50 rounded">
                    <div class="text-2xl font-bold text-teal-600"><?= $ageStats['edad_max'] ?></div>
                    <div class="text-sm text-gray-600">Edad Máxima</div>
                </div>
                <div class="text-center p-4 bg-cyan-50 rounded">
                    <div class="text-2xl font-bold text-cyan-600"><?= number_format($ageStats['edad_promedio'], 1) ?></div>
                    <div class="text-sm text-gray-600">Edad Promedio</div>
                </div>
                <div class="text-center p-4 bg-pink-50 rounded">
                    <div class="text-2xl font-bold text-pink-600"><?= number_format($ageStats['edad_desviacion_tipica'], 1) ?></div>
                    <div class="text-sm text-gray-600">Desv. Estándar</div>
                </div>
            </div>
        </div>

        <!-- Estadísticas de Anemia -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-green-600">Diagnóstico de Anemia</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Diagnóstico</th>
                            <th class="px-4 py-2 text-center">Cantidad</th>
                            <th class="px-4 py-2 text-center">Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $anemiaStats->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium"><?= htmlspecialchars($row['dx_anemia']) ?></td>
                            <td class="px-4 py-2 text-center"><?= number_format($row['cantidad']) ?></td>
                            <td class="px-4 py-2 text-center">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">
                                    <?= number_format($row['porcentaje'], 1) ?>%
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        


        <div style="max-width: 500px; margin: 30px auto; font-family: Arial, sans-serif;">
    <h2 style="text-align: center;">Casos de Anemia por Sexo</h2>

    <?php if ($total === 0): ?>
        <p style="text-align: center; color: #666;">No hay datos disponibles.</p>
    <?php else: ?>
        <div style="margin-bottom: 15px;">
            <strong>Hombres: <?php echo number_format($casos['Hombre']); ?></strong>
            <div style="background-color: #f5f5f5; border-radius: 8px; overflow: hidden; height: 24px; margin-top: 5px;">
                <div style="width: <?php echo round(($casos['Hombre'] / $total) * 100); ?>%; background-color: #e74c3c; height: 100%;"></div>
            </div>
        </div>

        <div>
            <strong>Mujeres: <?php echo number_format($casos['Mujer']); ?></strong>
            <div style="background-color: #f5f5f5; border-radius: 8px; overflow: hidden; height: 24px; margin-top: 5px;">
                <div style="width: <?php echo round(($casos['Mujer'] / $total) * 100); ?>%; background-color: #3498db; height: 100%;"></div>
            </div>
        </div>

        <p style="text-align: center; margin-top: 20px; font-style: italic; color: #777;">
            Total de casos: <?php echo number_format($total); ?>
        </p>
    <?php endif; ?>
</div>













    </div>

    <!-- Optional: Add some JavaScript for enhanced interactivity -->
    <script>
        // Add hover effects and smooth transitions
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth hover transitions to stat cards
            const statCards = document.querySelectorAll('.bg-white');
            statCards.forEach(card => {
                card.style.transition = 'transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out';
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.15)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 1px 3px rgba(0, 0, 0, 0.1)';
                });
            });

            // Add loading animation for table if needed
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    row.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 50);
            });
        });
    </script>
</body>
</html>
