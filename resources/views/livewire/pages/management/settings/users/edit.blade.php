<?php

use function Livewire\Volt\{state, layout, computed, mount};
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use App\Models\Users\User;

layout('layouts.app');

$user = fn() => $this->user;
$permissions = [];
$rolePermissions = [];
$breadcrumbItems = [];

$pageTitle = 'Edit User';

$permissionModules = computed(function() {
    return Permission::all()->groupBy('module_name');
});

$authUser = computed(function() {
   return auth()->user();
});

$roles = computed(function() {
    $isntSuperAdmin = $this->authUser->cannot('show super admin role');
    return Role::when($isntSuperAdmin, function ($query) {
        $query->where('name', '!=', 'super-admin');
    })->get();
});

state([
    'name' => fn() => $this->user->name ,
    'email' => fn() => $this->user->email,
    'password' => '',
    'role' => fn() => $this->user->mainRole->name
]);

state(compact('breadcrumbItems', 'pageTitle', 'permissions', 'rolePermissions', 'user'))->locked();

$update = function() {
    $this->validate(
        [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'lowercase', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'exists:roles,name']
        ],
        [
            'name.required' => 'The name field is required',
            'email.required' => 'The email field is required',
            'password.required' => 'The password field is required',
            'role.required' => 'The role field is required',
            'email.email' => 'This is not a valid email',
            'email.unique' => 'This is not a valid email',
            'password.min' => 'The password must have at least 8 characters',
            'role.exists' => 'It is not a valid role',
        ]
    );

    $cannotEditSuperAdmin = $this->authUser->cannot('edit super admin users');
    $cannotEditAdmin = $this->authUser->cannot('edit admin users');

    if($cannotEditSuperAdmin && $this->user->hasRole('super-admin')) {
        abort(401);
    }

    if($cannotEditAdmin && $this->user->hasRole('admin')) {
        abort(401);
    }

    $this->authorize('update', $this->user);

    $superAdminRoleSelected = $this->role && $this->role === 'super-admin';
    $cannotAssignSuperAdmin = $this->authUser->cannot('assign super admin role');
    $adminRoleSelected = $this->role && $this->role === 'admin';
    $cannotAssignSuperAdmin = $this->authUser->cannot('assign super admin role');

    if($superAdminRoleSelected && $cannotAssignSuperAdmin) {
        abort(401);
    }

    if($adminRoleSelected && $cannotAssignAdmin) {
        abort(401);
    }

    if($this->password) {
        $this->user->password = bcrypt($this->password);
    }

    $this->user->name = $this->name;
    $this->user->email = $this->email;

    $this->user->syncRoles([$this->role]);

    $this->user->save();

    session()->flash(
        'status',
        [
            'message' => 'User has been edited successfully.',
            'type' => 'success'
        ]
    );

    $this->redirect(route('management.settings.users'));
};

mount(function(User $user) {

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
    <form wire:submit="update" class="max-w-4xl m-auto">
        <div class="bg-white dark:bg-slate-800 rounded-md p-5 pb-6">
            <div class="grid sm:grid-cols-1 gap-x-8 gap-y-4">
                {{--Name input end--}}
                <div class="input-area">
                    <label for="name" class="form-label">{{ __('Name') }}</label>
                    <input
                        wire:model="name"
                        name="name"
                        type="text"
                        id="name"
                        class="form-control"
                        placeholder="{{ __('Enter your name') }}"
                        required
                    >
                    <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                </div>

                {{--Email input start--}}
                <div class="input-area">
                    <label for="email" class="form-label">{{ __('Email') }}</label>
                    <input
                        wire:model="email"
                        name="email"
                        type="email"
                        id="email"
                        class="form-control"
                        placeholder="{{ __('Enter your email') }}"
                        required
                    >
                    <x-input-error :messages="$errors->get('email')" class="mt-2"/>
                </div>

                {{--Password input start--}}
                <div class="input-area">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <input
                        wire:model="password"
                        name="password"
                        type="password"
                        id="password"
                        class="form-control"
                        placeholder="{{ __('Enter Password') }}"
                    >
                    <x-input-error :messages="$errors->get('password')" class="mt-2"/>
                </div>

                {{--Role input start--}}
                <div class="input-area">
                    <label for="role" class="form-label">{{ __('Role') }}</label>
                    <select
                        wire:model="role"
                        name="role"
                        class="form-control"
                    >
                        @foreach($this->roles as $itemRole)
                            <option
                                value="{{ $itemRole->name }}"
                                @selected($role === $itemRole->name)
                            >
                                {{ $itemRole->display_name }}
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
