<?php

use function Livewire\Volt\{state, layout, usesPagination, with};
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Users\User;
use App\Models\Invoice;
use Illuminate\Support\Str;

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
        'name' => 'User Invoices',
        'url' => route('management.tools.invoices'),
        'active' => true
    ],
];

$pageTitle = 'User Invoices';

with(function() {
    $search = addslashes($this->search ?? '');
    $invoices = QueryBuilder::for(Invoice::class)
        ->defaultSort($this->sort)
        ->where('number', 'like', "%{$search}%")
        ->paginate($this->rows);
    return compact('invoices');
});

state(compact('breadcrumbItems', 'pageTitle'))->locked();

$resetUrl = function() {
    $this->search = null;
    $this->rows = 10;
    $this->sort = 'id';
};

$resetStatus = function () {
    session()->forget('status');
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

    {{-- Alert start --}}
    @if (session('status'))
    <x-alert :message="session('status')['message']" :type="session('status')['type']" />
    @endif
    {{-- Alert end --}}

    <div class="card">
        <header class=" card-header noborder">
            <div class="justify-end flex gap-3 items-center flex-wrap">
                {{--Refresh Button start--}}
                <button
                    wire:click="resetUrl"
                    class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-3"
                >
                    <iconify-icon icon="mdi:refresh" class="text-xl"></iconify-icon>
                </button>
                {{--Refresh Button end--}}
                <a href="{{route('management.tools.invoices.create')}}" wire:navigate class="btn inline-flex justify-center btn-outline-dark text-lg">Add Invoice</a>

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
                                        Sl No
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
                                        Number
                                    </th>

                                    <th scope="col" class="table-th ">
                                        Status
                                    </th>

                                    <th scope="col" class="table-th ">
                                        Model Name
                                    </th>

                                    <th scope="col" class="table-th ">
                                        Payment Method
                                    </th>

                                    <th scope="col" class="table-th">
                                        Amount
                                    </th>

                                    <th scope="col" class="table-th">
                                        Date
                                    </th>

                                    <th scope="col" class="table-th">
                                        Time
                                    </th>

                                    <th scope="col" class="table-th">
                                        Created at
                                    </th>
                                    {{-- <th scope="col" class="table-th w-20">
                                        Action
                                    </th> --}}
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @forelse ($invoices as $invoice)
                                <tr>
                                    <td class="table-td">
                                        # {{ $invoice->id }}
                                    </td>

                                    <td class="table-td">
                                        {{$invoice->number}}
                                    </td>

                                    <td class="table-td">
                                        {{ $invoice->status }}
                                    </td>

                                    <td class="table-td">
                                        {{ $invoice->llm_name }}
                                    </td>

                                    <td class="table-td">
                                        {{ $invoice->payment_method}}
                                    </td>

                                    <td class="table-td">
                                        {{$invoice->total_amount}} €
                                    </td>

                                    <td class="table-td">
                                        {{ $invoice->date ?? 'N/A' }}
                                    </td>

                                    <td class="table-td">
                                        {{ $invoice->time ?? 'N/A' }}
                                    </td>

                                    <td class="table-td">
                                        {{ $invoice->created_at->diffForHumans() }}
                                    </td>

                                    {{-- <td class="table-td">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('management.tools.invoices.show', $invoice->id) }}" class="btn btn-primary btn-sm" wire:navigate>
                                                <iconify-icon icon="heroicons:eye"></iconify-icon>
                                            </a>
                                            <a href="{{ route('management.tools.invoices.edit', $invoice->id) }}" class="btn btn-secondary btn-sm" wire:navigate>
                                                <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                            </a>
                                            <button
                                                class="btn btn-danger btn-sm"
                                                wire:click="$dispatch('confirmDelete', {{ $invoice->id }})"
                                            >
                                                <iconify-icon icon="heroicons:trash"></iconify-icon>
                                            </button>
                                        </div>
                                    </td> --}}

                                </tr>
                                @empty
                                <tr class="border border-slate-100 dark:border-slate-900 relative">
                                    <td class="table-cell text-center" colspan="5">
                                        <img src="{{asset('images/result-not-found.svg')}}" alt="page not found" class="w-64 m-auto" />
                                        <h2 class="text-xl text-slate-700 mb-8 -mt-4">No results found.</h2>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @if ($invoices->isNotEmpty())
                        <x-table-footer :data="$invoices" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
