<?php
// Importar la clase SimpleXLSX para manejar archivos Excel
use Shuchkin\SimpleXLSX;

// Configuración para mostrar errores (útil durante el desarrollo)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluir la librería necesaria para procesar archivos Excel
require_once 'SimpleXLSX.php';

// Establecer conexión con la base de datos MySQL
$mysqli = new mysqli('localhost', 'root', '', 'salud_puno'); // Cambiar por tu base de datos
$mysqli->set_charset('utf8mb4');

// Verificar si hay error en la conexión
$dbStatus = '';
$dbStatusClass = '';
$recordCount = 0;

if ($mysqli->connect_error) {
    $dbStatus = 'Error de conexión: ' . $mysqli->connect_error;
    $dbStatusClass = 'error';
} else {
    $dbStatus = '✅ Conexión exitosa a la base de datos';
    $dbStatusClass = 'success';
    
    // Contar registros existentes en la tabla
    $result = $mysqli->query("SELECT COUNT(*) as total FROM datos_salud");
    if ($result) {
        $row = $result->fetch_assoc();
        $recordCount = $row['total'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Datos de Salud desde Excel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.2em;
            margin-bottom: 10px;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 1.1em;
        }
        
        .content {
            padding: 40px;
        }
        
        .db-status {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 30px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .db-status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .db-status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .stats {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .stats h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .record-count {
            font-size: 2em;
            color: #3498db;
            font-weight: bold;
        }
        
        .upload-container {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            border: 2px dashed #dee2e6;
            transition: all 0.3s ease;
        }
        
        .upload-container:hover {
            border-color: #3498db;
            background: #f0f8ff;
        }
        
        .file-input-container {
            position: relative;
            margin-bottom: 20px;
        }
        
        .file-input-container input[type="file"] {
            position: absolute;
            left: -9999px;
        }
        
        .file-label {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.1em;
            font-weight: 500;
        }
        
        .file-label:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        
        .file-icon {
            font-size: 1.2em;
            margin-right: 10px;
        }
        
        .file-info {
            margin: 20px 0;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
        }
        
        .submit-btn:disabled {
            background: #95a5a6;
            cursor: not-allowed;
        }
        
        .progress-container {
            margin: 30px 0;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }
        
        .progress-bar-container {
            background: #e9ecef;
            border-radius: 20px;
            height: 10px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #3498db, #2ecc71);
            border-radius: 20px;
            transition: width 0.3s ease;
            width: 0%;
        }
        
        .progress-text {
            text-align: center;
            color: #2c3e50;
            font-weight: 500;
        }
        
        .result-container {
            margin-top: 30px;
        }
        
        .success, .error {
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .result-details {
            margin-top: 15px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .success-count {
            color: #27ae60;
            font-weight: bold;
        }
        
        .error-count {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .info-box {
            background: #e7f3ff;
            border: 1px solid #b8daff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .info-box h4 {
            color: #004085;
            margin-bottom: 10px;
        }
        
        .info-box ul {
            color: #004085;
            margin-left: 20px;
        }
        
        .info-box li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 Importar Datos de Salud</h1>
            <p>Sistema de importación de datos de hemoglobina y anemia</p>
        </div>
        
        <div class="content">
            <!-- Mensaje de estado de la base de datos -->
            <div class="db-status <?php echo $dbStatusClass; ?>">
                <?php echo $dbStatus; ?>
                <?php if ($dbStatusClass === 'success'): ?>
                    <span style="margin-left: auto;">🗄️ Tabla: datos_salud</span>
                <?php endif; ?>
            </div>
            
            <?php if ($dbStatusClass === 'success'): ?>
            <div class="stats">
                <h3>📊 Registros Actuales</h3>
                <div class="record-count"><?php echo number_format($recordCount); ?></div>
                <small>registros en la base de datos</small>
            </div>
            <?php endif; ?>
            
            <!-- Información sobre el formato esperado -->
            <div class="info-box">
                <h4>📋 Formato del Archivo Excel</h4>
                <ul>
                    <li><strong>Sexo:</strong> M o f </li>
                    <li><strong>Edad en meses:</strong> Número decimal </li>
                    <li><strong>Ubicación:</strong> Departamento, Provincia, Distrito </li>
                    <li><strong>Hemoglobina:</strong> Valor decimal</li>
                    <li><strong>Diagnóstico:</strong> Texto del diagnóstico de anemia </li>
                    <li><strong>Fecha:</strong> Fecha de hemoglobina</li>
                    <li><strong>Programas:</strong> Juntos, SIS, Suplementación, Consejería</li>
                </ul>
            </div>
            
            <!-- Contenedor para el formulario de carga -->
            <div class="upload-container">
                <form id="uploadForm" method="POST" enctype="multipart/form-data">
                    <div class="file-input-container">
                        <input type="file" id="excelFile" name="file" accept=".xlsx" required>
                        <label for="excelFile" class="file-label">
                            <span class="file-icon">📊</span>
                            <span class="file-text">Seleccionar archivo Excel (.xlsx)</span>
                        </label>
                    </div>
                    <div id="fileInfo" class="file-info" style="display: none;"></div>
                    <button type="submit" class="submit-btn" <?php echo $dbStatusClass === 'error' ? 'disabled' : ''; ?>>
                        🚀 Importar Datos de Salud
                    </button>
                </form>
            </div>

            <!-- Contenedor para la barra de progreso -->
            <div id="progress" class="progress-container" style="display: none;">
                <div class="progress-bar-container">
                    <div class="progress-bar"></div>
                </div>
                <div class="progress-text">Procesando datos de salud...</div>
            </div>

            <!-- Contenedor para mostrar resultados -->
            <div id="result" class="result-container"></div>
        </div>
    </div>

    <!-- Incluir el archivo JavaScript -->
    <script src="script.js"></script>
</body>
</html>