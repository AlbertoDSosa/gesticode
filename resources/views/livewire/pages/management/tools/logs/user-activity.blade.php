<?php

use function Livewire\Volt\{state, layout, usesPagination, with};
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Users\User;

usesPagination();

layout('layouts.app');

state(['search', 'rows' => 10, 'sort' => 'id'])->url();

$breadcrumbItems = [
    [
        'name' => 'Tools',
        'url' => route('management.tools'),
        'active' => false
    ],
    [
        'name' => 'User Activity',
        'url' => route('management.tools.logs.user-activity'),
        'active' => true
    ],
];

$pageTitle = 'User Activity Logs';

with(function() {
    $activities = collect([]);
    return compact('activities');
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
    $user = User::find($id);
    $user->delete();
};

?>

<div>
    <div class=" mb-6">
        {{--Breadcrumb start--}}
        <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />
    </div>


    <div class="card">
        <header class=" card-header noborder">
            <div class="justify-end flex gap-3 items-center flex-wrap">
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
                                    <th scope="col" class="table-th">
                                        {{ __('Activity name') }}
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
                                        {{ __('user name') }}
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
                                    <th scope="col" class="table-th">
                                        {{ __('Date') }}
                                    </th>
                                    <th scope="col" class="table-th w-20">
                                        {{ __('Action') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @forelse ($activities as $activity)
                                <tr>
                                    <td class="table-td">
                                        # {{ $activity->id }}
                                    </td>
                                    <td class="table-td">
                                        {{$activity->name}}
                                    </td>
                                    <td class="table-td">
                                        <div class="flex items-center">
                                            <div class="flex-none">
                                                <div class="w-8 h-8 rounded-[100%] ltr:mr-3 rtl:ml-3">
                                                    <img class="w-full h-full rounded-[100%] object-cover" src="{{ Avatar::create($user->name)->toBase64() }}" alt="image">
                                                </div>
                                            </div>
                                            <div class="flex-1 text-start">
                                                <h4 class="text-sm font-medium text-slate-600 whitespace-nowrap">
                                                    {{ $activity->user->name }}
                                                </h4>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-td">
                                        {{ $activity->created_at->diffForHumans() }}
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
                        @if ($activities->isNotEmpty())
                        <x-table-footer :data="$activities" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
