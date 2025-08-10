<?php

use function Livewire\Volt\{state, layout, usesFileUploads};
use App\Models\Invoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

layout('layouts.app');

usesFileUploads();

state(['invoice']);

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
        'name' => 'Create',
        'url' => route('management.tools.invoices.create'),
        'active' => true
    ],
];

$resetStatus = function () {
    session()->forget('status');
};

$pageTitle = 'Create Invoice';

state(compact('breadcrumbItems', 'pageTitle'))->locked();

$create = function() {

    $this->validate([
        'invoice' => 'required|file|mimes:jpg,jpeg|max:2048', // 2MB max
    ]);

    $invoiceFile = file_get_contents($this->invoice->path());

    // $response = Http::post(env('N8N_WEBHOOK_URL'), [
    //     'imageBase64' => base64_encode($invoiceFile),
    //     'name' => $this->invoice->getClientOriginalName(),
    //     'mime_type' => $this->invoice->getClientMimeType(),
    // ]);

    try {

        $response = Http::attach('invoice', $invoiceFile , $this->invoice->getClientOriginalName())
                        ->post(env('N8N_WEBHOOK_URL'));

    } catch (\Throwable $th) {
        $this->dispatch('clear-file');

        return session()->flash(
            'status',
            [
                'message' => 'Failed to process the invoice. Please try again later.',
                'type' => 'danger'
            ]
        );
    }


    if ($response->failed()) {
        $this->dispatch('clear-file');
        return session()->flash(
            'status',
            [
                'message' => 'Failed to process the invoice. Please try again later or with other image.',
                'type' => 'danger'
            ]
        );
    }

    if (!$response->json() || !isset($response->json()['data'])) {
        $this->dispatch('clear-file');
        return session()->flash(
            'status',
            [
                'message' => 'Failed to process the invoice. Please try again later or with other image.',
                'type' => 'danger'
            ]
        );
    }

    $invoiceData = $response->json()['data'];

    $invoice = Invoice::create([
        'user_id' => auth()->user()->id,
        'number' => $invoiceData['number'] ?? '',
        'total_amount' => $invoiceData['total_amount'] ?? 0.00,
        'currency_code' => $invoiceData['currency_code'] ?? 'EUR',
        'date' => $invoiceData['date'] ?? null,
        'time' => $invoiceData['time'] ?? null,
        'payment_method' => $invoiceData['payment_method'] ?? 'Desconocido',
        'llm_name' => $invoiceData['llm_name'] ?? 'Gemmini',
        'llm_text_response' => $invoiceData['llm_text_response'] ?? '',
        'seller_info' => $invoiceData['seller_info'] ?? [],
        'items' => $invoiceData['items'] ?? [],
    ]);

    $invoice->addMedia($this->invoice->path())
            ->toMediaCollection('invoices');

    session()->flash(
        'status',
        [
            'message' => 'Invoice has been created successfully.',
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

    <div class="rounded-md overflow-hidden">
        <div class="flex justify-center bg-white dark:bg-slate-800 px-5 py-7">
            <form wire:submit="create">
                <div class="mb-3">
                    <label
                        for="formFileLg"
                        class="mb-2 inline-block text-neutral-500 dark:text-neutral-400"
                    >
                        Select your invoice file
                    </label>
                    <input
                        wire:loading.attr="disabled"
                        wire:loading.class="pointer-events-none"
                        wire:target="create"
                        wire:model="invoice"
                        required
                        accept=".jpg,.jpeg"
                        class="relative m-0 block w-full min-w-0 flex-auto cursor-pointer rounded border border-solid border-secondary-500 bg-transparent bg-clip-padding px-3 py-[0.32rem] text-base font-normal leading-[2.15] text-surface transition duration-300 ease-in-out file:-mx-3 file:-my-[0.32rem] file:me-3 file:cursor-pointer file:overflow-hidden file:rounded-none file:border-0 file:border-e file:border-solid file:border-inherit file:bg-transparent file:px-3  file:py-[0.32rem] file:text-surface focus:border-primary focus:text-gray-700 focus:shadow-inset focus:outline-none dark:border-white/70 dark:text-white  file:dark:text-white"
                        id="formFileLg"
                        type="file"
                    />
                </div>
                <div class="text-sm text-red-500 mb-3">
                    <x-input-error :messages="$errors->get('invoice')" class="mt-2"/>
                </div>
                <div class="flex flex-col justify-center align-middle gap-3">
                    {{--Submit Button--}}

                    <button
                        type="submit"
                        class="btn inline-flex justify-center btn-dark mt-4 w-full"
                        wire:loading.attr="disabled"
                        wire:loading.class="pointer-events-none"
                        wire:target="create"
                    >
                        Upload Invoice
                        <iconify-icon class="text-lg ms-2" icon="mdi:upload"></iconify-icon>
                    </button>

                    <button
                        type="button"
                        wire:loading
                        wire:target="create"
                        class="pointer-events-none inline-block rounded bg-primary px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-primary-3 transition duration-150 ease-in-out hover:bg-primary-accent-300 hover:shadow-primary-2 focus:bg-primary-accent-300 focus:shadow-primary-2 focus:outline-none focus:ring-0 active:bg-primary-600 active:shadow-primary-2 disabled:opacity-70 dark:shadow-black/30 dark:hover:shadow-dark-strong dark:focus:shadow-dark-strong dark:active:shadow-dark-strong"
                        disabled>
                        <div
                            class="inline-block h-4 w-4 animate-[spinner-grow_0.75s_linear_infinite] rounded-full bg-current align-[-0.125em] opacity-0 motion-reduce:animate-[spinner-grow_1.5s_linear_infinite]"
                            role="status"></div>
                        <span>Loading...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@script
<script>
    $wire.on('clear-file', () => {
        $wire.el.querySelector('input[type="file"]').value = '';
    });
</script>
@endscript

