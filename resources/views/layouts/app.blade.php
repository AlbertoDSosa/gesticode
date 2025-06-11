<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Gesticode') }}</title>
        <x-favicon />

        <!-- Scripts -->
        @vite([
            'resources/css/app.scss'
        ])
        @vite([
            'resources/js/app.js',
            'resources/js/main.js'
        ])

        <!-- Alpine.js Styles -->
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-inter dashcode-app" id="body_class" x-data="responsiveDebug" x-cloak>
        <!-- Panel de depuración responsiva (visible solo en desarrollo) -->
        @if(config('app.debug'))
        <div class="fixed bottom-0 left-0 z-50 bg-black bg-opacity-75 text-white p-2 text-xs rounded-tr-md" style="font-family: monospace;">
            <span>Screen: <span x-text="screenSize"></span></span>
            <span class="ml-2">Size: <span x-text="width + 'x' + height"></span></span>
        </div>
        @endif

        <div class="app-wrapper" x-data="sidebarManager"

            <!-- BEGIN: Sidebar Navigation -->
            <x-sidebar-menu />
            <!-- End: Sidebar -->

            <!-- BEGIN: Settings -->
            <x-dashboard-settings />
            <!-- End: Settings -->

            <div class="flex flex-col justify-between min-h-screen">
                <div>
                    <!-- BEGIN: header -->
                    <livewire:layout.dashboard-header />
                    <!-- BEGIN: header -->

                    <div class="content-wrapper transition-all duration-150 ltr:ml-0 xl:ltr:ml-[248px] rtl:mr-0 xl:rtl:mr-[248px]" id="content_wrapper">
                        <div class="page-content">
                            <div class="transition-all duration-150 container-fluid" id="page_layout">
                                <main id="content_layout">
                                    <!-- Page Content -->
                                    {{ $slot }}
                                </main>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BEGIN: footer -->
                <x-dashboard-footer />
                <!-- BEGIN: footer -->

            </div>
        </div>
        {{-- @vite([]) --}}
        <script>
            document.addEventListener('livewire:navigate', (event) => {
                // Almacenar el estado actual del tema y la navegación
                // if (window.Alpine) {
                //     const themeManager = Alpine.store('themeState') || {};
                //     if (themeManager.saveState) {
                //         themeManager.saveState();
                //     }
                // }
            });

            document.addEventListener('livewire:navigating', () => {
                // Podemos mostrar un estado de carga aquí si es necesario
                const bodyOverlay = document.getElementById('bodyOverlay');
                if (bodyOverlay) {
                    bodyOverlay.classList.remove('hidden');
                }
            });

            document.addEventListener('livewire:navigated', () => {
                // Reinicializar componentes Alpine.js después de la navegación
                // if (window.Alpine) {
                //     // Actualizar componentes Alpine.js
                //     Alpine.initTree(document.body);
                // }

                // También inicializar los event listeners
                // if (window.setupEventListeners) {
                //     window.setupEventListeners();
                // }

                // Ocultar overlay una vez completada la navegación
                const bodyOverlay = document.getElementById('bodyOverlay');
                if (bodyOverlay) {
                    bodyOverlay.classList.add('hidden');
                }
            });
        </script>
    </body>
</html>
