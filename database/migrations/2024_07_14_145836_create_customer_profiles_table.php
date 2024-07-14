<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');
            $table->string('ref', 30)->unique()->nullable();
            $table->unsignedBigInteger('contact_person')->nullable();
            $table->foreign('contact_person')
                ->references('id')
                ->on('user_profiles')
                ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_profiles');
    }
};
