

// Implementación del componente responsiveDebug
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

        // Debounce para el evento resize
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                this.width = window.innerWidth;
                this.height = window.innerHeight;
                this.updateScreenSize();

                // Disparar evento personalizado
                window.dispatchEvent(new CustomEvent('app:resize', {
                    detail: {
                        width: this.width,
                        height: this.height,
                        size: this.screenSize
                    }
                }));
            }, 250);
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

// Componente para la gestión del tema
Alpine.data('themeManager', () => ({
    currentTheme: localStorage.getItem('theme') || 'light',
    grayScale: localStorage.getItem('effect') === 'grayScale',

    // Inicialización
    init() {
        this.applyTheme();
        this.applyGrayScale();

        document.addEventListener('livewire:navigating', () => {
            // Guardar el estado del tema para mantener la coherencia durante la navegación
            localStorage.theme = this.currentTheme;
        });
    },

    // Cambiar entre temas dark/light
    toggleTheme() {
        this.currentTheme = this.currentTheme === 'light' ? 'dark' : 'light';
        localStorage.theme = this.currentTheme;
        this.applyTheme();
    },

    // Cambiar a un tema específico
    setTheme(theme) {
        this.currentTheme = theme;
        localStorage.theme = theme;
        this.applyTheme();
    },

    // Aplicar el tema actual al DOM
    applyTheme() {
        const html = document.documentElement;
        const body = document.body;

        // Eliminar todas las clases de tema
        html.classList.remove('light', 'dark', 'semiDark');
        body.classList.remove('light', 'dark', 'semiDark');

        // Aplicar el tema actual
        if (this.currentTheme) {
            html.classList.add(this.currentTheme);
            body.classList.add(this.currentTheme);
        }
    },

    // Activar/desactivar el modo escala de grises
    toggleGrayScale() {
        this.grayScale = !this.grayScale;
        localStorage.effect = this.grayScale ? 'grayScale' : '';
        this.applyGrayScale();
    },

    // Aplicar escala de grises
    applyGrayScale() {
        if (this.grayScale) {
            document.documentElement.classList.add('grayscale');
        } else {
            document.documentElement.classList.remove('grayscale');
        }
    }
}));

// Componente para la gestión del sidebar
Alpine.data('sidebarManager', () => ({
    collapsed: localStorage.getItem('sideBarType') === 'collapsed',
    hidden: false,
    debounceTimer: null,

    init() {
        this.applySidebarState();
        this.checkScreenSize();

        // Usar evento personalizado en lugar de resize directo
        window.addEventListener('app:resize', (event) => {
            this.checkScreenSize();

            // Auto-colapsar en pantallas pequeñas
            if (event.detail.width < 1281 && !this.collapsed) {
                this.closeSidebar();
            } else if (event.detail.width >= 1281 && localStorage.getItem('sideBarType') !== 'collapsed' && this.collapsed) {
                this.openSidebar();
            }
        });
    },

    toggleSidebar() {
        this.collapsed = !this.collapsed;
        localStorage.setItem('sideBarType', this.collapsed ? 'collapsed' : 'extend');
        this.applySidebarState();
    },

    toggleSidebarVisibility() {
        this.hidden = !this.hidden;
        this.applySidebarState();
    },

    openSidebar() {
        this.collapsed = false;
        localStorage.setItem('sideBarType', 'extend');
        this.applySidebarState();
    },

    closeSidebar() {
        this.collapsed = true;
        localStorage.setItem('sideBarType', 'collapsed');
        this.applySidebarState();
    },

    applySidebarState() {
        try {
            const appWrapper = document.querySelector('.app-wrapper');
            if (!appWrapper) {
                console.warn('No se encontró el elemento .app-wrapper');
                return;
            }

            // Eliminar todas las clases primero
            appWrapper.classList.remove('collapsed', 'extend', 'menu-hide');

            // Aplicar el estado actual
            if (this.collapsed) {
                appWrapper.classList.add('collapsed');
            } else {
                appWrapper.classList.add('extend');
            }

            if (this.hidden) {
                appWrapper.classList.add('menu-hide');
            }
        } catch (error) {
            console.error('Error al aplicar el estado del sidebar:', error);
        }
    },

    checkScreenSize() {
        try {
            const width = window.innerWidth;

            console.log('Verificando tamaño de pantalla:', width);

            // Elementos de DOM que necesitamos actualizar
            const elements = {
                header: document.querySelector(".app-header"),
                footer: document.querySelector(".site-footer"),
                content: document.querySelector("#content_wrapper"),
                closeIcon: document.querySelector(".sidebarCloseIcon"),
                sidebarType: document.querySelector("#sidebar_type"),
                overlay: document.querySelector("#bodyOverlay")
            };

            // Verificar que todos los elementos existen
            Object.entries(elements).forEach(([key, el]) => {
                if (!el) {
                    console.warn(`Elemento no encontrado: ${key}`);
                }
            });

            if (width < 1281) {
                elements.header?.classList.add("margin-0");
                elements.footer?.classList.add("margin-0");
                elements.content?.classList.add("margin-0");
                if (elements.closeIcon) elements.closeIcon.style.display = 'block';
                if (elements.sidebarType) elements.sidebarType.style.display = 'none';
                elements.overlay?.classList.add("block");
            } else {
                elements.header?.classList.remove("margin-0");
                elements.footer?.classList.remove("margin-0");
                elements.content?.classList.remove("margin-0");
                if (elements.closeIcon) elements.closeIcon.style.display = 'none';
                if (elements.sidebarType) elements.sidebarType.style.display = 'block';
                elements.overlay?.classList.remove("block");
            }
        } catch (error) {
            console.error('Error en checkScreenSize:', error);
        }
    }
}));

