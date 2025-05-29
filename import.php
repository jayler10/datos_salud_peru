<?php
// Establecer el tipo de contenido como JSON para la respuesta
header('Content-Type: application/json');

// Importar la clase SimpleXLSX para manejar archivos Excel
use Shuchkin\SimpleXLSX;

// Configuración para mostrar errores (útil durante el desarrollo)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluir la librería necesaria para procesar archivos Excel
require_once 'SimpleXLSX.php';

// Establecer conexión con la base de datos MySQL
$mysqli = new mysqli('localhost', 'root', '', 'salud_puno');
$mysqli->set_charset('utf8mb4');

// Verificar si hay error en la conexión
if ($mysqli->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión a la base de datos'
    ]);
    exit;
}

function procesarFecha($fecha_str) {
    if (empty($fecha_str)) {
        return null;
    }
    
    $fecha_str = trim($fecha_str);
    
    // Si ya está en formato YYYY-MM-DD directo (como 2006-09-14), validar y retornar
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_str)) {
        $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha_str);
        if ($fecha_obj && $fecha_obj->format('Y-m-d') === $fecha_str) {
            return $fecha_str;
        }
    }
    
    // Manejar formato DD/MM/YYYY (como 26/01/2024)
    if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}/', $fecha_str)) {
        $fecha_limpia = preg_replace('/\s+\d{2}:\d{2}:\d{2}$/', '', $fecha_str);
        $fecha_obj = DateTime::createFromFormat('d/m/Y', $fecha_limpia);
        if ($fecha_obj) {
            return $fecha_obj->format('Y-m-d');
        }
    }
    
    // Manejar formato M/D/YYYY (como 1/26/2024)
    if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $fecha_str)) {
        $fecha_obj = DateTime::createFromFormat('n/j/Y', $fecha_str);
        if ($fecha_obj) {
            return $fecha_obj->format('Y-m-d');
        }
    }
    
    return null;
}

// Función para reiniciar el AUTO_INCREMENT si la tabla está vacía
function reiniciarAutoIncrement($mysqli) {
    $result = $mysqli->query("SELECT COUNT(*) as count FROM datos_salud");
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        $mysqli->query("ALTER TABLE datos_salud AUTO_INCREMENT = 1");
        error_log("AUTO_INCREMENT reiniciado a 1 - tabla vacía");
    }
}

// Inicializar contadores para el proceso de importación
$exitos = 0;
$errores = 0;

