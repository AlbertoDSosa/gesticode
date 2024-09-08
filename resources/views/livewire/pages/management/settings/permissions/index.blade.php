<?php

use function Livewire\Volt\{state, layout, usesPagination, with, computed};
use Spatie\QueryBuilder\QueryBuilder;

use Spatie\Permission\Models\Permission;

usesPagination();

layout('layouts.app');

state(['search', 'rows' => 10, 'sort' => '-id', 'level', 'module'])->url();

$breadcrumbItems = [
    [
        'name' => 'Settings',
        'url' => route('management.settings'),
        'active' => false
    ],
    [
        'name' => 'Permissions',
        'url' => route('management.settings.permissions'),
        'active' => true
    ],
];

$pageTitle = 'Permissions';

$authUser = computed(function() {
   return auth()->user();
});

$modules = computed(function () {
    return Permission::all()->groupBy('module_name')->keys();
});

$permissionLevels = computed(function () {
    if($this->authUser->hasRole('super-admin')) {
        return ['regular', 'admin', 'super-admin'];
    }
    return ['regular', 'admin'];
});

with(function() {
    $isntSuperAdmin = $this->authUser->cannot('show super admin permissions');
    $permissions = QueryBuilder::for(Permission::class)
        ->when($isntSuperAdmin, function ($query) {
            $query->where('level', '!=', 'super-admin');
        })
        ->defaultSort($this->sort)
        ->allowedSorts(['id', 'name'])
        ->when($this->level, function ($query, $level) {
            $query->where('level', $level);
            $this->resetPage();
        })
        ->when($this->module, function ($query, $module) {
            $query->where('module_name', $module);
            $this->resetPage();
        })
        ->where('name', 'like', "%{$this->search}%")
        ->paginate($this->rows);
    return compact('permissions');
});

state(compact('breadcrumbItems', 'pageTitle'))->locked();

$resetUrl = function() {
    $this->search = null;
    $this->level = null;
    $this->module = null;
    $this->rows = 10;
    $this->sort = '-id';
    $this->resetPage();
};

$toggleSort = function($sort) {
    if($sort == $this->sort) {
        $this->sort = "-{$sort}";
    } else {
        $this->sort = $sort;
    }
};

$resetStatus = function () {
    session()->forget('status');
};

$delete = function($id) {
    $permission = Permission::find($id);

    if(!$permission) {
        abort(404);
    }

    if(!$permission->removable) {
        abort(403);
    }

    if($this->authUser->cannot('delete permissions')) {
        abort(401);
    }

    if(
        $this->authUser->cannot('delete super admin permissions') &&
        $permission->level === 'super-admin'
    ) {
        abort(401);
    }

    if(
        $this->authUser->cannot('delete admin permissions') &&
        $permission->level === 'admin'
    ) {
        abort(401);
    }

    $permission->delete();
};

?>

