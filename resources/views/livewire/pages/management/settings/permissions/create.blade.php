<?php

use function Livewire\Volt\{state, layout};
use Spatie\Permission\Models\Permission;

layout('layouts.app');

state('name');

$breadcrumbItems = [
    [
        'name' => 'Settings',
        'url' => route('management.settings'),
        'active' => false
    ],
    [
        'name' => 'Permissions',
        'url' => route('management.settings.permissions'),
        'active' => false
    ],
    [
        'name' => 'Create',
        'url' => route('management.settings.permissions.create'),
        'active' => true
    ],
];

$pageTitle = 'Create Permission';

state(compact('breadcrumbItems', 'pageTitle'))->locked();

$create = function() {
    $this->validate(
        ['name' => ['required', 'string', 'max:255', 'unique:permissions,name']]
    );

    Permission::create([
        'name' => $this->name,
        'guard_name' => 'web'
    ]);

    session()
        ->flash('status', ['message' => 'Permisson Create successfully.', 'type' => 'success']);

}


?>

<div class="md:w-2/3 mx-auto">
    {{--Breadcrumb start--}}
    <div class="block sm:flex items-center justify-between mb-6">
        {{--Breadcrumb--}}
        <x-breadcrumb :pageTitle="$pageTitle" :breadcrumbItems="$breadcrumbItems"/>

        <div class="text-end">
            <a class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2 !px-3" href="{{ route('management.settings.permissions') }}">
                <iconify-icon class="text-lg mr-1" icon="ic:outline-arrow-back"></iconify-icon>
                {{ __('Back') }}
            </a>
        </div>
    </div>
    {{--Breadcrumb end--}}

    {{--Create permission form start--}}
    <form wire:submit="create">
        <div class="bg-white dark:bg-slate-800 rounded-md p-5 pb-6 space-y-4">
            {{-- module name--}}
            <div class="input-area">
                <label for="module_name" class="form-label">{{ __('Module Name') }}</label>
                <input
                    name="module_name"
                    type="text"
                    id="module_name"
                    class="form-control"
                    placeholder="{{ __('Enter your module name') }}"
                    required
                >
                <x-input-error :messages="$errors->get('module_name')" class="mt-2"/>
            </div>

            {{--Name input --}}
            <div class="input-area">
                <label for="name" class="form-label">
                    {{ __('Permission Name') }}
                </label>
                <input
                    wire.model="name"
                    name="name"
                    type="text"
                    id="name"
                    class="form-control"
                    placeholder="{{ __('Enter permission name') }}"
                    required
                >
                <x-input-error :messages="$errors->get('name')" class="mt-2"/>
            </div>

            <x-input-error :messages="$errors->get('permission_name')" class="mt-2"/>

            <button
                type="submit"
                class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1 mt-4 !px-3 !py-2"
            >
                <span class="flex items-center">
                    <iconify-icon class="ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                    <span>@lang('Save')</span>
                </span>
            </button>
        </div>
    </form>
</div>
