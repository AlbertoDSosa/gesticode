<?php

use function Livewire\Volt\{state, layout, computed, rules, on};
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

layout('layouts.app');

state([
    'name' => '',
    'display_name' => '',
    'removable' => true,
    'permissions' => [],
]);

rules([
    'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
    'removable' => ['boolean'],
    'display_name' => ['required', 'string', 'max:255'],
    'permissions' => ['array']
])->messages([
    'display_name.required' => __('The display name field is required.'),
    'name.unique' => __('The role name already exists.')
]);

$breadcrumbItems = [
    [
        'name' => 'Settings',
        'url' => route('management.settings'),
        'active' => false
    ],
    [
        'name' => 'Roles',
        'url' => route('management.settings.roles'),
        'active' => false
    ],
    [
        'name' => 'Create',
        'url' => route('management.settings.roles.create'),
        'active' => true
    ],
];

$pageTitle = 'Create Role';

$authUser = computed(function() {
   return auth()->user();
});

$permissionModules = computed(function() {
    $isntSuperAdmin = $this->authUser->cannot('show super admin permissions');
    return Permission::when($isntSuperAdmin, function ($query) {
        $query->where('level', '!=', 'super-admin');
    })->get()->groupBy('module_name')->forget(['roles', 'permissions']);
});

on(['name-change' => function () {
    $this->name = Str::slug($this->display_name, '-');
}]);

state(compact('breadcrumbItems', 'pageTitle'))->locked();

$create = function() {
    $this->validate();

    $validatedPermissions = [];

    foreach ($this->permissions as $permissonId) {
        $permission = Permission::find($permissonId);

        if(!$permission) {
            $this->addError('permissions', "The permission with ID: {$permissonId} not exists");
            return false;
        }

        if(!$permission->editable) {
            abort(403);
        }

        $permissionHasSuperAdminLevel = $permission->level === 'super-admin';
        $isntSuperAdmin = $this->authUser->mainRole->name !== 'super-admin';

        if ($isntSuperAdmin && $permissionHasSuperAdminLevel) {
            abort(401);
        }

        $validatedPermissions[] = $permission->name;
    }

    DB::transaction(function () use($validatedPermissions) {
        $role = Role::create([
            'name' => $this->name,
            'display_name' => $this->display_name,
            'removable' => $this->removable ?? null
        ]);

        if(count($validatedPermissions)) {
            $role->givePermissionTo($validatedPermissions);
        }
    });

    session()->flash(
        'status',
        [
            'message' => 'Role has been created successfully.',
            'type' => 'success'
        ]
    );

    $this->redirect(route('management.settings.roles'));

};

?>

<div class="space-y-8">
    <div class="block sm:flex items-center justify-between mb-6">
        {{--Breadcrumb--}}
        <x-breadcrumb :pageTitle="$pageTitle" :breadcrumbItems="$breadcrumbItems"/>

        <div class="text-end">
            <a class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2 !px-3" href="{{ route('management.settings.roles') }}">
                <iconify-icon class="text-lg mr-1" icon="ic:outline-arrow-back"></iconify-icon>
                </iconify-icon>
                {{ __('Back') }}
            </a>
        </div>
    </div>

    {{-- Role Permission Card --}}
    <div class="rounded-md overflow-hidden">
        <div class="bg-white dark:bg-slate-800 px-5 py-7">
            <form wire:submit="create">
                <div class="flex gap-4 justify-center">
                    <div class="input-area items-center flex-1">
                        <label for="display_name" class="form-label">{{ __('Display Name') }}</label>
                        <input
                            wire:change="$dispatch('name-change')"
                            wire:model="display_name"
                            name="display_name"
                            type="text"
                            id="displayName"
                            class="form-control"
                            placeholder="{{ __('Enter your role name') }}"
                            required
                        >
                        <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                    </div>

                    <div class="input-area input-area flex-1">
                        <label for="display_name" class="form-label">{{ __('Unique Name') }}</label>
                        <input
                            wire:model="name"
                            name="name"
                            type="text"
                            id="name"
                            class="form-control"
                            required
                            disabled
                        >
                    </div>

                    <div class="input-area">
                        <label for="removable" class="capitalize form-label">
                            {{__('Removable')}}
                        </label>
                        <div class="flex mt-4 mr-2 sm:mr-4 space-x-2">
                            <label class="relative flex h-6 w-[46px] items-center rounded-full transition-all duration-150 cursor-pointer">
                                <input
                                    wire:model="removable"
                                    name="removable"
                                    id="removable"
                                    @checked($removable)
                                    type="checkbox"
                                    class="sr-only peer"
                                >
                                <div class="w-14 h-6 bg-gray-200 peer-focus:outline-none ring-0 rounded-full peer dark:bg-gray-900 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:z-10 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-500"></div>
                                <span class="absolute left-1 z-20 text-xs text-white font-Inter font-normal opacity-0 peer-checked:opacity-100">On</span>
                                <span class="absolute right-1 z-20 text-xs text-white font-Inter font-normal opacity-100 peer-checked:opacity-0">Off</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center gap-4">
                    <h3 class="flex-1 font-semibold text-2xl text-black dark:text-white py-7">
                        {{ __('Choose Permission') }}
                        <x-input-error :messages="$errors->get('permissions')" class="mt-2"/>
                    </h3>
                    <div class="flex-none self-center">
                        <button type="submit" class="btn btn-dark dark:bg-slate-700 dark:text-slate-300 m-2 !px-3 !py-2">
                            <span class="flex items-center">
                                <iconify-icon class="ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                                <span>@lang('Save')</span>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-7">
                    @foreach ($this->permissionModules as $key => $permissionModule)
                        <div class="card border border-slate-200">
                            <div class="card-header bg-slate-100 !p-3">
                                <h4 class="p-0 text-lg uppercase">{{ __($key) }}</h4>
                            </div>
                            <!-- Card Body Start -->
                            <div class="p-4">
                                <ul>
                                    @foreach ($permissionModule as $permission)

                                    <li @class(['permissionCardList', 'singlePermissionCardList' => ($loop->count == 1)])>
                                        <div class="flex items-center justify-between gap-x-3">
                                            <label for="{{$permission->name}}" class="capitalize">
                                                {{__($permission->name)}}
                                            </label>
                                            <div class="flex items-center mr-2 sm:mr-4 mt-2 space-x-2">
                                                <label class="relative inline-flex h-6 w-[46px] items-center rounded-full transition-all duration-150 cursor-pointer">
                                                    <input
                                                        wire:model="permissions"
                                                        name="permissions[]"
                                                        id="{{$permission->name}}"
                                                        value="{{$permission->id}}"
                                                        type="checkbox"
                                                        class="sr-only peer"
                                                        @disabled(!$permission->editable)
                                                    >
                                                    <div class="w-14 h-6 bg-gray-200 peer-focus:outline-none ring-0 rounded-full peer dark:bg-gray-900 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:z-10 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-500"></div>
                                                    <span class="absolute left-1 z-20 text-xs text-white font-Inter font-normal opacity-0 peer-checked:opacity-100">On</span>
                                                    <span class="absolute right-1 z-20 text-xs text-white font-Inter font-normal opacity-100 peer-checked:opacity-0">Off</span>
                                                </label>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            <!-- Card Body End -->
                        </div>
                    @endforeach
                </div>
            </form>
        </div>
    </div>
</div>
