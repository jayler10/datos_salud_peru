<?php
// index.php - Archivo principal con routing interno
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once './controllers/StatsController.php';

// Obtener la sección actual
$section = $_GET['section'] ?? 'dashboard';

$controller = new StatsController($mysqli);

// Definir las secciones disponibles
$sections = [
    'dashboard' => 'Panel Principal',
    'demographics' => 'Demografía',
    'geography' => 'Geografía',
    'health' => 'Salud',
    'statistics' => 'Estadísticas Numéricas',
    'ranges' => 'Rangos y Clasificaciones',
    'establishments' => 'Establecimientos'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Análisis - Datos de Salud Puno</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-heartbeat"></i> Salud Puno</h2>
            </div>
            
            <ul class="sidebar-menu">
                <?php foreach($sections as $key => $title): ?>
                <li class="menu-item <?php echo $section === $key ? 'active' : ''; ?>">
                    <a href="#" onclick="loadSection('<?php echo $key; ?>')">
                        <i class="fas <?php echo getSectionIcon($key); ?>"></i>
                        <span><?php echo $title; ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <div class="sidebar-footer">
                <p><i class="fas fa-database"></i> Total: <?php echo number_format($controller->getTotalRecords()); ?> registros</p>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <header class="content-header">
                <h1 id="page-title"><?php echo $sections[$section]; ?></h1>
                <div class="header-actions">
                    <button class="btn btn-refresh" onclick="refreshData()">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                </div>
            </header>

            <div class="content-body" id="content-area">
                <?php include "views/{$section}.php"; ?>
            </div>
        </main>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loading-overlay">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Cargando datos...</p>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>