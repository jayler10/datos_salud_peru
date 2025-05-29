<?php
// ajax/load_section.php - Cargador de secciones vía AJAX

require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../controllers/StatsController.php';

// Configurar headers para JSON
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    // Obtener la sección solicitada
    $section = $_GET['section'] ?? 'dashboard';
    
    // Validar sección
    $validSections = ['dashboard', 'demographics', 'geography', 'health', 'statistics', 'ranges', 'establishments'];
    
    if (!in_array($section, $validSections)) {
        throw new Exception('Sección no válida');
    }
    
    // Crear controlador
    $controller = new StatsController($mysqli);
    
    // Obtener títulos de secciones
    $sectionTitles = [
        'dashboard' => 'Panel Principal',
        'demographics' => 'Análisis Demográfico',
        'geography' => 'Distribución Geográfica',
        'health' => 'Indicadores de Salud',
        'statistics' => 'Estadísticas Numéricas',
        'ranges' => 'Rangos y Clasificaciones',
        'establishments' => 'Establecimientos de Salud'
    ];
    
    // Capturar el contenido de la vista
    ob_start();
    include "../views/{$section}.php";
    $html = ob_get_clean();
    
    // Devolver respuesta JSON
    echo json_encode([
        'success' => true,
        'html' => $html,
        'title' => $sectionTitles[$section],
        'section' => $section
    ]);
    
} catch (Exception $e) {
    // Error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => true
    ]);
}
?>