// Verificar si se ha subido un archivo
if (isset($_FILES['excelFile'])) {
    // Reiniciar AUTO_INCREMENT si es necesario
    reiniciarAutoIncrement($mysqli);
    
    // Intentar leer y procesar el archivo Excel
    if ($xlsx = SimpleXLSX::parse($_FILES['excelFile']['tmp_name'])) {
        // Obtener dimensiones del archivo Excel
        $dim = $xlsx->dimension();
        $cols = $dim[0];
        $totalRows = count($xlsx->rows());
        $currentRow = 0;

        // Procesar cada fila del archivo Excel
        foreach ($xlsx->readRows() as $k => $r) {
            $currentRow++;
            
            // Omitir la primera fila (encabezados)
            if ($k == 0) continue;

            // Validar que la fila tenga suficientes columnas
            if (count($r) < 36) {
                $errores++;
                error_log("Fila " . ($k + 1) . " - Error: Fila incompleta, solo tiene " . count($r) . " columnas");
                continue;
            }

            // Extraer datos según la estructura del Excel
            $sexo = isset($r[10]) ? trim($r[10]) : '';
            $departamento = isset($r[30]) ? trim($r[30]) : '';
            $provincia = isset($r[31]) ? trim($r[31]) : '';
            $distrito = isset($r[32]) ? trim($r[32]) : '';
            $altura_msnm = isset($r[33]) ? intval($r[33]) : null;
            $hemoglobina = isset($r[34]) ? floatval($r[34]) : null;
            $dx_anemia = isset($r[35]) ? trim($r[35]) : '';
            $establecimiento = isset($r[4]) ? trim($r[4]) : '';
            
            // Debug: mostrar todas las columnas de las primeras 3 filas de datos
            if ($k <= 3) {
                error_log("DEBUG - Fila " . ($k + 1) . " - Datos extraídos:");
                error_log("  Sexo (col 10): '$sexo'");
                error_log("  Departamento (col 30): '$departamento'");
                error_log("  Provincia (col 31): '$provincia'");
                error_log("  Distrito (col 32): '$distrito'");
                error_log("  Establecimiento (col 4): '$establecimiento'");
                error_log("  Altura (col 33): '$altura_msnm'");
                error_log("  Hemoglobina (col 34): '$hemoglobina'");
                error_log("  Dx Anemia (col 35): '$dx_anemia'");
                
                // Debug adicional: mostrar valores raw de la fila
                error_log("  DEBUG - Valores raw alrededor del distrito:");
                error_log("    r[30] (depto): '" . (isset($r[30]) ? $r[30] : 'NO_EXISTE') . "'");
                error_log("    r[31] (prov): '" . (isset($r[31]) ? $r[31] : 'NO_EXISTE') . "'");
                error_log("    r[32] (dist): '" . (isset($r[32]) ? $r[32] : 'NO_EXISTE') . "'");
                error_log("    r[33] (alt): '" . (isset($r[33]) ? $r[33] : 'NO_EXISTE') . "'");
            }
            
            // Procesar fecha de nacimiento (columna 28)
            $fecha_nacimiento = null;
            if (isset($r[28]) && !empty($r[28])) {
                $fecha_nacimiento = procesarFecha($r[28]);
                if ($k <= 3) {
                    error_log("  Fecha Nacimiento (col 28): '" . $r[28] . "' -> '$fecha_nacimiento'");
                }
            }

            // Validaciones básicas
            if (empty($sexo) || empty($departamento)) {
                error_log("Fila " . ($k + 1) . " - Error de validación: sexo='$sexo', departamento='$departamento'");
                $errores++;
                continue;
            }
            
            // Validación adicional para distrito
            if (empty($distrito)) {
                error_log("Fila " . ($k + 1) . " - Advertencia: distrito vacío, se guardará como cadena vacía");
                $distrito = ''; // Asegurar que sea string vacío en lugar de null
            }

            // Normalizar sexo
            $sexo_original = $sexo;
            $sexo = strtoupper(substr($sexo, 0, 1));
            if ($sexo !== 'M' && $sexo !== 'F') {
                // Intentar mapear valores comunes
                $sexo_lower = strtolower($sexo_original);
                if (in_array($sexo_lower, ['masculino', 'hombre', 'male', '1'])) {
                    $sexo = 'M';
                } elseif (in_array($sexo_lower, ['femenino', 'mujer', 'female', '2'])) {
                    $sexo = 'F';
                } else {
                    error_log("Fila " . ($k + 1) . " - Sexo inválido: '$sexo_original'");
                    $errores++;
                    continue;
                }
            }

            // Validar que hemoglobina sea un valor válido si existe
            if ($hemoglobina !== null && ($hemoglobina < 0 || $hemoglobina > 25)) {
                error_log("Fila " . ($k + 1) . " - Hemoglobina fuera de rango: $hemoglobina, se establecerá como NULL");
                $hemoglobina = null;
            }

            // Validar que altura sea un valor válido si existe
            if ($altura_msnm !== null && ($altura_msnm < 0 || $altura_msnm > 6000)) {
                error_log("Fila " . ($k + 1) . " - Altura fuera de rango: $altura_msnm, se establecerá como NULL");
                $altura_msnm = null;
            }

            // Preparar e ejecutar la consulta SQL para insertar los datos
            $stmt = $mysqli->prepare("INSERT INTO datos_salud (
                sexo, fecha_nacimiento, departamento, provincia, distrito, 
                altura_msnm, hemoglobina, dx_anemia, establecimiento
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            if ($stmt) {
                // Debug: mostrar los valores antes del bind
                if ($k <= 3) {
                    error_log("  DEBUG - Valores antes del bind:");
                    error_log("    sexo: '$sexo' (tipo: " . gettype($sexo) . ")");
                    error_log("    fecha_nacimiento: '$fecha_nacimiento' (tipo: " . gettype($fecha_nacimiento) . ")");
                    error_log("    departamento: '$departamento' (tipo: " . gettype($departamento) . ")");
                    error_log("    provincia: '$provincia' (tipo: " . gettype($provincia) . ")");
                    error_log("    distrito: '$distrito' (tipo: " . gettype($distrito) . ")");
                    error_log("    altura_msnm: '$altura_msnm' (tipo: " . gettype($altura_msnm) . ")");
                    error_log("    hemoglobina: '$hemoglobina' (tipo: " . gettype($hemoglobina) . ")");
                    error_log("    dx_anemia: '$dx_anemia' (tipo: " . gettype($dx_anemia) . ")");
                    error_log("    establecimiento: '$establecimiento' (tipo: " . gettype($establecimiento) . ")");
                }
                
                $stmt->bind_param('sssssisss', 
                    $sexo, $fecha_nacimiento, $departamento, $provincia, $distrito,
                    $altura_msnm, $hemoglobina, $dx_anemia, $establecimiento
                );
                
                if ($stmt->execute()) {
                    $exitos++;
                    if ($k <= 3) {
                        error_log("Fila " . ($k + 1) . " - Inserción exitosa - ID: " . $mysqli->insert_id);
                        error_log("  Datos insertados: sexo='$sexo', depto='$departamento', prov='$provincia', dist='$distrito'");
                    }
                } else {
                    $errores++;
                    error_log("Fila " . ($k + 1) . " - Error SQL: " . $stmt->error);
                    error_log("  Datos que intentaban insertarse: sexo='$sexo', depto='$departamento', prov='$provincia', dist='$distrito'");
                }
                $stmt->close();
            } else {
                $errores++;
                error_log("Fila " . ($k + 1) . " - Error preparando statement: " . $mysqli->error);
            }
        }

        // Devolver resultado exitoso con estadísticas
        echo json_encode([
            'success' => true,
            'message' => "Importación finalizada. Registros exitosos: $exitos, Errores: $errores",
            'exitos' => $exitos,
            'errores' => $errores,
            'total_procesados' => $exitos + $errores
        ]);
    } else {
        // Devolver error si no se puede procesar el archivo Excel
        echo json_encode([
            'success' => false,
            'message' => 'Error al procesar el archivo Excel: ' . SimpleXLSX::parseError()
        ]);
    }
} else {
    // Devolver error si no se subió ningún archivo
    echo json_encode([
        'success' => false,
        'message' => 'No se ha subido ningún archivo'
    ]);
}

// Cerrar la conexión a la base de datos
$mysqli->close();
?>