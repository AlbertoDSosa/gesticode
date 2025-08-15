<?php

use function Livewire\Volt\{layout, state, mount, computed};
use App\Neuron\RAG\InvoicesRAG;
use NeuronAI\Chat\Messages\UserMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

layout('layouts.app');

state([
    'query' => '',
    'response' => '',
    'searchResults' => [],
    'error' => null,
    'searchMode' => 'chat'
]);

$pageTitle = 'Advanced Search';

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
        'name' => 'Advanced Search',
        'url' => route('management.tools.invoices.advanced-search'),
        'active' => true
    ],
];

state(compact('breadcrumbItems', 'pageTitle'))->locked();

$hasEmbeddings = computed(function() {
    return \App\Models\Invoice::where('user_id', Auth::id())
        ->whereNotNull('content_embedding')
        ->exists();
});


$searchWithChat = function() {
    if (empty($this->query)) {
        $this->error = 'Por favor, ingresa una consulta';
        return;
    }

    $this->error = null;
    $this->response = '';

    try {
        $rag = InvoicesRAG::forAuthUser();
        $message = new UserMessage($this->query);
        $response = $rag->chat($message);

        $this->response = $response->getContent();
    } catch (\Exception $e) {
        $this->error = 'Error al procesar la consulta: ' . $e->getMessage();
    }
};


$searchDocuments = function() {
    if (empty($this->query)) {
        $this->error = 'Por favor, ingresa una consulta';
        return;
    }

    $this->error = null;
    $this->searchResults = [];

    try {

        $embeddingsProvider = new \NeuronAI\RAG\Embeddings\GeminiEmbeddingsProvider(
            key: env('GEMINI_API_KEY'),
            model: env('GEMINI_EMBEDDINGS_MODEL', 'text-embedding-004')
        );

        $queryEmbedding = $embeddingsProvider->embedText($this->query);


        $vectorStore = new \App\Neuron\RAG\InvoicesVectorStore(Auth::id(), 10);
        $documents = $vectorStore->similaritySearch($queryEmbedding, 10);

        $this->searchResults = collect($documents)->map(function($document) {
            return [
                'id' => $document->metadata['invoice_id'] ?? null,
                'number' => $document->metadata['number'] ?? 'N/A',
                'amount' => $document->metadata['total_amount'] ?? 0,
                'currency' => $document->metadata['currency_code'] ?? 'EUR',
                'payment_method' => $document->metadata['payment_method'] ?? 'N/A',
                'date' => $document->metadata['date'] ?? null,
                'score' => round($document->getScore(), 4),
                'content' => $document->getContent()
            ];
        })->toArray();

    } catch (\Exception $e) {
        $this->error = 'Error al buscar documentos: ' . $e->getMessage();
    }
};


$setSearchMode = function($mode) {
    $this->searchMode = $mode;
    $this->response = '';
    $this->searchResults = [];
    $this->error = null;
};


$clear = function() {
    $this->query = '';
    $this->response = '';
    $this->searchResults = [];
    $this->error = null;
};

?>

