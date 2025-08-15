<?php

namespace App\Neuron\RAG;

use App\Models\Invoice;
use NeuronAI\RAG\Document;
use NeuronAI\RAG\VectorStore\VectorStoreInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Concerns\HasVectorColumns;

/**
 * Custom Vector Store implementation for Invoices using PostgreSQL with pgvector
 * Compatible with Neuron AI framework
 */
class InvoicesVectorStore implements VectorStoreInterface
{
    use HasVectorColumns;

    protected int $userId;
    protected int $topK;

    public function __construct(int $userId, int $topK = 5)
    {
        $this->userId = $userId;
        $this->topK = $topK;
    }

    /**
     * Add a single document to the vector store
     */
    public function addDocument(Document $document): VectorStoreInterface
    {
        $this->addDocuments([$document]);
        return $this;
    }

    /**
     * Add multiple documents to the vector store
     * @param Document[] $documents
     */
    public function addDocuments(array $documents): VectorStoreInterface
    {
        foreach ($documents as $document) {
            $this->storeInvoiceDocument($document);
        }
        return $this;
    }

    /**
     * Delete documents by source
     */
    public function deleteBySource(string $sourceType, string $sourceName): VectorStoreInterface
    {
        // For invoices, sourceType could be 'invoice' and sourceName could be invoice ID
        if ($sourceType === 'invoice') {
            $invoice = Invoice::where('user_id', $this->userId)
                ->where('id', $sourceName)
                ->first();

            if ($invoice) {
                $invoice->update([
                    'content_embedding' => null,
                    'metadata_embedding' => null,
                    'searchable_content' => null,
                    'embedding_generated_at' => null,
                    'embedding_model' => null
                ]);

                Log::info('Deleted invoice embeddings', [
                    'invoice_id' => $sourceName,
                    'user_id' => $this->userId
                ]);
            }
        }
        return $this;
    }

    /**
     * Perform similarity search
     * @param float[] $embedding
     * @return iterable<Document>
     */
    public function similaritySearch(array $embedding): iterable
    {
        $k = $this->topK;

        // Use the existing searchBySemantic method from the Invoice model
        $results = Invoice::searchBySemantic($this->userId, $embedding, 0.5, $k);

        return $results->map(function ($invoice) {
            return $this->invoiceToDocument($invoice);
        });
    }

    /**
     * Store a Document as an Invoice embedding
     */
    protected function storeInvoiceDocument(Document $document): void
    {
        // Extract invoice ID from document metadata
        $invoiceId = $document->metadata['invoice_id'] ?? null;

        if (!$invoiceId) {
            Log::warning('Document missing invoice_id in metadata', [
                'source_name' => $document->getSourceName(),
                'source_type' => $document->getSourceType()
            ]);
            return;
        }

        $invoice = Invoice::where('user_id', $this->userId)
            ->where('id', $invoiceId)
            ->first();

        if (!$invoice) {
            Log::warning('Invoice not found for document', [
                'invoice_id' => $invoiceId,
                'user_id' => $this->userId
            ]);
            return;
        }

        // Update the invoice with the embedding
        $invoice->update([
            'content_embedding' => $document->getEmbedding(),
            'searchable_content' => $document->getContent(),
            'embedding_generated_at' => now(),
            'embedding_model' => $document->metadata['model'] ?? 'text-embedding-004',
            'metadata' => $document->metadata
        ]);

        Log::info('Stored invoice embedding', [
            'invoice_id' => $invoiceId,
            'user_id' => $this->userId,
            'embedding_dimensions' => count($document->getEmbedding())
        ]);
    }

    /**
     * Convert an Invoice model to a Neuron AI Document
     */
    protected function invoiceToDocument(Invoice $invoice): Document
    {
        $document = new Document(
            content: $invoice->searchable_content ?: $invoice->generateInvoiceContent()
        );

        // Set the embedding
        if ($invoice->content_embedding) {
            $document->embedding = (array) $invoice->content_embedding;
        }

        // Set source information
        $document->sourceType = 'invoice';
        $document->sourceName = "invoice_{$invoice->id}";

        // Set similarity score if available
        if (isset($invoice->similarity)) {
            $document->setScore($invoice->similarity);
        }

        // Add metadata
        $document->metadata = [
            'invoice_id' => $invoice->id,
            'user_id' => $invoice->user_id,
            'number' => $invoice->number,
            'total_amount' => $invoice->total_amount,
            'currency_code' => $invoice->currency_code,
            'payment_method' => $invoice->payment_method,
            'date' => $invoice->date ? (is_string($invoice->date) ? $invoice->date : $invoice->date->toISOString()) : null,
        ];

        return $document;
    }

    /**
     * Set the user ID for filtering
     */
    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Get all invoices for this user that have embeddings
     */
    public function getAllDocuments(): Collection
    {
        return Invoice::where('user_id', $this->userId)
            ->whereNotNull('content_embedding')
            ->get()
            ->map(fn($invoice) => $this->invoiceToDocument($invoice));
    }

    /**
     * Get count of documents in the vector store
     */
    public function getDocumentCount(): int
    {
        return Invoice::where('user_id', $this->userId)
            ->whereNotNull('content_embedding')
            ->count();
    }
}
