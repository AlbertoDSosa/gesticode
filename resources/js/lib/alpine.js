// Importar componente de debugging responsivo
import './responsive-debug';

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
        this.applyTheme();
        localStorage.theme = this.currentTheme;
    },

    // Cambiar a un tema específico
    setTheme(theme) {
        this.currentTheme = theme;
        this.applyTheme();
        localStorage.theme = theme;
    },

    // Aplicar tema actual
    applyTheme() {
        document.documentElement.classList.remove('dark', 'light', 'semiDark');
        document.documentElement.classList.add(this.currentTheme);
    },

    // Toggle grayScale
    toggleGrayScale() {
        this.grayScale = !this.grayScale;
        this.applyGrayScale();
        localStorage.effect = this.grayScale ? 'grayScale' : '';
    },

    // Aplicar grayScale
    applyGrayScale() {
        if (this.grayScale) {
            document.documentElement.classList.add('grayScale');
        } else {
            document.documentElement.classList.remove('grayScale');
        }
    }
}));

// Componente para la gestión del sidebar
Alpine.data('sidebarManager', () => ({
    collapsed: localStorage.sideBarType === 'collapsed',
    hidden: false,
    debounceTimer: null,

    init() {
        this.applySidebarState();
        this.checkScreenSize();

        // En lugar de usar un event listener directo, usamos nuestro evento personalizado
        window.addEventListener('app:resize', () => {
            this.checkScreenSize();
        });

        // Escuchar eventos desde el componente responsiveDebug
        this.$el.addEventListener('screen:resized', (event) => {
            this.handleScreenChange(event.detail);
        });
    },

    handleScreenChange(detail) {
        console.log('Sidebar - Cambio de pantalla detectado:', detail.size);

        if (detail.width < 1281 && !this.collapsed) {
            // Auto-colapsar en pantallas pequeñas
            this.closeSidebar();
        } else if (detail.width >= 1281 && localStorage.sideBarType !== 'collapsed' && this.collapsed) {
            // Restaurar estado anterior en pantallas grandes
            this.openSidebar();
        }
    },

    toggleSidebar() {
        this.collapsed = !this.collapsed;
        localStorage.sideBarType = this.collapsed ? 'collapsed' : 'extend';
        this.applySidebarState();
    },

    toggleSidebarVisibility() {
        this.hidden = !this.hidden;
        this.applySidebarState();
    },

    openSidebar() {
        this.collapsed = false;
        localStorage.sideBarType = 'extend';
        this.applySidebarState();
    },

    closeSidebar() {
        this.collapsed = true;
        localStorage.sideBarType = 'collapsed';
        this.applySidebarState();
    },

    applySidebarState() {
        const appWrapper = document.querySelector('.app-wrapper');

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
    init() {
        this.setupMenu();
    },

    setupMenu() {
        // Inicializar el estado activo del menú basado en la URL actual
        const currentPageUrl = window.location.href;
        const currentLink = currentPageUrl.split("/");
        const href = currentLink[currentLink.length - 1];

        // Activar el enlace actual si existe
        const activeLink = document.querySelector(`a[href="${href}"]`);
        if (activeLink) {
            activeLink.classList.add("active");

            // Activar el menú padre
            const parentUl = activeLink.closest('ul.sidebar-submenu');
            if (parentUl) {
                parentUl.classList.add("menu-open");

                // Activar el elemento li padre
                const parentLi = parentUl.closest('li');
                if (parentLi) {
                    parentLi.classList.add("active");
                }
            }
        }
    },

    toggleSubmenu(event, element) {
        const submenu = element.nextElementSibling;

        if (submenu && submenu.classList.contains('sidebar-submenu')) {
            if (submenu.classList.contains('menu-open')) {
                // Cerrar el submenu
                submenu.classList.remove('menu-open');
                submenu.parentElement.classList.remove('active');

                // Animar el cierre
                submenu.style.height = submenu.scrollHeight + 'px';
                setTimeout(() => {
                    submenu.style.height = '0px';
                    setTimeout(() => {
                        submenu.style.removeProperty('height');
                    }, 300);
                }, 10);
            } else {
                // Si hay otros submenus abiertos en el mismo nivel, cerrarlos
                const parent = element.closest('ul');
                const openSubmenus = parent.querySelectorAll('.sidebar-submenu.menu-open');

                openSubmenus.forEach(menu => {
                    menu.classList.remove('menu-open');
                    menu.parentElement.classList.remove('active');

                    // Animar el cierre de otros submenus
                    menu.style.height = menu.scrollHeight + 'px';
                    setTimeout(() => {
                        menu.style.height = '0px';
                        setTimeout(() => {
                            menu.style.removeProperty('height');
                        }, 300);
                    }, 10);
                });

                // Abrir el submenu actual
                submenu.classList.add('menu-open');
                submenu.parentElement.classList.add('active');

                // Animar la apertura
                submenu.style.height = '0px';
                setTimeout(() => {
                    submenu.style.height = submenu.scrollHeight + 'px';
                    setTimeout(() => {
                        submenu.style.removeProperty('height');
                    }, 300);
                }, 10);
            }

            // Si es un enlace sin URL (solo para expandir el menú), prevenir la navegación
            if (submenu) {
                event.preventDefault();
            }
        }
    },
}));

// Componente para la gestión del layout
Alpine.data('layoutManager', () => ({
    contentLayout: localStorage.contentLayout || 'layout-full',
    menuLayout: localStorage.menuLayout || '',
    navbarStyle: localStorage.navbar || 'static',
    footerStyle: localStorage.footer || 'static',
    direction: localStorage.dir || 'ltr',

    init() {
        this.applyLayoutSettings();

        // Reinicializar cuando ocurra una navegación
        document.addEventListener('livewire:navigated', () => {
            this.applyLayoutSettings();
        });
    },

    applyLayoutSettings() {
        // Content Layout
        if (this.contentLayout === 'layout-boxed') {
            document.documentElement.classList.add('layout-boxed');
        } else {
            document.documentElement.classList.remove('layout-boxed');
        }

        // Menu Layout
        if (this.menuLayout === 'horizontalMenu') {
            document.documentElement.classList.add('horizontalMenu');
        } else {
            document.documentElement.classList.remove('horizontalMenu');
        }

        // Navbar Style
        document.documentElement.classList.remove('nav-floating', 'nav-sticky', 'nav-hidden', 'nav-static');
        document.documentElement.classList.add(`nav-${this.navbarStyle}`);

        // Footer Style
        const footer = document.getElementById('footer');
        if (footer) {
            footer.classList.remove('static', 'sticky', 'bottom-0', 'hidden');
            if (this.footerStyle === 'sticky bottom-0') {
                footer.classList.add('sticky', 'bottom-0');
            } else {
                footer.classList.add(this.footerStyle);
            }
        }

        // Direction
        document.documentElement.setAttribute('dir', this.direction);
    },

    setContentLayout(layout) {
        this.contentLayout = layout;
        localStorage.contentLayout = layout;
        this.applyLayoutSettings();
    },

    setMenuLayout(layout) {
        this.menuLayout = layout;
        localStorage.menuLayout = layout;
        this.applyLayoutSettings();
    },

    setNavbarStyle(style) {
        this.navbarStyle = style;
        localStorage.navbar = style;
        this.applyLayoutSettings();
    },

    setFooterStyle(style) {
        this.footerStyle = style;
        localStorage.footer = style;
        this.applyLayoutSettings();
    },

    setDirection(dir) {
        this.direction = dir;
        localStorage.dir = dir;
        this.applyLayoutSettings();
        // La recarga es necesaria para aplicar correctamente los cambios de dirección
        location.reload();
    }
}));

// Componente para manejo de contraseñas
Alpine.data('passwordToggle', () => ({
    showPassword: false,

    toggleVisibility() {
        this.showPassword = !this.showPassword;
        this.$refs.passwordField.type = this.showPassword ? 'text' : 'password';
    }
}));

// Componente para input de logo
Alpine.data('logoInput', () => ({
    change(previewId) {
        const output = document.getElementById(previewId);
        if (output && this.$el.files && this.$el.files[0]) {
            output.src = URL.createObjectURL(this.$el.files[0]);
            output.onload = () => {
                URL.revokeObjectURL(output.src); // liberar memoria
            };
        }
    }
}));

// Componente para el selector de nivel
Alpine.data('levelSelect', () => ({
    levelChange(e) {
        // Este componente necesita ser implementado según la funcionalidad específica
        // requerida en las páginas de permisos
    }
}));


