document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('uploadForm');
    const fileInput = document.getElementById('excelFile');
    const fileInfo = document.getElementById('fileInfo');
    const progress = document.getElementById('progress');
    const progressBar = document.querySelector('.progress-bar');
    const progressText = document.querySelector('.progress-text');
    const result = document.getElementById('result');

    // Variable para controlar el estado de importación
    let importInProgress = false;

    // Manejar cambio de archivo
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validar extensión del archivo
            const validExtensions = ['.xlsx'];
            const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
            
            if (!validExtensions.includes(fileExtension)) {
                showFileInfo('❌ Por favor, seleccione un archivo Excel válido (.xlsx)', 'error');
                fileInput.value = '';
                return;
            }

            // Mostrar información del archivo
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            const lastModified = new Date(file.lastModified).toLocaleDateString('es-ES');
            
            showFileInfo(`
                <div style="text-align: left;">
                    <p><strong>📄 Archivo:</strong> ${file.name}</p>
                    <p><strong>📏 Tamaño:</strong> ${fileSize} MB</p>
                    <p><strong>📅 Modificado:</strong> ${lastModified}</p>
                    <p><strong>📊 Tipo:</strong> Datos de Salud y Hemoglobina</p>
                </div>
            `, 'success');
        } else {
            hideFileInfo();
        }
    });

    // Manejar envío del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const file = fileInput.files[0];
        if (!file) {
            showResult('❌ Por favor, seleccione un archivo Excel', 'error');
            return;
        }

        // Validar tamaño del archivo (máximo 50MB)
        const maxSize = 50 * 1024 * 1024; // 50MB
        if (file.size > maxSize) {
            showResult('❌ El archivo es demasiado grande. Máximo permitido: 50MB', 'error');
            return;
        }

        // Iniciar estado de importación
        importInProgress = true;
        window.addEventListener('beforeunload', preventUnload);

        // Mostrar barra de progreso
        showProgress();
        updateProgress(0, 'Preparando importación...');
        result.innerHTML = '';

        // Crear FormData y enviar archivo
        const formData = new FormData();
        formData.append('excelFile', file);

        // Simular progreso inicial
        let progressValue = 0;
        const progressMessages = [
            'Leyendo archivo Excel...',
            'Validando estructura de datos...',
            'Procesando registros de salud...',
            'Insertando datos en la base de datos...',
            'Verificando integridad de datos...',
            'Finalizando importación...'
        ];
        
        const progressInterval = setInterval(() => {
            if (progressValue <= 85) {
                progressValue += Math.random() * 15;
                const messageIndex = Math.floor((progressValue / 100) * progressMessages.length);
                updateProgress(
                    Math.min(progressValue, 85), 
                    progressMessages[Math.min(messageIndex, progressMessages.length - 1)]
                );
            }
        }, 500);

        // Enviar archivo al servidor
        fetch('import.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP Error: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            clearInterval(progressInterval);
            updateProgress(100, 'Completado');
            
            setTimeout(() => {
                hideProgress();
                
                if (data.success) {
                    const totalProcesados = data.total_procesados || (data.exitos + data.errores);
                    const exitosPorcentaje = totalProcesados > 0 ? ((data.exitos / totalProcesados) * 100).toFixed(1) : 0;
                    
                    showResult(`
                        <div class="success">
                            <h3>✅ Importación de Datos de Salud Completada</h3>
                            <div class="result-details">
                                <div>
                                    <p><strong>📊 Total procesados:</strong> ${totalProcesados.toLocaleString()}</p>
                                    <p><strong>✅ Registros exitosos:</strong> <span class="success-count">${data.exitos.toLocaleString()}</span></p>
                                </div>
                                <div>
                                    <p><strong>❌ Errores:</strong> <span class="error-count">${data.errores.toLocaleString()}</span></p>
                                    <p><strong>📈 Tasa de éxito:</strong> ${exitosPorcentaje}%</p>
                                </div>
                            </div>
                            ${data.errores > 0 ? `
                                <div style="margin-top: 15px; padding: 10px; background: #fff3cd; border-radius: 5px; color: #856404;">
                                    <small><strong>ℹ️ Nota:</strong> Los errores pueden deberse a datos faltantes, formatos incorrectos o registros duplicados.</small>
                                </div>
                            ` : ''}
                        </div>
                    `, 'success');
                    
                    // Limpiar formulario
                    fileInput.value = '';
                    hideFileInfo();
                    
                } else {
                    showResult(`
                        <div class="error">
                            <h3>❌ Error en la Importación</h3>
                            <p><strong>Mensaje:</strong> ${data.message}</p>
                            <div style="margin-top: 15px;">
                                <small><strong>💡 Sugerencias:</strong></small>
                                <ul style="margin-top: 5px; margin-left: 20px;">
                                    <li>Verifique que el archivo tenga el formato correcto</li>
                                    <li>Asegúrese de que las columnas estén en el orden esperado</li>
                                    <li>Revise que no haya datos corruptos o faltantes</li>
                                </ul>
                            </div>
                        </div>
                    `, 'error');
                }
                
                // Finalizar estado de importación
                importInProgress = false;
                window.removeEventListener('beforeunload', preventUnload);
                
            }, 1000);
        })
        .catch(error => {
            clearInterval(progressInterval);
            hideProgress();
            
            // Finalizar estado de importación en caso de error
            importInProgress = false;
            window.removeEventListener('beforeunload', preventUnload);
            
            console.error('Error:', error);
            showResult(`
                <div class="error">
                    <h3>❌ Error en el Proceso</h3>
                    <p><strong>Error:</strong> ${error.message}</p>
                    <div style="margin-top: 15px;">
                        <small><strong>🔧 Posibles soluciones:</strong></small>
                        <ul style="margin-top: 5px; margin-left: 20px;">
                            <li>Verifique su conexión a internet</li>
                            <li>Asegúrese de que el servidor esté funcionando</li>
                            <li>Intente con un archivo más pequeño</li>
                            <li>Contacte al administrador si el problema persiste</li>
                        </ul>
                    </div>
                </div>
            `, 'error');
        });
    });

    // Funciones auxiliares
    function showFileInfo(content, type = 'info') {
        fileInfo.innerHTML = content;
        fileInfo.style.display = 'block';
        fileInfo.className = `file-info ${type}`;
    }

    function hideFileInfo() {
        fileInfo.style.display = 'none';
    }

    function showProgress() {
        progress.style.display = 'block';
        progressBar.style.width = '0%';
    }

    function updateProgress(value, text) {
        progressBar.style.width = `${Math.min(value, 100)}%`;
        progressText.textContent = text;
    }

    function hideProgress() {
        progress.style.display = 'none';
    }

    function showResult(content, type) {
        result.innerHTML = content;
        result.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Función para prevenir que se cierre la página durante la importación
    function preventUnload(e) {
        if (importInProgress) {
            e.preventDefault();
            e.returnValue = '¿Está seguro de que desea salir? La importación está en progreso y se perderá.';
            return e.returnValue;
        }
    }

    // Observer para detectar cuando la importación termine
    const observer = new MutationObserver(() => {
        if (result.innerHTML.includes('Completada') || result.innerHTML.includes('Error en la Importación') || result.innerHTML.includes('Error en el Proceso')) {
            importInProgress = false;
            window.removeEventListener('beforeunload', preventUnload);
        }
    });

    // Iniciar observación del elemento result
    observer.observe(result, {
        childList: true,
        subtree: true
    });
});