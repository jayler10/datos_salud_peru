// assets/js/app.js - JavaScript principal

class HealthStatsApp {
    constructor() {
        this.currentSection = 'dashboard';
        this.init();
    }

    init() {
        this.bindEvents();
        this.updateActiveMenu();
    }

    bindEvents() {
        // Event listeners para el menú
        document.addEventListener('DOMContentLoaded', () => {
            this.updateActiveMenu();
        });

        // Event listener para teclas de navegación
        document.addEventListener('keydown', (e) => {
            if (e.altKey) {
                const keyMap = {
                    '1': 'dashboard',
                    '2': 'demographics', 
                    '3': 'geography',
                    '4': 'health',
                    '5': 'statistics',
                    '6': 'ranges',
                    '7': 'establishments'
                };
                
                if (keyMap[e.key]) {
                    e.preventDefault();
                    this.loadSection(keyMap[e.key]);
                }
            }
        });
    }

    showLoading() {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.style.display = 'flex';
        }
    }

    hideLoading() {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }

    updateActiveMenu() {
        // Remover clase active de todos los elementos
        document.querySelectorAll('.menu-item').forEach(item => {
            item.classList.remove('active');
        });

        // Agregar clase active al elemento actual
        const activeItem = document.querySelector(`[onclick="loadSection('${this.currentSection}')"]`);
        if (activeItem) {
            activeItem.closest('.menu-item').classList.add('active');
        }
    }

    async loadSection(section) {
        if (section === this.currentSection) return;

        this.showLoading();
        
        try {
            // Simular delay para mostrar loading
            await new Promise(resolve => setTimeout(resolve, 300));

            const response = await fetch(`ajax/load_section.php?section=${section}`);
            const data = await response.json();

            if (data.success) {
                // Actualizar contenido
                document.getElementById('content-area').innerHTML = data.html;
                document.getElementById('page-title').textContent = data.title;
                
                // Actualizar estado
                this.currentSection = section;
                this.updateActiveMenu();
                
                // Actualizar URL sin recargar página
                const newUrl = `${window.location.pathname}?section=${section}`;
                window.history.pushState({section: section}, '', newUrl);
                
                // Aplicar animaciones
                document.getElementById('content-area').classList.add('fade-in');
                
                // Inicializar componentes específicos de la sección
                this.initSectionComponents(section);
                
            } else {
                throw new Error(data.message || 'Error al cargar la sección');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showError('Error al cargar los datos. Por favor, intente nuevamente.');
        } finally {
            this.hideLoading();
        }
    }

    initSectionComponents(section) {
        // Inicializar componentes específicos según la sección
        switch(section) {
            case 'dashboard':
                this.initDashboard();
                break;
            case 'statistics':
                this.initCharts();
                break;
            case 'ranges':
                this.initProgressBars();
                break;
        }
    }

    initDashboard() {
        // Animar contadores en el dashboard
        this.animateCounters();
    }

    initCharts() {
        // Inicializar gráficos si es necesario
        console.log('Inicializando gráficos...');
    }

    initProgressBars() {
        // Animar barras de progreso
        document.querySelectorAll('.progress-bar').forEach((bar, index) => {
            setTimeout(() => {
                const width = bar.getAttribute('data-width') || '0';
                bar.style.width = width + '%';
            }, index * 100);
        });
    }

    animateCounters() {
        document.querySelectorAll('.dashboard-card-value').forEach(counter => {
            const target = parseInt(counter.textContent.replace(/,/g, ''));
            const duration = 2000; // 2 segundos
            const start = 0;
            const increment = target / (duration / 16); // 60 FPS

            let current = start;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = Math.floor(current).toLocaleString();
            }, 16);
        });
    }

    showError(message) {
        const errorHtml = `
            <div class="stats-container">
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: var(--danger-color); margin-bottom: 20px;"></i>
                    <h3 style="color: var(--danger-color); margin-bottom: 15px;">Error</h3>
                    <p style="color: #6c757d;">${message}</p>
                    <button class="btn btn-refresh" onclick="refreshData()" style="margin-top: 20px;">
                        <i class="fas fa-sync-alt"></i> Reintentar
                    </button>
                </div>
            </div>
        `;
        document.getElementById('content-area').innerHTML = errorHtml;
    }

    async refreshData() {
        await this.loadSection(this.currentSection);
    }
}

// Instancia global de la aplicación
const app = new HealthStatsApp();

// Funciones globales para compatibilidad
function loadSection(section) {
    app.loadSection(section);
}

function refreshData() {
    app.refreshData();
}

// Manejar navegación del navegador
window.addEventListener('popstate', (event) => {
    if (event.state && event.state.section) {
        app.currentSection = event.state.section;
        app.loadSection(event.state.section);
    }
});

// Funciones de utilidad
function formatNumber(number, decimals = 0) {
    return new Intl.NumberFormat('es-ES', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(number);
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Copiado al portapapeles', 'success');
    }).catch(() => {
        showToast('Error al copiar', 'error');
    });
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <i class="fas ${getToastIcon(type)}"></i>
        <span>${message}</span>
    `;
    
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${getToastColor(type)};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    document.body.appendChild(toast);
    
    // Animar entrada
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 100);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

function getToastIcon(type) {
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    return icons[type] || icons.info;
}

function getToastColor(type) {
    const colors = {
        success: '#27ae60',
        error: '#e74c3c',
        warning: '#f39c12',
        info: '#3498db'
    };
    return colors[type] || colors.info;
}

// Funciones de exportación (para implementar en el futuro)
function exportToCSV(tableId) {
    showToast('Función de exportación en desarrollo', 'info');
}

function exportToPDF() {
    showToast('Función de exportación en desarrollo', 'info');
}

function printReport() {
    window.print();
}

// Debug mode
const DEBUG = false;

function log(...args) {
    if (DEBUG) {
        console.log('[HealthStats]', ...args);
    }
}

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    log('Aplicación inicializada');
    
    // Verificar si hay una sección en la URL
    const urlParams = new URLSearchParams(window.location.search);
    const sectionFromUrl = urlParams.get('section');
    
    if (sectionFromUrl && sectionFromUrl !== app.currentSection) {
        app.currentSection = sectionFromUrl;
        app.updateActiveMenu();
    }
});