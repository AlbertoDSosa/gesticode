<?php

use function Livewire\Volt\{state, layout, computed, mount};
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

layout('layouts.app');

$breadcrumbItems = [];
$permission = fn() => $this->permission;

state([
    'name' => fn() => $this->permission->name,
    'module_name' => fn() => $this->permission->module_name,
    'removable' => fn() => $this->permission->removable,
    'editable' => fn() => $this->permission->editable,
    'removable' => fn() => $this->permission->removable,
    'assignable' => fn() => $this->permission->assignable,
    'level' => fn() => $this->permission->level
]);

$pageTitle = 'Edit Permission';

state(compact('breadcrumbItems', 'pageTitle', 'permission'))->locked();

$authUser = computed(function() {
   return auth()->user();
});

$permissionLevels = computed(function () {
    if($this->authUser->hasRole('super-admin')) {
        return ['regular', 'admin', 'super-admin'];
    }
    return ['regular', 'admin'];
});

mount(function(Permission $permission) {

    $isntSuperAdmin = !$this->authUser->hasRole('super-admin');

    if($permission->level === 'super-admin' && $isntSuperAdmin) {
        abort(403);
    }

    $this->breadcrumbItems = [
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
            'name' => 'Edit',
            'url' => route('management.settings.permissions.edit', ['permission' => $permission]),
            'active' => true
        ],
    ];
});

$update = function () {
    $this->validate(
        [
            'name' => ['required', 'string', 'max:255', Rule::unique(Permission::class)->ignore($this->permission->id)],
            'removable' => ['boolean'],
            'editable' => ['boolean'],
            'assignable' => ['boolean'],
            'module_name' => ['required', 'string', 'max:255'],
            'level' => ['required', 'in:regular,admin,super-admin']
        ],
        [
            'name.unique' => __('The permission name already exists.'),
            'name.required' => __('The permission name field is required.'),
            'module_name.required' => __('The module name field is required.'),
            'level.required' => __('The level field is required.'),
            'level.in' => __('The level value is invalid.')
        ]
    );

    if($this->authUser->cannot('edit permissions')) {
        abort(401);
    }

    if($this->permission->level === 'super-admin') {
        abort(403);
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

    $this->permission->update([
        'name' => $this->name,
        'module_name' => $this->module_name,
        'level' => $this->level,
        'removable' => $this->removable,
        'editable' => $this->editable,
        'assignable' => $this->assignable
    ]);

    session()
        ->flash('status', ['message' => 'Permisson has been edited successfully.', 'type' => 'success']);

    $this->redirect(route('management.settings.permissions'), navigate: true);
};

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

    {{--Edit Permission form start--}}
    <form>
        <div class="bg-white dark:bg-slate-800 rounded-md p-5 pb-6 space-y-4">
            {{-- module name--}}
            <div class="input-area">
                <label for="name" class="form-label">
                    {{ __('Module Name') }}
                </label>
                <input
                    name="module_name"
                    type="text"
                    id="name"
                    class="form-control"
                    placeholder="{{ __('Enter your module name') }}"
                    value="{{$permission->module_name}}"
                    required
                    @disabled(!$editable)
                >
                <x-input-error :messages="$errors->get('module_name')" class="mt-2"/>
            </div>
            {{--Name input start--}}
            <div class="input-area">
                <label for="name" class="form-label">
                    {{ __('Permission Name') }}
                </label>
                <input
                    name="name"
                    type="text"
                    id="name"
                    class="form-control"
                    placeholder="{{__('Enter permission name')}}"
                    value="{{$permission->name}}"
                    required
                    @disabled(!$editable)
                >
                <x-input-error :messages="$errors->get('name')" class="mt-2"/>
            </div>
            <x-input-error :messages="$errors->get('permission_name')" class="mt-2"/>
            {{--Name input end--}}

            <div class="input-area">
                <label for="select" class="form-label">Level</label>
                <select
                    x-data="levelSelect"
                    id="select"
                    class="form-control"
                    x-on:change="levelChange"
                    @disabled(!$editable)
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
                                @disabled($permission->level === 'admin' || $permission->level === 'super-admin')
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
                                @disabled(($permission->level === 'admin' || $permission->level === 'super-admin') && !$editable)
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
                                @disabled($permission->level === 'super-admin')
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
                @disabled($permission->level === 'super-admin')
            >
                <span class="flex items-center">
                    <iconify-icon class="ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                    <span>{{ __('Save Changes') }}</span>
                </span>
            </button>
        </div>

    </form>
    {{--Edit Permission form end--}}
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
