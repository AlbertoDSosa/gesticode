<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Agregar columnas de embedding directamente a la tabla invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->text('searchable_content')->nullable()->comment('Contenido concatenado para búsqueda');
            $table->timestamp('embedding_generated_at')->nullable()->comment('Cuándo se generó el embedding');
            $table->string('embedding_model')->nullable()->comment('Modelo usado para generar el embedding');
            $table->json('metadata')->nullable()->comment('Metadatos de la factura');
            $table->vector('content_embedding', 768)->nullable()->comment('Embedding del contenido');
            // $table->vector('metadata_embedding', 384)->nullable()->comment('Embedding de los metadatos');
        });

        // Crear índices para búsquedas eficientes
        DB::statement('CREATE INDEX invoices_content_embedding_idx ON invoices USING ivfflat (content_embedding vector_l2_ops) WITH (lists = 100)');
        // DB::statement('CREATE INDEX invoices_metadata_embedding_idx ON invoices USING ivfflat (metadata_embedding vector_l2_ops) WITH (lists = 50)');

        // Índice compuesto para filtrar por usuario y buscar por similitud
        DB::statement('CREATE INDEX invoices_user_embedding_idx ON invoices (user_id) WHERE content_embedding IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['searchable_content', 'embedding_generated_at', 'embedding_model']);
        });

        // Eliminar columnas vector
        DB::statement('ALTER TABLE invoices DROP COLUMN IF EXISTS content_embedding');
        DB::statement('ALTER TABLE invoices DROP COLUMN IF EXISTS metadata_embedding');
    }
};
