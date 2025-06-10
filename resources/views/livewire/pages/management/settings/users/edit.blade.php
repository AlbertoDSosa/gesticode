<?php

use function Livewire\Volt\{state, layout, computed, mount};
use Illuminate\Auth\Events\Registered;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use App\Models\Users\User;

layout('layouts.app');

$user = fn() => $this->user;
$rolePermissions = [];
$userPermissions = [];
$breadcrumbItems = [];

$pageTitle = 'Edit User';

$permissionModules = computed(function() {
    $isntSuperAdmin = $this->authUser->cannot('show super admin permissions');
    return Permission::when($isntSuperAdmin, function ($query) {
        $query->where('level', '!=', 'super-admin');
    })->get()->groupBy('module_name')->forget(['roles', 'permissions', 'system settings']);
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
    'name' => fn() => $this->user->name,
    'email' => fn() => $this->user->email,
    'password' => '',
    'role' => fn() => $this->user->mainRole->name,
    'active' => fn() => $this->user->active,
    'permissions'
]);

state(compact('breadcrumbItems', 'rolePermissions', 'userPermissions', 'pageTitle', 'user'))->locked();

$update = function() {
    $this->validate(
        [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'lowercase', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'active' => ['required', 'boolean'],
            'role' => ['required', 'exists:roles,name'],
            'permissions' => ['array']
        ],
        [
            'name.required' => 'The name field is required',
            'email.required' => 'The email field is required',
            'email.email' => 'This is not a valid email',
            'email.unique' => 'This is not a valid email',
            'password.min' => 'The password must have at least 8 characters',
            'role.exists' => 'It is not a valid role',
            'active.required' => 'The active field is required',

        ]
    );

    $this->authorize('update', $this->user);

    $cannotEditSuperAdmin = $this->authUser->cannot('edit super admin users');

    if($cannotEditSuperAdmin && $this->user->hasRole('super-admin')) {
        abort(401);
    }

    $cannotEditAdmin = $this->authUser->cannot('edit admin users');

    if($cannotEditAdmin && $this->user->hasRole('admin')) {
        abort(401);
    }

    $superAdminRoleSelected = $this->role === 'super-admin';
    $cannotAssignSuperAdmin = $this->authUser->cannot('assign super admin role');

    if($superAdminRoleSelected && $cannotAssignSuperAdmin) {
        abort(401);
    }

    $adminRoleSelected = $this->role === 'admin';
    $cannotAssignAdmin = $this->authUser->cannot('assign admin role');

    if($adminRoleSelected && $cannotAssignAdmin) {
        abort(401);
    }

    $userChangeToActive = !$this->user->active && $this->active;

    if($userChangeToActive && !$this->user->hasVerifiedEmail()) {
        event(new Registered($this->user));
    }

    $emailChange = $this->user->email !== $this->email;

    if($emailChange) {
        $this->user->email_verified_at = null;
        $this->user->sendEmailVerificationNotification();
    }

    if($this->password) {
        $this->user->password = bcrypt($this->password);
    }

    $this->user->name = $this->name;
    $this->user->email = $this->email;

    $validatedPermissions = [];

    foreach ($this->permissions as $permissonId) {
        $isRolePermission = collect($this->rolePermissions)->contains(function ($item) use ($permissonId) {
            return $item === $permissonId;
        });

        if($isRolePermission) {
            continue;
        }

        $permission = Permission::find((int) $permissonId);

        if(!$permission) {
            $this->addError('permissions', "The permission with ID: {$permissonId} not exists");
            return false;
        }

        if(!$permission->assignable) {
            abort(403);
        }

        $permissionHasSuperAdminLevel = $permission->level === 'super-admin';
        $isntSuperAdmin = $this->authUser->mainRole->name !== 'super-admin';

        if ($isntSuperAdmin && $permissionHasSuperAdminLevel) {
            abort(401);
        }

        $validatedPermissions[] = $permission->name;
    }

    if($this->user->mainRole->name !== $this->role) {
        $this->user->syncRoles([$this->role]);
        $this->user->syncPermissions([]);
    } else {
        $this->user->syncPermissions($validatedPermissions);
    }

    $this->user->save();

    session()->flash(
        'status',
        [
            'message' => 'User has been edited successfully.',
            'type' => 'success'
        ]
    );

    $this->redirect(route('management.settings.users'), navigate: true);
};