<div>
    <div class="mb-6">
        {{--Breadcrumb--}}
        <x-breadcrumb :pageTitle="$pageTitle" :breadcrumbItems="$breadcrumbItems" />
    </div>

    {{-- Alert start --}}
    @if (session('status'))
    <x-alert :message="session('status')['message']" :type="session('status')['type']" />
    @endif
    {{-- Alert end --}}

    <div class="card">
        <header class="card-header noborder">
            <div class="justify-end flex gap-3 items-center flex-wrap">
                {{-- Create Button start--}}
                @can('create permissions')
                <a
                    class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2 !px-3"
                    href="{{route('management.settings.permissions.create')}}"
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
                    <iconify-icon icon="mdi:refresh" class="text-xl"></iconify-icon>
                </button>
            </div>
            <div class="justify-center flex sm:flex items-center lg:justify-end gap-4">
                <div class="input-area">
                    <select
                        wire:model.live="module"
                        id="selectModule"
                        class="rounded"
                    >
                        <option
                            selected
                            value=""
                            class="dark:bg-slate-700 w-full"
                        >
                            {{__('Select Module')}}
                        </option>
                        @foreach ($this->modules as $itemModule)
                        <option
                            value="{{$itemModule}}"
                            class="dark:bg-slate-700"
                        >
                            {{$itemModule}}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="input-area">
                    <select
                        wire:model.live="level"
                        id="selectLevel"
                        class="rounded"
                    >
                        <option
                            selected
                            value=""
                            class="dark:bg-slate-700 w-full"
                        >
                            {{__('Select Level')}}
                        </option>
                        @foreach ($this->permissionLevels as $itemLevel)
                        <option
                            value="{{$itemLevel}}"
                            class="dark:bg-slate-700"
                        >
                            {{$itemLevel}}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="relative w-full sm:w-auto flex items-center">
                    <form id="searchForm">
                        <input
                            wire:model.live="search"
                            type="search"
                            class="inputField pl-8 p-2 border border-slate-200 dark:border-slate-700 rounded-md dark:bg-slate-900"
                            placeholder="Search"
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
                                        {{ __('Module') }}
                                    </th>
                                    <th scope="col" class="table-th ">
                                        {{ __('Level') }}
                                    </th>
                                    <th scope="col" class="table-th ">
                                        {{ __('Assignable') }}
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
                                @forelse ($permissions as $permission)
                                <tr class="border border-slate-100 dark:border-slate-900 relative">
                                    <td class="table-td sticky left-0"># {{ $permission->id }}</td>
                                    <td class="table-td">
                                        <span>{{ $permission->name }}</span>
                                    </td>
                                    <td class="table-td">
                                        <span>{{ $permission->module_name }}</span>
                                    </td>
                                    <td class="table-td">
                                        @if ($permission->level === 'super-admin')
                                        <span class="badge bg-danger-500 text-white capitalize">{{ $permission->level }}</span>
                                        @elseif ($permission->level === 'admin')
                                        <span class="badge bg-warning-500 text-white capitalize">{{ $permission->level }}</span>
                                        @else
                                        <span class="badge bg-secondary-500 text-white capitalize">{{ $permission->level }}</span>
                                        @endif
                                    </td>
                                    <td class="table-td">
                                        @if ($permission->assignable)
                                        <span class="badge bg-success-500 text-white capitalize">Yes</span>
                                        @else
                                        <span class="badge bg-danger-500 text-white capitalize">No</span>
                                        @endif
                                    </td>
                                    <td class="table-td">
                                        @if ($permission->editable)
                                        <span class="badge bg-success-500 text-white capitalize">Yes</span>
                                        @else
                                        <span class="badge bg-danger-500 text-white capitalize">No</span>
                                        @endif
                                    </td>
                                    <td class="table-td">
                                        @if ($permission->removable)
                                        <span class="badge bg-success-500 text-white capitalize">Yes</span>
                                        @else
                                        <span class="badge bg-danger-500 text-white capitalize">No</span>
                                        @endif
                                    </td>
                                    <td class="table-td">{{ $permission->created_at ? $permission->created_at->toFormattedDateString() : '' }}</td>
                                    <td class="table-td">{{ $permission->created_at ? $permission->updated_at->toFormattedDateString() : '' }}</td>
                                    <td class="table-td">
                                        <div class="action-btns space-x-2 flex">
                                            {{-- Edit --}}
                                            @can('update permissions')
                                            <a
                                                class="action-btn"
                                                href="{{route('management.settings.permissions.edit', ['permission' => $permission])}}"
                                            >
                                                <iconify-icon icon="uil:edit"></iconify-icon>
                                            </a>
                                            @endcan
                                            {{-- delete --}}
                                            @if ($permission->removable)
                                            @can('delete permissions')
                                            <button
                                                x-data="deletePermission"
                                                x-on:click="exec({{$permission->id}})"
                                                class="action-btn"
                                            >
                                                <iconify-icon icon="fluent:delete-24-regular"></iconify-icon>
                                            </button>
                                            @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr class="border border-slate-100 dark:border-slate-900 relative">
                                    <td class="table-cell text-center" colspan="5">
                                        <img src="{{asset('images/result-not-found.svg')}}" alt="page not found" class="w-64 m-auto" />
                                        <h2 class="text-xl text-slate-700 mb-8 -mt-4">{{ __('No results found.') }}</h2>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <x-table-footer :data="$permissions" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    Alpine.data('deletePermission', () => ({
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
