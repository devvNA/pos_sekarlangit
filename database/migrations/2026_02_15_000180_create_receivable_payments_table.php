<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receivable_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receivable_id')->constrained('receivables')->cascadeOnDelete();
            $table->dateTime('paid_at');
            $table->decimal('amount', 12, 2)->default(0);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['receivable_id', 'paid_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receivable_payments');
    }
};
