<?php

use App\Models\Common\LogoSetting;
use App\Settings\LogoSettings;
use function Livewire\Volt\{layout, state, usesFileUploads};

layout('layouts.app');

usesFileUploads();

$pageTitle = 'App Logos';

$logo = '';
$favicon = '';
$dark_logo = '';
$guest_logo = '';
$guest_background = '';
$disabledUpload = true;

state(compact(
    'pageTitle',
    'logo',
    'favicon',
    'dark_logo',
    'guest_logo',
    'guest_background',
    'disabledUpload'
));

$update = function (LogoSettings $logoSettings) {

    $this->authorize('update', LogoSetting::class);

    $this->validate(
        [
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

    $logoSettings->saveLogos($this);

    session()
        ->flash('status', ['message' => 'Logo updated successfully.', 'type' => 'success']);

};

$resetStatus = function () {
    session()->forget('status');
};

?>

<div class="space-y-8">
    {{--Alert--}}
    @if (session('status'))
        <x-alert :message="session('status')['message']" :type="session('status')['type']" />
    @endif
    <div class="space-y-8">

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
                                src="{{ getLogoSettings('logo')['content'] }}"
                                alt="logo"
                            >
                            <div class="relative">
                                <input
                                    x-data="logoInput"
                                    x-on:change="change('logoPreview')"
                                    type="file"
                                    name="logo"
                                    wire:model="logo"
                                    class="defaultButton absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer"
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
                                    src="{{ getLogoSettings('favicon')['content'] }}"
                                    alt="favicon"
                                >
                            </div>
                            <div class="relative">
                                <input
                                    type="file"
                                    name="favicon"
                                    x-data="logoInput"
                                    x-on:change="change('faviconPreview')"
                                    wire:model="favicon"
                                    class="defaultButton absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer"
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
                                src="{{ getLogoSettings('dark_logo')['content'] }}"
                                alt="dark_logo"
                            >
                            <div class="relative">
                                <input
                                    type="file"
                                    name="dark_logo"
                                    x-data="logoInput"
                                    x-on:change="change('darkLogoPreview')"
                                    wire:model="dark_logo"
                                    class="defaultButton absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer"
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
                                src="{{ getLogoSettings('guest_logo')['content'] }}"
                                alt="guest_logo"
                            >
                            <div class="relative">
                                <input
                                    type="file"
                                    name="guest_logo"
                                    x-data="logoInput"
                                    x-on:change="change('guestLogoPreview')"
                                    wire:model="guest_logo"
                                    class="defaultButton absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer"
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
                                src="{{ getLogoSettings('guest_background')['content'] }}"
                                alt="guest_background"
                            >
                            <div class="relative">
                                <input
                                    type="file"
                                    name="guest_background"
                                    x-data="logoInput"
                                    x-on:change="change('guestBackgroundPreview')"
                                    wire:model="guest_background"
                                    class="defaultButton absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer"
                                />
                                <label class="btn btn-dark !static defaultButton inline-block">
                                    {{ __('Choose') }}
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('guest_background')" class="mt-2"/>
                        </div>
                    </div>
                </div>
                <button
                    class="defaultButton btn btn-dark submitButton ml-auto mt-8"
                    type="submit"
                    @disabled($disabledUpload)
                >
                    {{ __('Save Changes') }}
                </button>
            </form>
        </div>
    </div>
</div>

@script
<script>
    Alpine.data('logoInput', () => ({
        change(id) {
            let output = document.getElementById(id);
            output.src = URL.createObjectURL(this.$el.files[0]);
            output.onload = function() {
                URL.revokeObjectURL(output.src) // free memory
            }

            $wire.disabledUpload = false;
        }
    }));
</script>
@endscript
