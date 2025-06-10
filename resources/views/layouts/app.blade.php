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
            'resources/js/custom/store.js',
            'resources/js/main.js'
        ])
    </head>
    <body class="font-inter dashcode-app" id="body_class">
        <div class="app-wrapper">

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
                // Triggers when a navigation is triggered.

                console.log('livewire:navigate', $("#bodyOverlay"))
            })

            document.addEventListener('livewire:navigating', () => {
                // Triggered when new HTML is about to swapped onto the page...

                // This is a good place to mutate any HTML before the page
                // is navigated away from...
                console.log('livewire:navigating', $("#bodyOverlay"))
            })

            document.addEventListener('livewire:navigated', () => {
                // Triggered as the final step of any page navigation...

                // Also triggered on page-load instead of "DOMContentLoaded"...
                console.log('livewire:navigated',$("#bodyOverlay"))
            })
        </script>
    </body>
</html>
