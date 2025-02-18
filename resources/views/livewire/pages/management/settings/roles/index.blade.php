<?php

use function Livewire\Volt\{state, layout, usesPagination, with, computed};
use Spatie\QueryBuilder\QueryBuilder;

use Spatie\Permission\Models\Role;

usesPagination();

layout('layouts.app');

state(['search', 'rows' => 10, 'sort' => '-id'])->url();

$breadcrumbItems = [
    [
        'name' => 'Settings',
        'url' => route('management.settings'),
        'active' => false
    ],
    [
        'name' => 'Roles',
        'url' => route('management.settings.roles'),
        'active' => true
    ],
];

$pageTitle = 'Roles';

$authUser = computed(function() {
   return auth()->user();
});

with(function() {
    $isntSuperAdmin = !$this->authUser->hasRole('super-admin');
    $roles = QueryBuilder::for(Role::class)
        ->when($isntSuperAdmin, function ($query) {
            $query->where('name', '!=', 'super-admin');
        })
        ->defaultSort($this->sort)
        ->allowedSorts(['id', 'name'])
        ->where('name', 'like', "%{$this->search}%")
        ->paginate($this->rows);
    return compact('roles');
});

state(compact('breadcrumbItems', 'pageTitle'))->locked();

$resetUrl = function() {
    $this->search = null;
    $this->rows = 10;
    $this->sort = 'id';
};

$toggleSort = function($sort) {
    if($sort == $this->sort) {
        $this->sort = "-{$sort}";
    } else {
        $this->sort = $sort;
    }
};

$delete = function($id) {
    $role = Role::find($id);

    if(!$role->removable) {
        abort(403);
    }

    if($this->authUser->cannot('delete roles')) {
        abort(401);
    }

    $role->delete();
};

$resetStatus = function () {
    session()->forget('status');
};

?>

