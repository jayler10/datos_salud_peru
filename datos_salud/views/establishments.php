<?php
// establishments.php - Interface para establecimientos
require_once '../controllers/StatsController.php';
require_once '../config/database.php';

$statsController = new StatsController($mysqli);
$topEstablishments = $statsController->getTopEstablishments(15);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Establecimientos de Salud</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">🏥 Top Establecimientos de Salud</h1>
        
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold text-blue-600">Establecimientos con Mayor Cantidad de Registros</h2>
                <p class="text-gray-600 mt-2">Mostrando los 15 establecimientos principales</p>
            </div>
            
            <div class="p-6">
                <div class="space-y-4">
                    <?php 
                    $rank = 1;
                    while ($row = $topEstablishments->fetch_assoc()): 
                    ?>
                    <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                    <?= $rank ?>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900"><?= htmlspecialchars($row['establecimiento']) ?></h3>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <div class="text-lg font-bold text-gray-900"><?= number_format($row['cantidad']) ?></div>
                                <div class="text-sm text-gray-500">registros</div>
                            </div>
                            <div class="text-right">
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                    <?= $row['porcentaje'] ?>%
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php 
                    $rank++;
                    endwhile; 
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>