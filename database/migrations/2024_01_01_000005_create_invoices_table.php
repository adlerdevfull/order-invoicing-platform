<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained();
            $table->string('number', 50)->unique();
            $table->string('identification_key', 64)->unique();
            $table->string('tax_type', 20);
            $table->integer('net_amount');
            $table->integer('tax_amount');
            $table->integer('total_amount');
            $table->string('digital_signature', 64)->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();

            $table->index('number');
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