<div>
    <div class="mb-6">
        {{-- Breadcrumb --}}
        <x-breadcrumb :pageTitle="$pageTitle" :breadcrumbItems="$breadcrumbItems" />
    </div>

    {{-- Alert start --}}
    @if (session('status'))
    <x-alert :message="session('status')['message']" :type="session('status')['type']" />
    @endif
    {{-- Alert end --}}

    <div class="card">
        <header class=" card-header noborder">
            <div class="justify-end flex gap-3 items-center flex-wrap">
                {{-- Create Button start--}}
                @can('create roles')
                <a
                    class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2 !px-3"
                    href="{{route('management.settings.roles.create')}}"
                >
                    <iconify-icon icon="ic:round-plus" class="text-lg mr-1"></iconify-icon>
                    {{ __('New') }}
                </a>
                @endcan
                {{--Refresh Button start--}}
                <button
                    wire:click="resetUrl"
                    class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2.5"
                >
                    <iconify-icon icon="mdi:refresh" class="text-xl "></iconify-icon>
                </button>
            </div>
            <div class="justify-center flex flex-wrap sm:flex items-center lg:justify-end gap-3">
                <div class="relative w-full sm:w-auto flex items-center">
                    <form id="searchForm">
                        <input
                            name="search"
                            type="text"
                            class="inputField pl-8 p-2 border border-slate-200 dark:border-slate-700 rounded-md dark:bg-slate-900"
                            placeholder="Search"
                            wire:model.live="search"
                        >
                    </form>
                    <iconify-icon class="absolute text-textColor left-2 dark:text-white" icon="quill:search-alt"></iconify-icon>
                </div>
            </div>
        </header>
        <div class="card-body px-6 pb-6">
            <div class="overflow-x-auto -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden ">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead class="bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th scope="col" class="table-th ">
                                        {{ __('Sl No') }}
                                        <button
                                        wire:click="toggleSort('id')"
                                        class="btn"
                                    >
                                        @if($sort == 'id')
                                        <iconify-icon icon="heroicons:chevron-double-down"></iconify-icon>
                                        @elseif($sort == '-id')
                                        <iconify-icon icon="heroicons:chevron-double-up"></iconify-icon>
                                        @else
                                        <iconify-icon icon="heroicons:chevron-up-down"></iconify-icon>
                                        @endif
                                    </button>
                                    </th>
                                    <th scope="col" class="table-th ">
                                        {{ __('Name') }}
                                        <button
                                            wire:click="toggleSort('name')"
                                            class="btn"
                                        >
                                            @if($sort == 'name')
                                            <iconify-icon icon="heroicons:chevron-double-down"></iconify-icon>
                                            @elseif($sort == '-name')
                                            <iconify-icon icon="heroicons:chevron-double-up"></iconify-icon>
                                            @else
                                            <iconify-icon icon="heroicons:chevron-up-down"></iconify-icon>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col" class="table-th ">
                                        {{ __('Editable') }}
                                    </th>
                                    <th scope="col" class="table-th ">
                                        {{ __('Removable') }}
                                    </th>
                                    <th scope="col" class="table-th ">
                                        {{ __('Created At') }}
                                    </th>
                                    <th scope="col" class="table-th ">
                                        {{ __('Updated At') }}
                                    </th>
                                    <th scope="col" class="table-th w-20">
                                        {{ __('Action') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @forelse ($roles as $role)
                                <tr class="border border-slate-100 dark:border-slate-900 relative">
                                    <td class="table-td sticky left-0"># {{ $role->id }}</td>
                                    <td class="table-td">
                                        <span>{{ $role->display_name }}</span>
                                    </td>
                                    <td class="table-td">
                                        @if ($role->editable)
                                        <span class="badge bg-success-500 text-white capitalize">Yes</span>
                                        @else
                                        <span class="badge bg-danger-500 text-white capitalize">No</span>
                                        @endif
                                    </td>
                                    <td class="table-td">
                                        @if ($role->removable)
                                        <span class="badge bg-success-500 text-white capitalize">Yes</span>
                                        @else
                                        <span class="badge bg-danger-500 text-white capitalize">No</span>
                                        @endif
                                    </td>
                                    <td class="table-td">{{$role->created_at ? $role->created_at->toFormattedDateString() : '' }}</td>
                                    <td class="table-td">{{$role->created_at ? $role->updated_at->toFormattedDateString() : '' }}</td>
                                    <td class="table-td">
                                        <div class="action-btns space-x-2 flex">
                                            {{-- Edit --}}
                                            @can('edit roles')
                                            <a
                                                class="action-btn"
                                                href="{{route('management.settings.roles.edit', ['role' => $role])}}"
                                            >
                                                <iconify-icon icon="uil:edit"></iconify-icon>
                                            </a>
                                            @endcan
                                            {{-- delete --}}
                                            @can('delete roles')
                                            @if ($role->removable)
                                            <button
                                                x-data="deleteRole"
                                                x-on:click="exec({{$role->id}})"
                                                class="action-btn"
                                            >
                                                <iconify-icon icon="fluent:delete-24-regular">{{$role->name}}-{{$role->id}}</iconify-icon>
                                            </button>
                                            @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr class="border border-slate-100 dark:border-slate-900 relative">
                                    <td class="table-cell text-center" colspan="5">
                                        <img src="images/result-not-found.svg" alt="page not found" class="w-64 m-auto" />
                                        <h2 class="text-xl text-slate-700 mb-8 -mt-4">{{ __('No results found.') }}</h2>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <x-table-footer :data="$roles" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    Alpine.data('deleteRole', () => ({
        exec(id) {
            Swal.fire({
                title: '@lang('Are you sure ? ')',
                icon : 'question',
                showDenyButton: true,
                confirmButtonText: '@lang('Delete')',
                denyButtonText: '@lang('Cancel')',
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.delete(id);
                }
            });
        }
    }));
</script>
@endscript
