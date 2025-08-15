<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\Users\User;
use Illuminate\Support\Facades\DB;
use App\Concerns\HasVectorColumns;


class Invoice extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasVectorColumns;

    protected $fillable = [
        'user_id',
        'number',
        'total_amount',
        'currency_code',
        'payment_method',
        'status',
        'time',
        'date',
        'llm_name',
        'llm_text_response',
        'seller_info',
        'items',
        'metadata',
        'content_description',
        'content_embedding',
        'searchable_content',
        'embedding_generated_at',
        'embedding_model'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('invoices')
            ->singleFile()
            ->useDisk('invoices')
            ->acceptsMimeTypes(['image/jpeg', 'image/jpg']);

    }

    /**
     * Get the user that owns the invoice.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'seller_info' => 'array',
            'items' => 'array',
            'embedding_generated_at' => 'datetime',
            'content_embedding' => 'array',
            'metadata_embedding' => 'array',
            'metadata' => 'array',
        ];
    }

    /**
     * Buscar facturas similares por contenido
     */
    public function findSimilarInvoices(array $queryVector, int $limit = 5)
    {
        $vectorString = $this->arrayToVector($queryVector);

        return self::where('user_id', $this->user_id) // Solo facturas del mismo usuario
            ->whereNotNull('content_embedding')
            ->select('*')
            ->selectRaw('content_embedding <-> ? as distance', [$vectorString])
            ->orderBy('distance')
            ->limit($limit)
            ->get();
    }

    /**
     * Buscar facturas por similitud semántica
     */
    public static function searchBySemantic(int $userId, array $queryVector, float $threshold = 0.8, int $limit = 10)
    {
        $vectorString = (new self)->arrayToVector($queryVector);

        // Usar consulta SQL directa para evitar problemas de GROUP BY
        $results = DB::select("
            SELECT *, 1 - (content_embedding <=> ?) as similarity
            FROM invoices
            WHERE user_id = ?
              AND content_embedding IS NOT NULL
              AND 1 - (content_embedding <=> ?) > ?
            ORDER BY similarity DESC
            LIMIT ?
        ", [$vectorString, $userId, $vectorString, $threshold, $limit]);

        // Convertir resultados a modelos
        return collect($results)->map(function($row) {
            $invoice = new self();
            foreach ((array)$row as $key => $value) {
                if ($key !== 'similarity') {
                    $invoice->setAttribute($key, $value);
                }
            }
            $invoice->similarity = $row->similarity;
            $invoice->exists = true;
            return $invoice;
        });
    }

    /**
     * Buscar facturas por metadatos similares (vendedor, tipo de producto, etc.)
     */
    // public function findSimilarByMetadata(array $queryVector, int $limit = 5)
    // {
    //     $vectorString = $this->arrayToVector($queryVector);

    //     return self::where('user_id', $this->user_id)
    //         ->whereNotNull('metadata_embedding')
    //         ->select('*')
    //         ->selectRaw('metadata_embedding <-> ? as distance', [$vectorString])
    //         ->orderBy('distance')
    //         ->limit($limit)
    //         ->get();
    // }


    /**
     * Generar contenido de factura para embedding
     */
    public function generateInvoiceContent(): string
    {
        $content = [];

        $content[] = "Número de factura: " . $this->number;
        $content[] = "Importe: " . $this->total_amount . " " . $this->currency_code;
        $content[] = "Método de pago: " . $this->payment_method;
        $content[] = "Fecha de compra: " . $this->date;
        $content[] = "Hora de compra: " . $this->time;

        if ($this->seller_info && is_array($this->seller_info)) {
            $sellerInfo = [];
            foreach ($this->seller_info as $key => $value) {
                if (is_string($value) || is_numeric($value)) {
                    $sellerInfo[] = "{$key}: {$value}";
                }
            }

            if (!empty($sellerInfo)) {
                $content[] = "Vendedor: " . implode(', ', $sellerInfo);
            }
        }

        if ($this->content_description) {
            $content[] = "Descripción del contenido: {$this->content_description}";
        }

        if ($this->items && is_array($this->items)) {
            $itemsText = [];
            foreach ($this->items as $item) {
                if (is_array($item)) {
                    $itemText = [];
                    foreach ($item as $key => $value) {
                        if (is_string($value) || is_numeric($value)) {
                            $itemText[] = "{$key}: {$value}";
                        }
                    }
                    if (!empty($itemText)) {
                        $itemsText[] = implode(', ', $itemText);
                    }
                }
            }

            if (!empty($itemsText)) {
                $content[] = "Productos: " . implode('; ', $itemsText);
            }
        }

        return implode('. ', $content);
    }

    /**
     * Obtener facturas relacionadas (por contenido y metadatos)
     */
    public function getRelatedInvoices(int $limit = 5)
    {
        $related = collect();

        // Buscar por contenido similar
        if ($this->content_embedding) {
            $contentSimilar = $this->findSimilarInvoices($this->content_embedding, $limit);
            $related = $related->merge($contentSimilar);
        }

        // Buscar por metadatos similares
        // if ($this->metadata_embedding) {
        //     $metadataSimilar = $this->findSimilarByMetadata($this->metadata_embedding, $limit);
        //     $related = $related->merge($metadataSimilar);
        // }

        // Eliminar duplicados y la factura actual
        return $related->unique('id')
            ->reject(fn($invoice) => $invoice->id === $this->id)
            ->take($limit);
    }

    /**
     * Scope para facturas con embeddings
     */
    public function scopeWithEmbeddings($query)
    {
        return $query->whereNotNull('content_embedding');
    }

    /**
     * Scope para facturas recientes con embeddings
     */
    public function scopeRecentWithEmbeddings($query, int $days = 30)
    {
        return $query->withEmbeddings()
            ->where('created_at', '>=', now()->subDays($days));
    }

}