mount(function(User $user) {

    $this->rolePermissions = $user->mainRole->permissions()->pluck('id')->toArray();
    $this->userPermissions = $user->permissions()->pluck('id')->toArray();

    $this->permissions = collect($this->rolePermissions)->merge($this->userPermissions)->toArray();

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
    <form wire:submit="update" class="max-w-6xl m-auto">
        <div class="bg-white dark:bg-slate-800 rounded-md p-5 pb-6 max-w-4xl mb-4">
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
                <div class="flex items-center gap-x-3">
                    <label for="active" class="inputText">
                        {{__('Active')}}
                    </label>
                    <div class="flex items-center mr-2 sm:mr-4 space-x-2">
                        <label class="relative inline-flex h-6 w-[46px] items-center rounded-full transition-all duration-150 cursor-pointer">
                            <input
                                wire:model="active"
                                @checked($active)
                                name="active"
                                id="active"
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
            <button type="submit" class="btn inline-flex justify-center btn-dark mt-4 w-full">
                {{ __('Save Changes') }}
            </button>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-md p-5 pb-6">
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
                                            <label for="{{$permission->name}}" class="inputText">
                                                {{ __($permission->name) }}
                                            </label>
                                            @if ($user->mainRole->name === 'super-admin')
                                            <div class="flex items-center mr-2 sm:mr-4 mt-2 space-x-2">
                                                <label class="relative inline-flex h-6 w-[46px] items-center rounded-full transition-all duration-150 cursor-pointer">
                                                    <input
                                                        checked
                                                        type="checkbox"
                                                        class="sr-only peer"
                                                        disabled
                                                    >
                                                    <div class="w-14 h-6 bg-gray-200 peer-focus:outline-none ring-0 rounded-full peer dark:bg-gray-900 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:z-10 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-500"></div>
                                                    <span class="absolute left-1 z-20 text-xs text-white font-Inter font-normal opacity-0 peer-checked:opacity-100">On</span>
                                                    <span class="absolute right-1 z-20 text-xs text-white font-Inter font-normal opacity-100 peer-checked:opacity-0">Off</span>
                                                </label>
                                            </div>
                                            @else
                                            <div class="flex items-center mr-2 sm:mr-4 mt-2 space-x-2">
                                                <label class="relative inline-flex h-6 w-[46px] items-center rounded-full transition-all duration-150 cursor-pointer">
                                                    <input
                                                        wire:model="permissions"
                                                        name="permissions[]"
                                                        @checked(in_array($permission->id, $this->permissions))
                                                        id="{{$permission->name}}"
                                                        value="{{ $permission->id }}"
                                                        type="checkbox"
                                                        class="sr-only peer"
                                                        @disabled(in_array($permission->id, $this->rolePermissions) || !$permission->assignable)
                                                    >
                                                    <div class="w-14 h-6 bg-gray-200 peer-focus:outline-none ring-0 rounded-full peer dark:bg-gray-900 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:z-10 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-500"></div>
                                                    <span class="absolute left-1 z-20 text-xs text-white font-Inter font-normal opacity-0 peer-checked:opacity-100">On</span>
                                                    <span class="absolute right-1 z-20 text-xs text-white font-Inter font-normal opacity-100 peer-checked:opacity-0">Off</span>
                                                </label>
                                            </div>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <!-- Card Body End -->
                    </div>
                @endforeach
            </div>
        </div>
    </form>
    {{--Create user form end--}}
</div>
