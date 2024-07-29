<?php

use function Livewire\Volt\{state, layout, computed, mount};
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Users\User;

layout('layouts.app');

$user = null;
$permissions = [];
$rolePermissions = [];
$breadcrumbItems = [];

$pageTitle = 'Edit User';

$permissionModules = computed(function() {
    return Permission::all()->groupBy('module_name');
});

$roles = computed(function() {
    return Role::all();
});

state(compact('breadcrumbItems', 'pageTitle', 'permissions', 'rolePermissions', 'user'))->locked();

mount(function(User $user) {
    $this->user = $user;
    $rolePemissions = $user->mainRole->permissions()->pluck('id')->toArray();
    $userPermissions = $user->permissions()->pluck('id')->toArray();

    $this->rolePermissions = $rolePemissions;
    $this->permissions = collect($rolePemissions)->merge($userPermissions);

    $this->breadcrumbItems = [
        [
            'name' => 'Settings',
            'url' => route('management.settings'),
            'active' => false
        ],
        [
            'name' => 'Users',
            'url' => route('management.settings.users'),
            'active' => false
        ],
        [
            'name' => 'Edit',
            'url' => route('management.settings.users.edit', ['user' => $user]),
            'active' => true
        ],
    ];
});

?>

<div>
    {{--Breadcrumb start--}}
    <div class="mb-6">
        <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />
    </div>
    {{--Breadcrumb end--}}

    {{--Create user form start--}}
    <form class="max-w-4xl m-auto">
        <div class="bg-white dark:bg-slate-800 rounded-md p-5 pb-6">
            <div class="grid sm:grid-cols-1 gap-x-8 gap-y-4">
                {{--Name input end--}}
                <div class="input-area">
                    <label for="name" class="form-label">{{ __('Name') }}</label>
                    <input name="name" type="text" id="name" class="form-control"
                           placeholder="{{ __('Enter your name') }}" value="{{ $user->name }}" required>
                    <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                </div>

                {{--Email input start--}}
                <div class="input-area">
                    <label for="email" class="form-label">{{ __('Email') }}</label>
                    <input name="email" type="email" id="email" class="form-control"
                           placeholder="{{ __('Enter your email') }}" value="{{ $user->email }}" required>
                    <x-input-error :messages="$errors->get('email')" class="mt-2"/>
                </div>

                {{--Password input start--}}
                <div class="input-area">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <input name="password" type="password" id="password" class="form-control"
                           placeholder="{{ __('Enter Password') }}">
                    <x-input-error :messages="$errors->get('password')" class="mt-2"/>
                </div>

                {{--Role input start--}}
                <div class="input-area">
                    <label for="role" class="form-label">{{ __('Role') }}</label>
                    <select name="role" class="form-control">
                        <option value="" selected disabled>
                            {{ __('Select Role') }}
                        </option>
                        @foreach($this->roles as $role)
                            <option value="{{ $role->id }}" @selected($user->hasRole($role->name))>
                                {{ $role->display_name }}
                            </option>
                        @endforeach
                    </select>
                    <iconify-icon
                        class="absolute right-3 bottom-3 text-xl dark:text-white z-10"
                        icon="material-symbols:keyboard-arrow-down-rounded"
                    >
                    </iconify-icon>
                </div>
                {{--Role input end--}}
            </div>
            <button type="submit" class="btn inline-flex justify-center btn-dark mt-4 w-full">
                {{ __('Save Changes') }}
            </button>
        </div>

    </form>
    {{--Create user form end--}}
</div>
