<?php

use function Livewire\Volt\{state, layout, computed, usesPagination, with};
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Str;

use Spatie\Permission\Models\Permission;

usesPagination();

layout('layouts.app');

state(['search', 'rows' => 10, 'sort' => 'id'])->url();

$breadcrumbItems = [
    [
        'name' => 'Settings',
        'url' => 'management.settings',
        'active' => false
    ],
    [
        'name' => 'Permissions',
        'url' => 'management.settings.permissions',
        'active' => true
    ],
];

$pageTitle = 'Permissions';


with(function() {
    $permissions = QueryBuilder::for(Permission::class)
            ->defaultSort($this->sort)
            ->allowedSorts(['id', 'name', 'created_at', 'updated_at'])
            ->where('name', 'like', "%{$this->search}%")
            ->paginate($this->rows);
    return compact('permissions');

});

state(compact('breadcrumbItems', 'pageTitle'));

$resetUrl = function() {
    $this->search = '';
    $this->rows = 10;
    $this->sort = 'id';
};

$toggleSort = function($sort) {
    if($sort == $this->sort) {
        $this->sort = "-{$sort}";
    } else {
        $this->sort = $sort;
    }
}

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
        <header class=" card-header noborder">
            <div class="justify-end flex gap-3 items-center flex-wrap">
                {{-- Create Button start--}}
                @can('create permissions')
                <a class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2 !px-3" href="#">
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
                        <input wire:model.live="search" type="search" class="inputField pl-8 p-2 border border-slate-200 dark:border-slate-700 rounded-md dark:bg-slate-900" placeholder="Search">
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
                                    <td class="table-td">{{ $permission->created_at ? $permission->created_at->toFormattedDateString() : '' }}</td>
                                    <td class="table-td">{{ $permission->created_at ? $permission->updated_at->toFormattedDateString() : '' }}</td>
                                    <td class="table-td">
                                        <div class="action-btns space-x-2 flex">
                                            {{-- Edit --}}
                                            @can('update permissions')
                                            <a class="action-btn" href="#">
                                                <iconify-icon icon="uil:edit"></iconify-icon>
                                            </a>
                                            @endcan
                                            {{-- delete --}}
                                            @can('delete permisions')
                                            <form id="deleteForm-{{ $permission->id }}" class="cursor-pointer">
                                                <a class="action-btn" type="submit">
                                                    <iconify-icon icon="fluent:delete-24-regular"></iconify-icon>
                                                </a>
                                            </form>
                                            @endcan
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
