// Componente para manejar eventos de redimensionamiento y debugging
Alpine.data('responsiveDebug', () => ({
    screenSize: '',
    width: window.innerWidth,
    height: window.innerHeight,
    breakpoints: {
        'sm': 640,
        'md': 768,
        'lg': 1024,
        'xl': 1280,
        '2xl': 1536
    },

    init() {
        this.updateScreenSize();

        // Escuchar el evento personalizado de redimensionamiento
        window.addEventListener('app:resize', (event) => {
            this.width = event.detail.width;
            this.height = event.detail.height;
            this.updateScreenSize();

            // Evento para que los componentes Alpine puedan reaccionar
            this.$dispatch('screen:resized', {
                width: this.width,
                height: this.height,
                size: this.screenSize
            });
        });
    },

    updateScreenSize() {
        const width = this.width;

        if (width < this.breakpoints.sm) {
            this.screenSize = 'xs';
        } else if (width < this.breakpoints.md) {
            this.screenSize = 'sm';
        } else if (width < this.breakpoints.lg) {
            this.screenSize = 'md';
        } else if (width < this.breakpoints.xl) {
            this.screenSize = 'lg';
        } else if (width < this.breakpoints['2xl']) {
            this.screenSize = 'xl';
        } else {
            this.screenSize = '2xl';
        }

        console.log(`Pantalla redimensionada: ${this.width}x${this.height} (${this.screenSize})`);
    }
}));
