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
    }
}));

// Componente para la gestión del tema
Alpine.data('themeManager', () => ({
    currentTheme: localStorage.getItem('theme') || 'light',
    grayScale: localStorage.getItem('effect') === 'grayScale',

    init() {
        this.applyTheme();
        this.applyGrayScale();
    },

    toggleTheme() {
        this.currentTheme = this.currentTheme === 'light' ? 'dark' : 'light';
        localStorage.setItem('theme', this.currentTheme);
        this.applyTheme();
    },

    setTheme(theme) {
        this.currentTheme = theme;
        localStorage.setItem('theme', theme);
        this.applyTheme();
    },

    applyTheme() {
        const html = document.documentElement;
        const body = document.body;

        html.classList.remove('light', 'dark', 'semiDark');
        body.classList.remove('light', 'dark', 'semiDark');

        if (this.currentTheme) {
            html.classList.add(this.currentTheme);
            body.classList.add(this.currentTheme);
        }
    },

    toggleGrayScale() {
        this.grayScale = !this.grayScale;
        localStorage.setItem('effect', this.grayScale ? 'grayScale' : '');
        this.applyGrayScale();
    },

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

    init() {
        this.applySidebarState();
        this.checkScreenSize();

        window.addEventListener('resize', () => {
            this.checkScreenSize();
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
        const appWrapper = document.querySelector('.app-wrapper');
        if (!appWrapper) return;

        appWrapper.classList.remove('collapsed', 'extend', 'menu-hide');

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
        const width = window.innerWidth;
        const elements = {
            header: document.querySelector(".app-header"),
            footer: document.querySelector(".site-footer"),
            content: document.querySelector("#content_wrapper"),
            closeIcon: document.querySelector(".sidebarCloseIcon"),
            sidebarType: document.querySelector("#sidebar_type"),
            overlay: document.querySelector("#bodyOverlay")
        };

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
    }
}));

// Componente para la gestión del menú sidebar
Alpine.data('sidebarMenu', () => ({
    activeMenu: null,
    activeSubMenu: null,

    init() {
        this.activeMenu = localStorage.getItem('activeMenu');
        this.activeSubMenu = localStorage.getItem('activeSubMenu');
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

// Componente para la gestión del layout
Alpine.data('layoutManager', () => ({
    direction: localStorage.getItem('direction') || 'ltr',
    contentLayout: localStorage.getItem('contentLayout') || 'layout-full',
    menuLayout: localStorage.getItem('menuLayout') || '',
    navbarStyle: localStorage.getItem('navbarStyle') || 'sticky',
    footerStyle: localStorage.getItem('footerStyle') || 'static',

    init() {
        this.applyLayoutSettings();
    },

    setDirection(dir) {
        this.direction = dir;
        localStorage.setItem('direction', dir);
        this.applyLayoutSettings();
    },

    setContentLayout(layout) {
        this.contentLayout = layout;
        localStorage.setItem('contentLayout', layout);
        this.applyLayoutSettings();
    },

    setMenuLayout(layout) {
        this.menuLayout = layout;
        localStorage.setItem('menuLayout', layout);
        this.applyLayoutSettings();
    },

    setNavbarStyle(style) {
        this.navbarStyle = style;
        localStorage.setItem('navbarStyle', style);
        this.applyLayoutSettings();
    },

    setFooterStyle(style) {
        this.footerStyle = style;
        localStorage.setItem('footerStyle', style);
        this.applyLayoutSettings();
    },

    applyLayoutSettings() {
        const html = document.documentElement;
        const body = document.querySelector('body');

        if (!body) return;

        if (this.direction === 'rtl') {
            html.setAttribute('dir', 'rtl');
            html.classList.add('rtl');
        } else {
            html.setAttribute('dir', 'ltr');
            html.classList.remove('rtl');
        }

        body.classList.remove('layout-full', 'layout-boxed');
        body.classList.add(this.contentLayout);

        body.classList.remove('horizontalMenu');
        if (this.menuLayout === 'horizontalMenu') {
            body.classList.add('horizontalMenu');
        }

        const navbar = document.querySelector('.app-header');
        if (navbar && this.navbarStyle) {
            navbar.classList.remove('floating', 'sticky', 'static', 'hidden');
            navbar.classList.add(this.navbarStyle);
        }

        const footer = document.querySelector('.site-footer');
        if (footer) {
            footer.className = 'site-footer';
            footer.classList.add(this.footerStyle);
        }
    }
}));

// Inicializar Alpine y configurar el logger de errores
document.addEventListener('DOMContentLoaded', () => {

    // Agregar el evento personalizado de redimensionamiento
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            window.dispatchEvent(new CustomEvent('app:resize', {
                detail: {
                    width: window.innerWidth,
                    height: window.innerHeight
                }
            }));
        }, 250);
    });

    // Eventos de Livewire
    document.addEventListener('livewire:navigated', () => {
        console.log('Livewire navegación completada');
    });

    document.addEventListener('livewire:navigating', () => {
        console.log('Livewire navegando...');
        // Guardar estados en localStorage para mantenerlos durante la navegación
        const themeManagerData = Alpine.store('themeManager');
        if (themeManagerData) {
            localStorage.setItem('theme', themeManagerData.currentTheme);
            localStorage.setItem('effect', themeManagerData.grayScale ? 'grayScale' : '');
        }
    });
});
