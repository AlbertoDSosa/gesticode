<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Query\Expression;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique()->default(new Expression('(UUID())'));
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('number')->nullable()->comment('Número de la factura');
            $table->double('total_amount')->comment('Monto total de la compra')->default(0.00);
            $table->dateTime('purchase_datetime')->comment('Fecha y hora de compra')->nullable();
            $table->string('llm_name')->comment('Nombre del modelo de lenguaje')->nullable();
            $table->string('currency_code', 3)->comment('Código de moneda')->default('EUR');
            $table->enum('payment_method', ['Desconocido', 'Targeta Bancaria', 'Efectivo'])->comment('Método de pago')->default('Desconocido')->nullable();
            $table->enum('status', ['Pendiente de revisión', 'Revisada y aprobada', 'Revisada y Editada'])->comment('Estado de la factura')->default('Pendiente de revisión');
            $table->text('invoice_edit_reason')->comment('Razón de la edición de la factura')->nullable();
            $table->text('llm_text_response')->comment('Respuesta de texto del modelo de lenguaje')->nullable();
            $table->json('seller_info')->comment('Información del vendedor')->nullable();
            $table->json('items')->comment('Lista de artículos comprados')->nullable();
            // $table->vector('embedding', dimensions: 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
