// Error Logger para Alpine.js y problemas de redimensionamiento
export const setupErrorLogger = () => {
    // Captura errores no manejados
    window.addEventListener('error', (event) => {
        console.error('Error capturado:', event.error);
        logErrorToUI(event.error.message, event.error.stack);
    });

    // Captura promesas rechazadas
    window.addEventListener('unhandledrejection', (event) => {
        console.error('Promesa rechazada:', event.reason);
        logErrorToUI('Promesa rechazada: ' + event.reason, event.reason.stack);
    });

    // Logger específico para Alpine
    if (window.Alpine) {
        const originalAlpineInit = window.Alpine.start;
        window.Alpine.start = function() {
            try {
                return originalAlpineInit.apply(this, arguments);
            } catch (error) {
                console.error('Error en Alpine.start:', error);
                logErrorToUI('Error en Alpine: ' + error.message, error.stack);
                throw error;
            }
        };
    }

    // Eventos de redimensionamiento
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            try {
                // Disparar evento personalizado que pueda ser escuchado por Alpine
                window.dispatchEvent(new CustomEvent('app:resize', {
                    detail: {
                        width: window.innerWidth,
                        height: window.innerHeight
                    }
                }));
                console.log('Ventana redimensionada:', window.innerWidth, 'x', window.innerHeight);
            } catch (error) {
                console.error('Error durante el redimensionamiento:', error);
                logErrorToUI('Error en resize: ' + error.message, error.stack);
            }
        }, 250); // Debounce para evitar múltiples llamadas
    });
};

// Crear UI para mostrar errores
const logErrorToUI = (message, stack) => {
    // Crear el contenedor de errores si no existe
    let errorContainer = document.getElementById('alpine-error-logger');
    if (!errorContainer) {
        errorContainer = document.createElement('div');
        errorContainer.id = 'alpine-error-logger';
        errorContainer.style.cssText = `
            position: fixed;
            bottom: 0;
            right: 0;
            width: 350px;
            max-height: 300px;
            overflow-y: auto;
            background-color: rgba(0, 0, 0, 0.8);
            color: #ff5555;
            z-index: 9999;
            font-family: monospace;
            font-size: 12px;
            padding: 10px;
            border-top-left-radius: 5px;
        `;

        const header = document.createElement('div');
        header.style.cssText = `
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            border-bottom: 1px solid #ff5555;
            padding-bottom: 5px;
        `;

        const title = document.createElement('span');
        title.textContent = 'Errores detectados';
        title.style.fontWeight = 'bold';

        const closeButton = document.createElement('button');
        closeButton.textContent = 'X';
        closeButton.style.cssText = `
            background: none;
            border: none;
            color: #ff5555;
            cursor: pointer;
        `;
        closeButton.addEventListener('click', () => {
            errorContainer.style.display = 'none';
        });

        header.appendChild(title);
        header.appendChild(closeButton);
        errorContainer.appendChild(header);

        document.body.appendChild(errorContainer);
    }

    // Añadir el mensaje de error
    const errorItem = document.createElement('div');
    errorItem.style.cssText = `
        margin-bottom: 5px;
        padding: 5px;
        border-left: 3px solid #ff5555;
    `;

    const timestamp = new Date().toLocaleTimeString();
    const msgElement = document.createElement('div');
    msgElement.textContent = `[${timestamp}] ${message}`;
    errorItem.appendChild(msgElement);

    if (stack) {
        const stackElement = document.createElement('div');
        stackElement.style.cssText = `
            margin-top: 5px;
            padding-left: 10px;
            font-size: 10px;
            color: #aaa;
            max-height: 100px;
            overflow-y: auto;
            white-space: pre-wrap;
        `;
        stackElement.textContent = stack;
        errorItem.appendChild(stackElement);
    }

    errorContainer.appendChild(errorItem);
    errorContainer.style.display = 'block';
};
