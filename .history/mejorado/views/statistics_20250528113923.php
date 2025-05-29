<?php
// statistics.php - Interface para estadísticas generales
require_once '../controllers/StatsController.php';
require_once '../config/database.php';

$statsController = new StatsController($mysqli);
$totalRecords = $statsController->getTotalRecords();
$genderStats = $statsController->getGenderStats();
$altitudeStats = $statsController->getAltitudeStats();
$anemiaByGender = $statsController->getAnemiaByGender();
$anemiaByAltitude = $statsController->getAnemiaByAltitudeRange();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas Generales</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">📈 Estadísticas Generales</h1>
        
        <!-- Total de Registros -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="text-center">
                <div class="text-4xl font-bold text-indigo-600 mb-2"><?= number_format($totalRecords) ?></div>
                <div class="text-lg text-gray-700">Total de Registros</div>
            </div>
        </div>

        <!-- Estadísticas por Sexo -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-purple-600">Distribución por Sexo</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php 
                $genderStats->data_seek(0); // Reset del puntero para reutilizar
                while ($row = $genderStats->fetch_assoc()): 
                ?>
                <div class="p-4 border rounded-lg text-center">
                    <div class="text-2xl font-bold text-gray-800"><?= number_format($row['cantidad']) ?></div>
                    <div class="text-lg font-medium text-gray-600"><?= ucfirst($row['sexo']) ?></div>
                    <div class="text-sm text-gray-500"><?= $row['porcentaje'] ?>%</div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Anemia por Sexo -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-red-600">Anemia por Sexo</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-red-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Sexo</th>
                            <th class="px-4 py-2 text-left">Diagnóstico</th>
                            <th class="px-4 py-2 text-center">Cantidad</th>
                            <th class="px-4 py-2 text-center">% del Sexo</th>
                            <th class="px-4 py-2 text-center">% Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $currentSex = '';
                        while ($row = $anemiaByGender->fetch_assoc()): 
                            $isNewSex = $currentSex !== $row['sexo'];
                            $currentSex = $row['sexo'];
                            
                            // Color del diagnóstico
                            $diagColor = 'bg-gray-100';
                            if (stripos($row['dx_anemia'], 'sin') !== false || stripos($row['dx_anemia'], 'normal') !== false) {
                                $diagColor = 'bg-green-100 text-green-800';
                            } elseif (stripos($row['dx_anemia'], 'leve') !== false) {
                                $diagColor = 'bg-yellow-100 text-yellow-800';
                            } elseif (stripos($row['dx_anemia'], 'moderada') !== false) {
                                $diagColor = 'bg-orange-100 text-orange-800';
                            } elseif (stripos($row['dx_anemia'], 'severa') !== false || stripos($row['dx_anemia'], 'grave') !== false) {
                                $diagColor = 'bg-red-100 text-red-800';
                            }
                        ?>
                        <tr class="border-b hover:bg-gray-50 <?= $isNewSex ? 'border-t-2 border-gray-300' : '' ?>">
                            <td class="px-4 py-2 font-medium <?= $isNewSex ? 'text-purple-600' : 'text-gray-400' ?>">
                                <?= $isNewSex ? ucfirst($row['sexo']) : '' ?>
                            </td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-sm <?= $diagColor ?>">
                                    <?= htmlspecialchars($row['dx_anemia']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-2 text-center font-medium"><?= number_format($row['cantidad']) ?></td>
                            <td class="px-4 py-2 text-center">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">
                                    <?= $row['porcentaje_por_sexo'] ?>%
                                </span>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm">
                                    <?= $row['porcentaje_total'] ?>%
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-sm text-gray-600">
                <p><strong>% del Sexo:</strong> Porcentaje dentro de cada sexo | <strong>% Total:</strong> Porcentaje del total general</p>
            </div>
        </div>

        <!-- Estadísticas de Altitud -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-green-600">Estadísticas de Altitud</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-green-50 rounded">
                    <div class="text-2xl font-bold text-green-600"><?= number_format($altitudeStats['alt_min']) ?></div>
                    <div class="text-sm text-gray-600">Mínima (msnm)</div>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded">
                    <div class="text-2xl font-bold text-blue-600"><?= number_format($altitudeStats['alt_max']) ?></div>
                    <div class="text-sm text-gray-600">Máxima (msnm)</div>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded">
                    <div class="text-2xl font-bold text-yellow-600"><?= number_format($altitudeStats['alt_promedio']) ?></div>
                    <div class="text-sm text-gray-600">Promedio (msnm)</div>
                </div>
                <div class="text-center p-4 bg-red-50 rounded">
                    <div class="text-2xl font-bold text-red-600"><?= number_format($altitudeStats['alt_desviacion_tipica']) ?></div>
                    <div class="text-sm text-gray-600">Desv. Estándar</div>
                </div>
            </div>
        </div>



        <!-- Comparación Altitud vs Anemia -->
<div class="bg-white rounded-lg shadow-md p-6 mt-6">
    <h2 class="text-xl font-semibold mb-4 text-blue-700">Comparativa: Altitud vs Anemia</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto text-sm">
            <thead class="bg-blue-50 text-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left">Rango de Altitud</th>
                    <th class="px-4 py-2 text-left">Diagnóstico</th>
                    <th class="px-4 py-2 text-center">Casos</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $currentRange = '';
                while ($row = $anemiaByAltitude->fetch_assoc()): 
                    $isNewRange = $currentRange !== $row['rango_altitud'];
                    $currentRange = $row['rango_altitud'];
                ?>
                <tr class="border-b hover:bg-gray-50 <?= $isNewRange ? 'border-t-2 border-gray-300' : '' ?>">
                    <td class="px-4 py-2 font-medium <?= $isNewRange ? 'text-blue-700' : 'text-gray-400' ?>">
                        <?= $isNewRange ? $row['rango_altitud'] : '' ?>
                    </td>
                    <td class="px-4 py-2"><?= htmlspecialchars($row['dx_anemia']) ?></td>
                    <td class="px-4 py-2 text-center"><?= number_format($row['cantidad']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>




    </div>
</body>
</html>