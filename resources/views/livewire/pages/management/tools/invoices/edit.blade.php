<?php

use function Livewire\Volt\{state, layout, mount};
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Image\Image;

layout('layouts.app');

state([
    'invoice',
    'number',
    'total_amount',
    'currency_code',
    'payment_method',
    'status',
    'time',
    'date',
    'llm_name',
    'llm_text_response'
]);

mount(function (Invoice $invoice) {
    $this->invoice = $invoice;

    $this->image = Image::load($invoice->getFirstMedia('invoices')->getPath())
    ->base64();

    // Initialize state with existing invoice data
    $this->number = $this->invoice->number;
    $this->total_amount = $this->invoice->total_amount;
    $this->currency_code = $this->invoice->currency_code;
    $this->payment_method = $this->invoice->payment_method;
    $this->status = $this->invoice->status;
    $this->time = optional($this->invoice->created_at)->format('H:i:s');
    $this->date = optional($this->invoice->created_at)->format('Y-m-d');
    $this->llm_name = $this->invoice->llm_name;
    $this->llm_text_response = $this->invoice->llm_text_response;
});

$breadcrumbItems = [
    [
        'name' => 'Tools',
        'url' => route('management.tools'),
        'active' => false
    ],
    [
        'name' => 'Invoices',
        'url' => route('management.tools.invoices'),
        'active' => false
    ],
    [
        'name' => 'Edit',
        'url' => '',
        'active' => true
    ],
];

$pageTitle = 'Edit Invoice';

state(compact('breadcrumbItems', 'pageTitle'))->locked();

$updateInvoice = function () {
    $validated = $this->validate([
        'number' => ['nullable', 'string', 'max:255'],
        'total_amount' => ['required', 'numeric', 'min:0'],
        'currency_code' => ['required', 'string', 'max:3'],
        'payment_method' => ['required', 'in:Desconocido,Targeta Bancaria,Efectivo'],
        'status' => ['required', 'in:pendiente,aprobada,rechazada'],
        'time' => ['nullable', 'date_format:H:i:s'],
        'date' => ['nullable', 'date'],
        'llm_name' => ['nullable', 'string', 'max:255'],
        'llm_text_response' => ['nullable', 'string'],
    ]);

    $this->invoice->update($validated);

    session()->flash(
        'status',
        [
            'message' => 'Invoice has been updated successfully.',
            'type' => 'success'
        ]
    );

    $this->redirect(route('management.tools.invoices'), navigate: true);
};

?>

