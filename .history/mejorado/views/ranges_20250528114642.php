<?php
// ranges.php - Interface para rangos de datos
require_once __DIR__ . '/../controllers/StatsController.php';
require_once __DIR__ . '/../config/database.php';



$statsController = new StatsController($mysqli);
$hemoglobinRanges = $statsController->getHemoglobinRanges();
$altitudeRanges = $statsController->getAltitudeRanges();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rangos de Datos</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">📊 Rangos de Datos</h1>
        
        <!-- Rangos de Hemoglobina -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-red-600">Rangos de Hemoglobina</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-red-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Rango</th>
                            <th class="px-4 py-2 text-center">Cantidad</th>
                            <th class="px-4 py-2 text-center">Porcentaje</th>
                            <th class="px-4 py-2 text-center">Barra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $hemoglobinRanges->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium"><?= htmlspecialchars($row['rango_hb']) ?></td>
                            <td class="px-4 py-2 text-center"><?= number_format($row['cantidad']) ?></td>
                            <td class="px-4 py-2 text-center">
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">
                                    <?= $row['porcentaje'] ?>%
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-red-500 h-2 rounded-full" style="width: <?= $row['porcentaje'] ?>%"></div>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Rangos de Altitud -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-green-600">Rangos de Altitud (Regiones Naturales)</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-green-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Región</th>
                            <th class="px-4 py-2 text-center">Cantidad</th>
                            <th class="px-4 py-2 text-center">Porcentaje</th>
                            <th class="px-4 py-2 text-center">Barra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $altitudeRanges->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium"><?= htmlspecialchars($row['rango_altura']) ?></td>
                            <td class="px-4 py-2 text-center"><?= number_format($row['cantidad']) ?></td>
                            <td class="px-4 py-2 text-center">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">
                                    <?= $row['porcentaje'] ?>%
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: <?= $row['porcentaje'] ?>%"></div>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>



        <!-- Rangos de Hemoglobina por Sexo -->
<div class="bg-white rounded-lg shadow-md p-6 mt-8">
    <h2 class="text-xl font-semibold mb-4 text-blue-600">Rangos de Hemoglobina por Sexo</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead class="bg-blue-50">
                <tr>
                    <th class="px-4 py-2 text-left">Sexo</th>
                    <th class="px-4 py-2 text-left">Rango Hemoglobina</th>
                    <th class="px-4 py-2 text-center">Cantidad</th>
                    <th class="px-4 py-2 text-center">Porcentaje</th>
                    <th class="px-4 py-2 text-center">Barra</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $hemoglobinRangesBySex->fetch_assoc()): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2 font-medium"><?= htmlspecialchars(ucfirst($row['sexo'])) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($row['rango_hb']) ?></td>
                    <td class="px-4 py-2 text-center"><?= number_format($row['cantidad']) ?></td>
                    <td class="px-4 py-2 text-center">
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">
                            <?= $row['porcentaje'] ?>%
                        </span>
                    </td>
                    <td class="px-4 py-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: <?= $row['porcentaje'] ?>%"></div>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>










    </div>
</body>
</html>
