<?php

use function Livewire\Volt\{state, layout, rules, computed};
use Spatie\Permission\Models\Permission;

layout('layouts.app');

state([
    'name' => '',
    'module_name' => '',
    'removable' => true,
    'editable' => true,
    'removable' => true,
    'assignable' => true,
    'level' => 'regular'
]);

rules([
    'name' => ['required', 'string', 'max:255', 'unique:permissions'],
    'removable' => ['boolean'],
    'editable' => ['boolean'],
    'assignable' => ['boolean'],
    'module_name' => ['required', 'string', 'max:255'],
    'level' => ['required', 'in:regular,admin,super-admin']
])->messages([
    'name.unique' => __('The permission name already exists.'),
    'name.required' => __('The permission name field is required.'),
    'module_name.required' => __('The module name field is required.'),
    'level.required' => __('The level field is required.'),
    'level.in' => __('The level value is invalid.')
]);

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

$authUser = computed(function() {
   return auth()->user();
});

$pageTitle = 'Create Permission';

$permissionLevels = computed(function () {
    if($this->authUser->hasRole('super-admin')) {
        return ['regular', 'admin', 'super-admin'];
    }
    return ['regular', 'admin'];
});

state(compact('breadcrumbItems', 'pageTitle'))->locked();

$create = function() {
    $this->validate();

    if($this->authUser->cannot('create permissions')) {
        abort(401);
    }

    $isntSuperAdmin = !$this->authUser->hasRole('super-admin');

    if($isntSuperAdmin && $this->level === 'super-admin') {
        abort(403);
    }

    $cannotModifiable = $this->removable || $this->editable || $this->assignable;

    if($this->level === 'super-admin' && $cannotModifiable) {
        abort(403);
    }

    $onlyAssignable = $this->removable || $this->editable;

    if($this->level === 'admin' && $onlyAssignable) {
        abort(403);
    }

    Permission::create([
        'name' => $this->name,
        'module_name' => $this->module_name,
        'removable' => $this->removable,
        'editable' => $this->editable,
        'removable' => $this->removable,
        'assignable' => $this->assignable,
        'level' => $this->level,
    ]);

    session()
        ->flash('status', ['message' => 'Permisson has been created successfully.', 'type' => 'success']);

    $this->redirect(route('management.settings.permissions'), navigate: true);

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

            <div class="input-area">
                <label for="select" class="form-label">Level</label>
                <select
                    x-data="levelSelect"
                    id="select"
                    class="form-control"
                    x-on:change="levelChange"
                >
                    @foreach ($this->permissionLevels as $itemLevel)
                    <option
                        value="{{$itemLevel}}"
                        class="dark:bg-slate-700"
                        @selected($itemLevel === $level)
                    >
                        {{$itemLevel}}
                    </option>
                    @endforeach

                </select>
            </div>

            <div class="flex">
                <div class="flex items-center justify-between gap-x-3">
                    <label for="editable" class="inputText">
                        {{__('Editable')}}
                    </label>
                    <div class="flex items-center mr-2 sm:mr-4 space-x-2">
                        <label class="relative inline-flex h-6 w-[46px] items-center rounded-full transition-all duration-150 cursor-pointer">
                            <input
                                x-ref="editable"
                                wire:model="editable"
                                name="editable"
                                id="editable"
                                type="checkbox"
                                class="sr-only peer"
                                @checked($editable)
                            >
                            <div class="w-14 h-6 bg-gray-200 peer-focus:outline-none ring-0 rounded-full peer dark:bg-gray-900 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:z-10 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-500"></div>
                            <span class="absolute left-1 z-20 text-xs text-white font-Inter font-normal opacity-0 peer-checked:opacity-100">On</span>
                            <span class="absolute right-1 z-20 text-xs text-white font-Inter font-normal opacity-100 peer-checked:opacity-0">Off</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-x-3">
                    <label for="removable" class="inputText">
                        {{__('Removable')}}
                    </label>
                    <div class="flex items-center mr-2 sm:mr-4 space-x-2">
                        <label class="relative inline-flex h-6 w-[46px] items-center rounded-full transition-all duration-150 cursor-pointer">
                            <input
                                x-ref="removable"
                                wire:model="removable"
                                name="removable"
                                id="removable"
                                type="checkbox"
                                class="sr-only peer"
                                @checked($removable)
                            >
                            <div class="w-14 h-6 bg-gray-200 peer-focus:outline-none ring-0 rounded-full peer dark:bg-gray-900 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:z-10 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-500"></div>
                            <span class="absolute left-1 z-20 text-xs text-white font-Inter font-normal opacity-0 peer-checked:opacity-100">On</span>
                            <span class="absolute right-1 z-20 text-xs text-white font-Inter font-normal opacity-100 peer-checked:opacity-0">Off</span>
                        </label>
                    </div>
                </div>
                <div class="flex items-center justify-between gap-x-3">
                    <label for="assignable" class="inputText">
                        {{__('Assignable')}}
                    </label>
                    <div class="flex items-center mr-2 sm:mr-4 space-x-2">
                        <label class="relative inline-flex h-6 w-[46px] items-center rounded-full transition-all duration-150 cursor-pointer">
                            <input
                                x-ref="assignable"
                                wire:model="assignable"
                                name="assignable"
                                id="assignable"
                                type="checkbox"
                                class="sr-only peer"
                                @checked($assignable)
                            >
                            <div class="w-14 h-6 bg-gray-200 peer-focus:outline-none ring-0 rounded-full peer dark:bg-gray-900 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:z-10 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-500"></div>
                            <span class="absolute left-1 z-20 text-xs text-white font-Inter font-normal opacity-0 peer-checked:opacity-100">On</span>
                            <span class="absolute right-1 z-20 text-xs text-white font-Inter font-normal opacity-100 peer-checked:opacity-0">Off</span>
                        </label>
                    </div>
                </div>
            </div>

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

@script
<script>
    Alpine.data('levelSelect', () => ({
        levelChange(e) {
            if(e.target.value === 'super-admin') {
                $refs.removable.disabled = true;
                $refs.editable.disabled = true;
                $refs.assignable.disabled = true;
                $refs.removable.checked = false;
                $refs.editable.checked = false;
                $refs.assignable.checked = false;
            }

            if(e.target.value === 'admin') {
                $refs.removable.disabled = true;
                $refs.editable.disabled = true;
                $refs.assignable.disabled = false;
                $refs.removable.checked = false;
                $refs.editable.checked = false;
                $refs.assignable.checked = true;
            }

            if(e.target.value === 'regular') {
                $refs.removable.disabled = false;
                $refs.editable.disabled = false;
                $refs.assignable.disabled = false;
                $refs.removable.checked = true;
                $refs.editable.checked = true;
                $refs.assignable.checked = true;
            }
        }
    }));
</script>
@endscript
