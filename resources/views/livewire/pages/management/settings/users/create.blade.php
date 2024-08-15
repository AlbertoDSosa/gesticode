<?php

use function Livewire\Volt\{state, layout, computed, rules};
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Users\{User, UserProfile};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

layout('layouts.app');

rules([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'lowercase', 'string', 'email', 'max:255', 'unique:users'],
    'password' => ['required', 'string', 'min:8'],
    'role' => ['required', 'exists:roles,name']
])->messages([
    'name.required' => 'The name field is required',
    'email.required' => 'The email field is required',
    'password.required' => 'The password field is required',
    'role.required' => 'The role field is required',
    'email.email' => 'This is not a valid email',
    'email.unique' => 'This is not a valid email',
    'password.min' => 'The password must have at least 8 characters',
    'role.exists' => 'It is not a valid role',
]);

$breadcrumbItems = [
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
        'name' => 'Create',
        'url' => route('management.settings.users.create'),
        'active' => true
    ],
];

$pageTitle = 'Create User';

state([
    'name' => '',
    'email' => '',
    'password' => '',
    'role' => ''
]);

state(compact('breadcrumbItems', 'pageTitle'))->locked();

$authUser = computed(function() {
   return auth()->user();
});

$roles = computed(function() {
    $isntSuperAdmin = $this->authUser->cannot('show super admin role');
    return Role::when($isntSuperAdmin, function ($query) {
        $query->where('name', '!=', 'super-admin');
    })->get();
});

$create = function () {
    $this->validate();

    $cannotCreateSuperAdmin = $this->authUser->cannot('create super admin users');
    $superAdminRoleSelected = $this->role && $this->role == 'super-admin';
    $cannotCreateAdmin = $this->authUser->cannot('create admin users');
    $adminRoleSelected = $this->role && $this->role == 'admin';

    if($superAdminRoleSelected && $cannotCreateSuperAdmin) {
        abort(403);
    }

    if($adminRoleSelected && $cannotCreateAdmin) {
        abort(403);
    }

    $this->authorize('create', User::class);

    DB::transaction(function () {
        $user = User::create([
            'uuid' => Str::uuid(),
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password)
        ]);

        $user->assignRole($this->role);

        UserProfile::create([
            'user_id' => $user->id,
            'first_name' => $user->name
        ]);
    });

    session()->flash(
        'status',
        [
            'message' => 'User has been created successfully.',
            'type' => 'success'
        ]
    );

    $this->redirect(route('management.settings.users'));
};

?>

<div>
    {{--Breadcrumb start--}}
    <div class="mb-6">
        {{--BreadCrumb--}}
        <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />
    </div>
    {{--Breadcrumb end--}}

    {{--Create user form start--}}
    <form wire:submit="create" class="max-w-4xl m-auto">
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
                        value="{{$email}}"
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
                        value="{{$email}}"
                        required
                    >
                    <x-input-error :messages="$errors->get('email')" class="mt-2"/>
                </div>

                {{--Email input start--}}
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

                {{--Password input end--}}
                {{--Role input start--}}
                <div class="input-area">
                    <label for="role" class="form-label">{{ __('Role') }}</label>
                    <select wire:model="role" name="role" class="form-control">
                        <option value="" selected disabled>
                            {{ __('Select Role') }}
                        </option>
                        @foreach($this->roles as $role)
                            <option value="{{ $role->name }}">
                                {{ $role->display_name }}
                            </option>
                        @endforeach
                    </select>
                    <iconify-icon
                        class="absolute right-3 bottom-3 text-xl dark:text-white z-10"
                        icon="material-symbols:keyboard-arrow-down-rounded">
                    </iconify-icon>
                    <x-input-error :messages="$errors->get('role')" class="mt-2"/>
                </div>
                {{--Role input end--}}
            </div>
            <button type="submit" class="btn inline-flex justify-center btn-dark mt-4 w-full">
                {{ __('Save') }}
            </button>
        </div>

    </form>
    {{--Create user form end--}}
</div>
