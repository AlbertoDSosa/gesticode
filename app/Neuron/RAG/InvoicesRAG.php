<?php

namespace App\Neuron\RAG;

use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\RAG\Embeddings\GeminiEmbeddingsProvider;
use NeuronAI\RAG\Embeddings\EmbeddingsProviderInterface;
use NeuronAI\Providers\Gemini\Gemini;
use NeuronAI\RAG\VectorStore\VectorStoreInterface;
use NeuronAI\RAG\RAG;
use Illuminate\Support\Facades\Auth;

/**
 * RAG implementation for Invoice search using Neuron AI
 */
class InvoicesRAG extends RAG
{
    protected int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Create a new instance for the authenticated user
     */
    public static function forUser(int $userId): self
    {
        return new self($userId);
    }

    /**
     * Create a new instance for the current authenticated user
     */
    public static function forAuthUser(): self
    {
        return new self(Auth::id());
    }

    /**
     * AI Provider configuration
     */
    protected function provider(): AIProviderInterface
    {
        return new Gemini(
            key: env('GEMINI_API_KEY'),
            model: env('GEMINI_MODEL', 'gemini-2.5-flash')
        );
    }

    /**
     * Embeddings Provider configuration
     */
    protected function embeddings(): EmbeddingsProviderInterface
    {
        return new GeminiEmbeddingsProvider(
            key: env('GEMINI_API_KEY'),
            model: env('GEMINI_EMBEDDINGS_MODEL')
        );
    }

    /**
     * Vector Store configuration
     */
    protected function vectorStore(): VectorStoreInterface
    {
        return new InvoicesVectorStore(
            userId: $this->userId,
            topK: 10
        );
    }

    /**
     * System instructions for the RAG agent
     */
    public function instructions(): string
    {
        return "Eres un asistente especializado en análisis de facturas. " .
               "Puedes ayudar a buscar, analizar y responder preguntas sobre facturas del usuario. " .
               "Usa la información de las facturas proporcionada para dar respuestas precisas y útiles. " .
               "Si no tienes información suficiente en las facturas, indícalo claramente.";
    }

    /**
     * Set a different user ID
     */
    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Get the current user ID
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
}
