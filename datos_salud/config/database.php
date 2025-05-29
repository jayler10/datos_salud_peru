<?php
// config/database.php - Configuración de la base de datos

// Mostrar errores (solo en desarrollo)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configuración de la base de datos
$db_config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'salud_puno',
    'charset' => 'utf8mb4'
];

try {
    // Crear conexión mysqli y asignar a $mysqli
    $mysqli = new mysqli(
        $db_config['host'],
        $db_config['username'],
        $db_config['password'],
        $db_config['database']
    );
    
    // Configurar charset
    $mysqli->set_charset($db_config['charset']);
    
    // Verificar conexión
    if ($mysqli->connect_error) {
        throw new Exception('Error de conexión: ' . $mysqli->connect_error);
    }

} catch (Exception $e) {
    die('Error al conectar con la base de datos: ' . $e->getMessage());
}

// Función para cerrar la conexión
function closeDatabase() {
    global $mysqli;
    if ($mysqli) {
        $mysqli->close();
    }
}

// Registrar función de cierre
register_shutdown_function('closeDatabase');
