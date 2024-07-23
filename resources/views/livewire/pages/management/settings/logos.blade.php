<?php

use App\Models\GeneralSetting;
use App\Settings\GeneralSettings;
use function Livewire\Volt\{layout, state, usesFileUploads, form};

layout('layouts.app');

usesFileUploads();

$breadcrumbItems = [
    [
        'name' => 'Settings',
        'url' => 'management.settings',
        'active' => false
    ],
    [
        'name' => 'Logos',
        'url' => 'management.settings.logos',
        'active' => true
    ],
];

$pageTitle = 'App Logos';

$logo = '';
$favicon = '';
$dark_logo = '';
$guest_logo = '';
$guest_background = '';

state(compact(
    'breadcrumbItems',
    'pageTitle',
    'logo',
    'favicon',
    'dark_logo',
    'guest_logo',
    'guest_background',
));

$update = function (GeneralSettings $logoSettings) {

    $this->validate([
        'logo' => [
            'image',
            'max:2048',
            'dimensions:width=32,height=32',
        ],
        'favicon' => [
            'image',
            'max:2048',
            'dimensions:width=32,height=32',
        ],
        'dark_logo' => [
            'image',
            'max:2048',
            'dimensions:width=32,height=32',
        ],
        'guest_logo' => [
            'image',
            'max:2048',
            'dimensions:width=122,height=32',
        ],
        'guest_background' => [
            'image',
            'max:2048',
            'dimensions:width=580,height=501',
        ]
    ],
    [
        'logo.dimensions' => 'The logo must be 32x32 pixels.',
        'favicon.dimensions' => 'The favicon must be 32x32 pixels.',
        'dark_logo.dimensions' => 'The dark logo must be 32x32 pixels.',
        'guest_logo.dimensions' => 'The guest logo must be 122x32 pixels.',
        'guest_background.dimensions' => 'The guest background must be 580x501 pixels.'
    ]
    );

    if ($this->logo) {
        $generalSetting = GeneralSetting::where('group', 'general-settings')
            ->where('name', 'logo')
            ->first();
        $generalSetting->clearMediaCollection('logo');
        $generalSetting->addMedia($this->logo)->toMediaCollection('logo');
        $logoSettings->logo = ['contentType' => 'image', 'content' => $generalSetting->getFirstMediaUrl('logo')];
        $logoSettings->save();
    }
    if ($this->favicon) {
        $generalSetting = GeneralSetting::where('group', 'general-settings')
            ->where('name', 'favicon')
            ->first();
        $generalSetting->clearMediaCollection('favicon');
        $generalSetting->addMedia($this->favicon)->toMediaCollection('favicon');
        $logoSettings->favicon = ['contentType' => 'image', 'content' => $generalSetting->getFirstMediaUrl('favicon')];
        $logoSettings->save();
    }
    if ($this->dark_logo) {
        $generalSetting = GeneralSetting::where('group', 'general-settings')
            ->where('name', 'dark_logo')
            ->first();
        $generalSetting->clearMediaCollection('dark_logo');
        $generalSetting->addMedia($this->dark_logo)->toMediaCollection('dark_logo');
        $logoSettings->dark_logo = ['contentType' => 'image', 'content' => $generalSetting->getFirstMediaUrl('dark_logo')];
        $logoSettings->save();
    }
    if ($this->guest_logo) {
        $generalSetting = GeneralSetting::where('group', 'general-settings')
            ->where('name', 'guest_logo')
            ->first();
        $generalSetting->clearMediaCollection('guest_logo');
        $generalSetting->addMedia($this->guest_logo)->toMediaCollection('guest_logo');
        $logoSettings->guest_logo = ['contentType' => 'image', 'content' => $generalSetting->getFirstMediaUrl('guest_logo')];

        $logoSettings->save();
    }
    if ($this->guest_background) {
        $generalSetting = GeneralSetting::where('group', 'general-settings')
            ->where('name', 'guest_background')
            ->first();
        $generalSetting->clearMediaCollection('guest_background');
        $generalSetting->addMedia($this->guest_background)->toMediaCollection('guest_background');
        $logoSettings->guest_background = ['contentType' => 'image', 'content' => $generalSetting->getFirstMediaUrl('guest_background')];
        $logoSettings->save();
    }

};

?>

