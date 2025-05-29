<?php
// includes/functions.php - Funciones auxiliares

/**
 * Formatear números con separadores de miles y decimales
 */
function formatearNumero($numero, $decimales = 2) {
    if ($numero === null || $numero === '') {
        return 'N/A';
    }
    return number_format($numero, $decimales, '.', ',');
}

/**
 * Obtener icono para cada sección del menú
 */
function getSectionIcon($section) {
    $icons = [
        'dashboard' => 'fa-tachometer-alt',
        'demographics' => 'fa-users',
        'geography' => 'fa-map-marked-alt',
        'health' => 'fa-user-md',
        'statistics' => 'fa-chart-bar',
        'ranges' => 'fa-layer-group',
        'establishments' => 'fa-hospital'
    ];
    
    return $icons[$section] ?? 'fa-circle';
}

/**
 * Sanitizar datos para evitar XSS
 */
function sanitizeOutput($data) {
    if (is_array($data)) {
        return array_map('sanitizeOutput', $data);
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Obtener clase CSS según el tipo de anemia
 */
function getAnemiaClass($diagnostico) {
    $diagnostico = strtolower($diagnostico);
    
    if (strpos($diagnostico, 'normal') !== false) {
        return 'anemia-normal';
    } elseif (strpos($diagnostico, 'leve') !== false) {
        return 'anemia-leve';
    } elseif (strpos($diagnostico, 'moderada') !== false) {
        return 'anemia-moderada';
    } elseif (strpos($diagnostico, 'severa') !== false) {
        return 'anemia-severa';
    }
    
    return '';
}

/**
 * Formatear sexo para mostrar
 */
function formatSex($sex) {
    return $sex === 'M' ? 'Masculino' : 'Femenino';
}

/**
 * Generar respuesta JSON para AJAX
 */
function jsonResponse($data, $success = true, $message = '') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

/**
 * Validar parámetros de entrada
 */
function validateInput($input, $type = 'string') {
    switch ($type) {
        case 'int':
            return filter_var($input, FILTER_VALIDATE_INT);
        case 'float':
            return filter_var($input, FILTER_VALIDATE_FLOAT);
        case 'email':
            return filter_var($input, FILTER_VALIDATE_EMAIL);
        default:
            return trim(strip_tags($input));
    }
}

/**
 * Obtener color para gráficos según el valor
 */
function getChartColor($index) {
    $colors = [
        '#3498db', '#e74c3c', '#2ecc71', '#f39c12',
        '#9b59b6', '#1abc9c', '#34495e', '#e67e22',
        '#95a5a6', '#f1c40f', '#8e44ad', '#16a085'
    ];
    
    return $colors[$index % count($colors)];
}

/**
 * Calcular percentiles
 */
function calculatePercentile($values, $percentile) {
    sort($values);
    $count = count($values);
    $index = ($percentile / 100) * ($count - 1);
    
    if (floor($index) == $index) {
        return $values[$index];
    } else {
        $lower = $values[floor($index)];
        $upper = $values[ceil($index)];
        $fraction = $index - floor($index);
        return $lower + ($fraction * ($upper - $lower));
    }
}
?>