<div class="space-y-8">
    <div class="block sm:flex items-center justify-between mb-6">
        {{--Breadcrumb--}}
        <x-breadcrumb :pageTitle="$pageTitle" :breadcrumbItems="$breadcrumbItems"/>

        <div class="text-end">
            <a class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2 !px-3" href="{{ route('management.tools.invoices') }}">
                <iconify-icon class="text-lg mr-1" icon="ic:outline-arrow-back"></iconify-icon>
                Back
            </a>
        </div>
    </div>

    {{-- Alert start --}}
    @if (session('status'))
    <x-alert :message="session('status')['message']" :type="session('status')['type']" />
    @endif
    {{-- Alert end --}}

    {{-- Main Content: Two Card Layout --}}
    <div class="flex lg:flex-row gap-6">
        {{-- Left Card: Invoice Image --}}
        <div class="w-full lg:w-1/2">
            <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Invoice Image
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Original uploaded invoice file
                    </p>
                </div>
                <div class="px-5 py-6">
                    @if($this->image)
                        <div class="relative">
                            <img
                                src="{{ $this->image }}"
                                alt="Invoice Image"
                                class="w-full h-auto rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 max-h-[600px] object-contain"
                            />
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-gray-500 dark:text-gray-400">
                            <iconify-icon icon="mdi:image-off" class="text-6xl mb-4"></iconify-icon>
                            <p class="text-lg font-medium">No image available</p>
                            <p class="text-sm">No invoice image has been uploaded for this invoice.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Card: Invoice Form --}}
        <div class="w-full lg:w-1/2">
            <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Invoice Details
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Edit the invoice information below
                    </p>
                </div>
                <div class="px-5 py-6">
                    <form wire:submit="updateInvoice" class="space-y-6">

                {{-- Invoice Number --}}
                <div>
                    <label for="number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Invoice Number
                    </label>
                    <input
                        wire:model="number"
                        type="text"
                        id="number"
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:bg-slate-700 dark:text-white"
                        placeholder="Enter invoice number"
                    />
                    <x-input-error :messages="$errors->get('number')" class="mt-2"/>
                </div>

                {{-- Total Amount --}}
                <div>
                    <label for="total_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Total Amount *
                    </label>
                    <input
                        wire:model="total_amount"
                        type="number"
                        step="0.01"
                        min="0"
                        id="total_amount"
                        required
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:bg-slate-700 dark:text-white"
                        placeholder="0.00"
                    />
                    <x-input-error :messages="$errors->get('total_amount')" class="mt-2"/>
                </div>

                {{-- Currency Code --}}
                <div>
                    <label for="currency_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Currency Code *
                    </label>
                    <select
                        wire:model="currency_code"
                        id="currency_code"
                        required
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:bg-slate-700 dark:text-white"
                    >
                        <option value="">Select currency</option>
                        <option value="EUR">EUR</option>
                        <option value="USD">USD</option>
                        <option value="GBP">GBP</option>
                    </select>
                    <x-input-error :messages="$errors->get('currency_code')" class="mt-2"/>
                </div>

                {{-- Payment Method --}}
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Payment Method *
                    </label>
                    <select
                        wire:model="payment_method"
                        id="payment_method"
                        required
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:bg-slate-700 dark:text-white"
                    >
                        <option value="">Select payment method</option>
                        <option value="Desconocido">Desconocido</option>
                        <option value="Targeta Bancaria">Targeta Bancaria</option>
                        <option value="Efectivo">Efectivo</option>
                    </select>
                    <x-input-error :messages="$errors->get('payment_method')" class="mt-2"/>
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Status *
                    </label>
                    <select
                        wire:model="status"
                        id="status"
                        required
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:bg-slate-700 dark:text-white"
                    >
                        <option value="">Select status</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="aprobada">Aprobada</option>
                        <option value="rechazada">Rechazada</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2"/>
                </div>

                {{-- Date --}}
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Date
                    </label>
                    <input
                        wire:model="date"
                        type="date"
                        id="date"
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:bg-slate-700 dark:text-white"
                    />
                    <x-input-error :messages="$errors->get('date')" class="mt-2"/>
                </div>

                {{-- Time --}}
                <div>
                    <label for="time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Time
                    </label>
                    <input
                        wire:model="time"
                        type="time"
                        id="time"
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:bg-slate-700 dark:text-white"
                    />
                    <x-input-error :messages="$errors->get('time')" class="mt-2"/>
                </div>

                {{-- LLM Name --}}
                <div>
                    <label for="llm_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        LLM Model Name
                    </label>
                    <input
                        wire:model="llm_name"
                        type="text"
                        id="llm_name"
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:bg-slate-700 dark:text-white"
                        placeholder="Enter LLM model name"
                    />
                    <x-input-error :messages="$errors->get('llm_name')" class="mt-2"/>
                </div>

                {{-- LLM Text Response --}}
                <div>
                    <label for="llm_text_response" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        LLM Text Response
                    </label>
                    <textarea
                        wire:model="llm_text_response"
                        id="llm_text_response"
                        rows="15"
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:bg-slate-700 dark:text-white"
                        placeholder="Enter LLM text response..."
                    ></textarea>
                    <x-input-error :messages="$errors->get('llm_text_response')" class="mt-2"/>
                </div>

                        {{-- Form Actions --}}
                        <div class="flex items-center justify-end gap-4 pt-4">
                            <a
                                href="{{ route('management.tools.invoices') }}"
                                class="btn inline-flex justify-center btn-outline-dark rounded-md"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="btn inline-flex justify-center btn-dark rounded-md"
                                wire:loading.attr="disabled"
                                wire:loading.class="pointer-events-none opacity-70"
                                wire:target="updateInvoice"
                            >
                                <span wire:loading.remove wire:target="updateInvoice">
                                    Update Invoice
                                    <iconify-icon class="text-lg ms-2" icon="mdi:content-save"></iconify-icon>
                                </span>
                                <span wire:loading wire:target="updateInvoice">
                                    <div class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]" role="status"></div>
                                    Updating...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