<div class="space-y-8">
    <div>
        <x-breadcrumb :page-title="$pageTitle" :breadcrumb-items="$breadcrumbItems" />
    </div>

    <div class="overflow-hidden rounded-md">
        <form
            wire:submit="update"
            class="bg-white dark:bg-slate-800 px-7 py-7"
        >
            <div class="grid gap-7 sm:grid-cols-2 lg:grid-cols-3 ">
                <div class="imageUploadCard">
                    <div class="imageUploadCardHeader">
                        <h3 class="">{{ __('Logo') }}</h3>
                    </div>
                    <div class="cardBody">
                        <img
                            wire:ignore
                            id="logoPreview"
                            class="mx-auto h-36 w-36 rounded-md"
                            src="{{ getSettings('logo')['content'] }}"
                            alt="logo"
                        >
                        <div class="relative">
                            <input
                                type="file"
                                name="logo"
                                wire:model="logo"
                                class="defaultButton absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer"
                                onchange="imagePreview(event, 'logoPreview')"
                            />
                            <label class="btn btn-dark !static defaultButton inline-block">
                                {{ __('Choose') }}
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('logo')" class="mt-2"/>
                    </div>
                </div>
                <div class="imageUploadCard">
                    <div class="imageUploadCardHeader">
                        <h3>{{ __('Favicon') }}</h3>
                    </div>
                    <div class="cardBody">
                        <div class="h-36 w-36 mx-auto rounded-md flex items-center justify-center">
                            <img
                                wire:ignore
                                id="faviconPreview"
                                src="{{ getSettings('favicon')['content'] }}"
                                alt="favicon"
                            >
                        </div>
                        <div class="relative">
                            <input
                                type="file"
                                name="favicon"
                                wire:model="favicon"
                                class="defaultButton absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer"
                                onchange="imagePreview(event, 'faviconPreview')"
                            />
                            <label class="btn btn-dark !static defaultButton inline-block">
                                {{ __('Choose') }}
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('favicon')" class="mt-2"/>
                    </div>
                </div>
                <div class="imageUploadCard">
                    <div class="imageUploadCardHeader">
                        <h3>{{ __('Dark Logo') }}</h3>
                    </div>
                    <div class="cardBody">
                        <img
                            wire:ignore
                            id="darkLogoPreview"
                            class="mx-auto h-36 w-36 rounded-md"
                            src="{{ getSettings('dark_logo')['content'] }}"
                            alt="dark_logo"
                        >
                        <div class="relative">
                            <input
                                type="file"
                                name="dark_logo"
                                wire:model="dark_logo"
                                class="defaultButton absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer"
                                onchange="imagePreview(event, 'darkLogoPreview')"
                            />
                            <label class="btn btn-dark !static defaultButton inline-block">
                                {{ __('Choose') }}
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('dark_logo')" class="mt-2"/>
                    </div>
                </div>
                <div class="imageUploadCard">
                    <div class="imageUploadCardHeader">
                        <h3>{{ __('Guest Logo') }}</h3>
                    </div>
                    <div class="cardBody">
                        <img
                            wire:ignore
                            id="guestLogoPreview"
                            class="mx-auto h-36 w-36 rounded-md"
                            src="{{ getSettings('guest_logo')['content'] }}"
                            alt="guest_logo"
                        >
                        <div class="relative">
                            <input
                                type="file"
                                name="guest_logo"
                                wire:model="guest_logo"
                                class="defaultButton absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer"
                                onchange="imagePreview(event, 'guestLogoPreview')"
                            />
                            <label class="btn btn-dark !static defaultButton inline-block">
                                {{ __('Choose') }}
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('guest_logo')" class="mt-2"/>
                    </div>
                </div>
                <div class="imageUploadCard">
                    <div class="imageUploadCardHeader">
                        <h3>{{ __('Guest Background') }}</h3>
                    </div>
                    <div class="cardBody">
                        <img
                            wire:ignore
                            id="guestBackgroundPreview"
                            class="mx-auto h-36 w-36 rounded-md"
                            src="{{ getSettings('guest_background')['content'] }}"
                            alt="guest_background"
                        >
                        <div class="relative">
                            <input
                                type="file"
                                name="guest_background"
                                wire:model="guest_background"
                                class="defaultButton absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer"
                                onchange="imagePreview(event, 'guestBackgroundPreview')"
                            />
                            <label class="btn btn-dark !static defaultButton inline-block">
                                {{ __('Choose') }}
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('guest_background')" class="mt-2"/>
                    </div>
                </div>
            </div>
            <button class="defaultButton btn btn-dark submitButton ml-auto mt-8" type="submit" wire:click="$refresh">
                {{ __('Save Changes') }}
            </button>
        </form>
    </div>

    @push('scripts')
        <script>
            let imagePreview = function(event, id) {
                event.preventDefault();
                let output = document.getElementById(id);
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                    URL.revokeObjectURL(output.src) // free memory
                }
            };
        </script>
    @endpush
</div>