<div class="max-w-6xl mx-auto p-6">
    <div class=" mb-6">
        {{--Breadcrumb start--}}
        <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />
    </div>
    <div class="bg-white rounded-lg shadow-lg">
        <div class="border-b border-gray-200 p-6">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Búsqueda Inteligente de Facturas
            </h1>
            <p class="text-gray-600 mt-2">
                Usa Neuron AI para buscar y analizar tus facturas usando lenguaje natural
            </p>
        </div>

        @if(!$this->hasEmbeddings)
            <div class="p-6">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="text-yellow-800 font-medium">Embeddings no generados</h3>
                            <p class="text-yellow-700 text-sm mt-1">
                                Necesitas generar embeddings para tus facturas antes de usar la búsqueda.
                                <a href="{{ route('management.tools.invoices.create') }}" class="text-yellow-600 hover:underline">Generar embeddings ahora</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="p-6">

                <div class="flex space-x-4 mb-6">
                    <button
                        wire:click="setSearchMode('chat')"
                        class="px-4 py-2 rounded-lg font-medium transition-colors {{ $searchMode === 'chat' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        💬 Chat Inteligente
                    </button>
                    <button
                        wire:click="setSearchMode('search')"
                        class="px-4 py-2 rounded-lg font-medium transition-colors {{ $searchMode === 'search' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        🔍 Búsqueda Directa
                    </button>
                </div>

                <div class="mb-6">
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <input
                                type="text"
                                wire:model="query"
                                wire:keydown.enter="{{ $searchMode === 'chat' ? 'searchWithChat' : 'searchDocuments' }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="{{ $searchMode === 'chat' ? 'Pregunta cualquier cosa sobre tus facturas...' : 'Busca facturas por contenido...' }}"
                                wire:loading.attr="disabled"
                            >
                        </div>
                        <button
                            wire:click="{{ $searchMode === 'chat' ? 'searchWithChat' : 'searchDocuments' }}"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            wire:loading.attr="disabled"
                        >

                            <svg wire:loading class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>

                            {{ $searchMode === 'chat' ? 'Preguntar' : 'Buscar' }}

                        </button>
                        @if(!empty($query) || !empty($response) || !empty($searchResults))
                            <button
                                wire:click="clear"
                                class="px-4 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                            >
                                Limpiar
                            </button>
                        @endif
                    </div>
                </div>

                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-2">Ejemplos de consultas:</p>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $examples = $searchMode === 'chat' ? [
                                "¿Cuáles fueron mis gastos más altos este mes?",
                                "Muéstrame las facturas de supermercados",
                                "¿Cuánto gasté en total en alimentación?",
                                "¿Qué facturas tengo pendientes de pago?"
                            ] : [
                                "supermercado alimentación",
                                "gasolina combustible",
                                "restaurante comida",
                                "farmacia medicamentos"
                            ];
                        @endphp
                        @foreach($examples as $example)
                            <button
                                wire:click="$set('query', '{{ $example }}')"
                                class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors"
                            >
                                {{ $example }}
                            </button>
                        @endforeach
                    </div>
                </div>

                @if($error)
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center text-red-700">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $error }}
                        </div>
                    </div>
                @endif

                @if($searchMode === 'chat' && !empty($response))
                    <div class="mb-6 p-6 bg-blue-50 border border-blue-200 rounded-lg">
                        <h3 class="text-lg font-medium text-blue-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                            </svg>
                            Respuesta del Asistente
                        </h3>
                        <div class="text-blue-800 whitespace-pre-wrap">{{ Str::of($response)->markdown()->toHtmlString() }}</div>
                    </div>
                @endif

                @if($searchMode === 'search' && !empty($searchResults))
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V4z" clip-rule="evenodd"></path>
                            </svg>
                            Facturas Encontradas ({{ count($searchResults) }})
                        </h3>

                        <div class="space-y-4">
                            @foreach($searchResults as $result)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h4 class="font-medium text-gray-900">Factura {{ $result['number'] }}</h4>
                                            <p class="text-sm text-gray-600">
                                                {{ number_format($result['amount'], 2) }} {{ $result['currency'] }} •
                                                {{ $result['payment_method'] }} •
                                                {{ $result['date'] ? \Carbon\Carbon::parse($result['date'])->format('d/m/Y') : 'Sin fecha' }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-sm bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                                Similitud: {{ $result['score'] }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded">
                                        {{ Str::limit($result['content'], 200) }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div wire:loading class="flex flex-col items-center justify-center py-8">
                    <svg class="animate-spin w-8 h-8 text-blue-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-gray-600">
                        {{ $searchMode === 'chat' ? 'Procesando tu consulta...' : 'Buscando facturas similares...' }}
                    </p>
                </div>

            </div>
        @endif
    </div>
</div>
