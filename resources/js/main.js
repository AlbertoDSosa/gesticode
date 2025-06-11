// Este archivo implementa las funcionalidades principales de la aplicación usando Alpine.js
// La implementación se ha movido principalmente a /resources/js/alpine.js

// Este archivo se mantiene para la compatibilidad con el código existente
// y proporciona una capa de compatibilidad jQuery para componentes antiguos

document.addEventListener('livewire:navigated', () => {
    // Inicializar los botones de tema después de la navegación sin recargar la página
    initThemeButtons();

    // Inicializar SimpleBar para elementos que lo necesitan
    initializeSimpleBar();

    // Inicializar las barras de progreso
    initProgressBars();

    // Inicializar los botones de radio del tema
    setupEventListeners();

    // Obtener el año actual
    document.getElementById('thisYear').textContent = new Date().getFullYear();

    console.log('Livewire navegación completada');
});

// Inicializar los botones de radio del tema
function initThemeButtons() {
    const themes = [
        { name: "dark", class: "dark" },
        { name: "semiDark", class: "semiDark" },
        { name: "light", class: "light" }
    ];

    const currentTheme = localStorage.getItem("theme") || 'light';

    themes.forEach((theme) => {
        const radioBtn = document.getElementById(theme.class);
        if (radioBtn) {
            radioBtn.checked = (theme.name === currentTheme);

            // Quitar listeners anteriores (para evitar duplicados)
            const newRadio = radioBtn.cloneNode(true);
            radioBtn.parentNode.replaceChild(newRadio, radioBtn);

            // Añadir nuevo listener
            newRadio.addEventListener('change', function() {
                if (this.checked) {
                    const themeManager = Alpine.store('themeState') || {};
                    if (themeManager.setTheme) {
                        themeManager.setTheme(theme.name);
                    } else {
                        // Fallback si Alpine store no está disponible
                        localStorage.theme = theme.name;
                        location.reload();
                    }
                }
            });
        }
    });
}

// Inicializa SimpleBar para elementos de scroll personalizados
function initializeSimpleBar() {
    const elements = document.querySelectorAll("#sidebar_menus, #scrollModal");
    elements.forEach(element => {
        if (element && !element.SimpleBar) {
            new SimpleBar(element);
        }
    });
}

// Inicializa las barras de progreso
function initProgressBars() {
    const progressBars = document.querySelectorAll('.progress-bar, .progress-bar2, .progress-bar3, .progress-bar4, .progress-bar5, .progress-bar6');

    progressBars.forEach((bar, index) => {
        // Obtener el valor de ancho deseado basado en la clase
        let targetWidth;
        if (bar.classList.contains('progress-bar')) targetWidth = '40%';
        else if (bar.classList.contains('progress-bar2')) targetWidth = '50%';
        else if (bar.classList.contains('progress-bar3')) targetWidth = '60%';
        else if (bar.classList.contains('progress-bar4')) targetWidth = '75%';
        else if (bar.classList.contains('progress-bar5')) targetWidth = '95%';
        else if (bar.classList.contains('progress-bar6')) targetWidth = '25%';
        else targetWidth = '0%';

        // Animación básica de la barra de progreso
        bar.style.transition = 'width 2.5s ease';
        bar.style.width = '0';

        // Disparar la animación después de un pequeño retraso
        setTimeout(() => {
            bar.style.width = targetWidth;
        }, 100);
    });
}

// Función para aplicar event listeners después de una navegación de Livewire
function setupEventListeners() {
    // Controlador para los botones de dispositivos pequeños
    document.querySelectorAll('.smallDeviceMenuController').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelector('.sidebar-wrapper').classList.add('sidebar-open');
            document.querySelector('#bodyOverlay').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        });
    });

    document.querySelectorAll('.sidebarCloseIcon, #bodyOverlay').forEach(element => {
        element.addEventListener('click', function() {
            document.querySelector('.sidebar-wrapper').classList.remove('sidebar-open');
            document.querySelector('#bodyOverlay').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        });
    });

    // Controlador para toggle de contraseña
    const toggleIcon = document.getElementById('toggleIcon');
    if (toggleIcon) {
        toggleIcon.addEventListener('click', function() {
            const passwordFields = document.querySelectorAll('.passwordfield');
            const hidePassword = document.getElementById('hidePassword');
            const showPassword = document.getElementById('showPassword');

            passwordFields.forEach(field => {
                if (field.type === 'password') {
                    field.type = 'text';
                    hidePassword.style.display = 'none';
                    showPassword.style.display = 'block';
                } else {
                    field.type = 'password';
                    showPassword.style.display = 'none';
                    hidePassword.style.display = 'block';
                }
            });
        });
    }
}
