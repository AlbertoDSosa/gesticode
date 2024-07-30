<?php

use function Livewire\Volt\{layout, state};

layout('layouts.app');

$breadcrumbItems = [
    [
        'name' => 'Tools',
        'url' => route('management.tools'),
        'active' => true
    ],
];

$pageTitle = 'Tools';

state(compact('breadcrumbItems', 'pageTitle'));

?>

<div class="space-y-8">
    <div>
        <x-breadcrumb :page-title="$pageTitle" :breadcrumb-items="$breadcrumbItems" />
    </div>

    <div class=" space-y-5">
        <div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-6">
            <div class="card">
                <div class="card-body p-6">
                    <div class="space-y-6">
                        <div class="flex space-x-3 items-center rtl:space-x-reverse">
                            <div class="flex-none h-8 w-8 rounded-full bg-slate-800 dark:bg-slate-700 text-slate-300 flex flex-col items-center
                                    justify-center text-lg">
                                <iconify-icon icon="material-symbols:error-outline"></iconify-icon>
                            </div>
                            <div class="flex-1 text-base text-slate-900 dark:text-white font-medium">
                                System Logs
                            </div>
                        </div>
                        <div class="text-slate-600 dark:text-slate-300 text-sm">
                            Manage your system logs
                        </div>
                        <a
                            href="/log-viewer"
                            class="inline-flex items-center space-x-3 rtl:space-x-reverse text-sm capitalize font-medium text-slate-600
                                dark:text-slate-300"
                        >
                            <span>Manage logs</span>
                            <iconify-icon icon="heroicons:arrow-right"></iconify-icon>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-6">
                    <div class="space-y-6">
                        <div class="flex space-x-3 items-center rtl:space-x-reverse">
                            <div class="flex-none h-8 w-8 rounded-full bg-slate-800 dark:bg-slate-700 text-slate-300 flex flex-col items-center
                                    justify-center text-lg">
                                <iconify-icon icon="material-symbols:interactive-space-outline"></iconify-icon>
                            </div>
                            <div class="flex-1 text-base text-slate-900 dark:text-white font-medium">
                                User Activity Logs
                            </div>
                        </div>
                        <div class="text-slate-600 dark:text-slate-300 text-sm">
                            Manage your user activity logs
                        </div>
                        <a
                            href="{{route('management.tools.logs.user-activity')}}"
                            class="inline-flex items-center space-x-3 rtl:space-x-reverse text-sm capitalize font-medium text-slate-600
                                dark:text-slate-300"
                        >
                            <span>Manage logs</span>
                            <iconify-icon icon="heroicons:arrow-right"></iconify-icon>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-6">
                    <div class="space-y-6">
                        <div class="flex space-x-3 items-center rtl:space-x-reverse">
                            <div class="flex-none h-8 w-8 rounded-full bg-slate-800 dark:bg-slate-700 text-slate-300 flex flex-col items-center
                                    justify-center text-lg">
                                <iconify-icon icon="material-symbols:backup-outline"></iconify-icon>
                            </div>
                            <div class="flex-1 text-base text-slate-900 dark:text-white font-medium">
                                System Backups
                            </div>
                        </div>
                        <div class="text-slate-600 dark:text-slate-300 text-sm">
                            Set up your system backups, and more
                        </div>
                        <a
                            href="{{route('management.tools.backups')}}"
                            class="inline-flex items-center space-x-3 rtl:space-x-reverse text-sm capitalize font-medium text-slate-600
                                dark:text-slate-300"
                        >
                            <span>Go to page</span>
                            <iconify-icon icon="heroicons:arrow-right"></iconify-icon>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