// Componente para la gestión del menú sidebar
Alpine.data('sidebarMenu', () => ({
    activeMenu: null,
    activeSubMenu: null,

    init() {
        // Intentar restaurar el menú activo al cargar
        this.activeMenu = localStorage.getItem('activeMenu');
        this.activeSubMenu = localStorage.getItem('activeSubMenu');

        document.addEventListener('livewire:navigating', () => {
            // Guardar el estado del menú para mantener la coherencia durante la navegación
            if (this.activeMenu) localStorage.setItem('activeMenu', this.activeMenu);
            if (this.activeSubMenu) localStorage.setItem('activeSubMenu', this.activeSubMenu);
        });
    },

    toggleSubmenu(menu) {
        if (this.activeMenu === menu) {
            this.activeMenu = null;
            localStorage.removeItem('activeMenu');
        } else {
            this.activeMenu = menu;
            localStorage.setItem('activeMenu', menu);
        }
    },

    setActiveSubMenu(submenu) {
        this.activeSubMenu = submenu;
        localStorage.setItem('activeSubMenu', submenu);
    },

    isActive(menu) {
        return this.activeMenu === menu;
    },

    isSubActive(submenu) {
        return this.activeSubMenu === submenu;
    }
}));

// Componente para la gestión de layouts
Alpine.data('layoutManager', () => ({
    // Guardar la configuración en localStorage
    direction: localStorage.getItem('direction') || 'ltr',
    contentLayout: localStorage.getItem('contentLayout') || 'layout-full',
    menuLayout: localStorage.getItem('menuLayout') || '',
    navbarStyle: localStorage.getItem('navbarStyle') || 'sticky',
    footerStyle: localStorage.getItem('footerStyle') || 'static',

    init() {
        this.applyLayoutSettings();

        document.addEventListener('livewire:navigating', () => {
            // Guardar la configuración de layout para mantenerla durante la navegación
            localStorage.setItem('direction', this.direction);
            localStorage.setItem('contentLayout', this.contentLayout);
            localStorage.setItem('menuLayout', this.menuLayout);
            localStorage.setItem('navbarStyle', this.navbarStyle);
            localStorage.setItem('footerStyle', this.footerStyle);
        });

        window.addEventListener('app:resize', () => {
            this.applyLayoutSettings();
        });
    },

    // Cambiar la dirección (RTL/LTR)
    setDirection(dir) {
        this.direction = dir;
        localStorage.setItem('direction', dir);
        this.applyLayoutSettings();
    },

    // Aplicar el diseño de contenido
    setContentLayout(layout) {
        this.contentLayout = layout;
        localStorage.setItem('contentLayout', layout);
        this.applyLayoutSettings();
    },

    // Cambiar el estilo del menú
    setMenuLayout(layout) {
        this.menuLayout = layout;
        localStorage.setItem('menuLayout', layout);
        this.applyLayoutSettings();
    },

    // Cambiar el estilo de la barra de navegación
    setNavbarStyle(style) {
        this.navbarStyle = style;
        localStorage.setItem('navbarStyle', style);
        this.applyLayoutSettings();
    },

    // Cambiar el estilo del pie de página
    setFooterStyle(style) {
        this.footerStyle = style;
        localStorage.setItem('footerStyle', style);
        this.applyLayoutSettings();
    },

    // Aplicar todos los ajustes de diseño
    applyLayoutSettings() {
        try {
            const html = document.documentElement;
            const body = document.querySelector('body');

            if (!body) {
                console.warn('Elemento body no encontrado');
                return;
            }

            // Dirección RTL/LTR
            if (this.direction === 'rtl') {
                html.setAttribute('dir', 'rtl');
                document.querySelector('html').classList.add('rtl');
            } else {
                html.setAttribute('dir', 'ltr');
                document.querySelector('html').classList.remove('rtl');
            }

            // Layout de contenido
            body.classList.remove('layout-full', 'layout-boxed');
            body.classList.add(this.contentLayout);

            // Layout de menú
            body.classList.remove('horizontalMenu');
            if (this.menuLayout === 'horizontalMenu') {
                body.classList.add('horizontalMenu');
            }

            // Estilo de navbar
            if (this.navbarStyle) {
                const navbar = document.querySelector('.app-header');
                if (navbar) {
                    navbar.classList.remove('floating', 'sticky', 'static', 'hidden');
                    navbar.classList.add(this.navbarStyle);
                }
            }

            // Estilo de footer
            const footer = document.querySelector('.site-footer');
            if (footer) {
                footer.className = 'site-footer';
                footer.classList.add(this.footerStyle);
            }

        } catch (error) {
            console.error('Error al aplicar configuración de layout:', error);
        }
    }
}));

// Componente para toggle de contraseñas
Alpine.data('passwordToggle', () => ({
    isVisible: false,

    toggleVisibility() {
        this.isVisible = !this.isVisible;

        // Cambiar el tipo de campo
        this.$el.querySelector('input').type = this.isVisible ? 'text' : 'password';
    }
}));

// Componente para inputs de logo/imagen
Alpine.data('logoInput', () => ({
    imagePreview: null,

    init() {
        // Si hay una imagen preexistente, mostrar preview
        const input = this.$el.querySelector('input[type="file"]');
        const preview = this.$el.querySelector('.preview-image');

        if (preview && preview.src && preview.src !== '') {
            this.imagePreview = preview.src;
        }

        // Setup del listener
        if (input) {
            input.addEventListener('change', (e) => this.handleImageChange(e));
        }
    },

    handleImageChange(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            this.imagePreview = e.target.result;
        };
        reader.readAsDataURL(file);
    },

    removeImage() {
        this.imagePreview = null;
        const input = this.$el.querySelector('input[type="file"]');
        if (input) input.value = '';
    }
}));
