<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
};

?>

<div class="z-[9] sticky top-0" id="app_header">
    <div class="app-header z-[999] bg-white dark:bg-slate-800 shadow-sm dark:shadow-slate-700">
        <div class="flex justify-between items-center h-full">
            <div class="flex items-center md:space-x-4 space-x-4 rtl:space-x-reverse vertical-box">
                <div class="xl:hidden inline-block">
                    <x-application-logo class="mobile-logo" />
                </div>
                <button class="smallDeviceMenuController  open-sdiebar-controller hidden xl:hidden md:inline-block">
                    <iconify-icon
                        class="leading-none bg-transparent relative text-xl top-[2px] text-slate-900 dark:text-white"
                        icon="heroicons-outline:menu-alt-3"></iconify-icon>
                </button>
                <button class="sidebarOpenButton text-xl text-slate-900 dark:text-white !ml-0">
                    <iconify-icon icon="ph:arrow-right-bold"></iconify-icon>
                </button>
                {{-- <x-header-search /> --}}
            </div>
            <!-- end vertcial -->

            <div class="items-center space-x-4 rtl:space-x-reverse horizental-box">
                <x-application-logo />
                <button class="smallDeviceMenuController open-sidebar-controller hidden xl:hidden md:inline-block">
                    <iconify-icon
                        class="leading-none bg-transparent relative text-xl top-[2px] text-slate-900 dark:text-white"
                        icon="heroicons-outline:menu-alt-3"></iconify-icon>
                </button>
                {{-- <x-header-search /> --}}
            </div>
            <!-- end horizontal -->

            <!-- start horizontal nav -->
            {{-- <x-topbar-menu /> --}}
            <!-- end horizontal nav -->

            <div class="nav-tools flex items-center lg:space-x-5 space-x-3 rtl:space-x-reverse leading-0">
                {{-- <x-nav-lang-dropdown /> --}}
                <x-dark-light />
                <x-gray-scale />
                {{-- <x-nav-message-dropdown />
                <x-nav-notification-dropdown /> --}}
                <x-nav-user-dropdown />
                <button class="smallDeviceMenuController md:hidden block leading-0">
                    <iconify-icon class="cursor-pointer text-slate-900 dark:text-white text-2xl"
                        icon="heroicons-outline:menu-alt-3"></iconify-icon>
                </button>
                <!-- end mobile menu -->
            </div>
            <!-- end nav tools -->
        </div>
    </div>
</div>
