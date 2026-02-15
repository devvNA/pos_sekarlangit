<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('description');
            $table->dateTime('occurred_at');
            $table->string('reference')->nullable();
            $table->timestamps();

            $table->index(['type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_ledger_entries');
    }
};
