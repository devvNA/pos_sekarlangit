<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('receipt_no')->unique();
            $table->dateTime('sold_at');
            $table->string('payment_method');
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('paid', 12, 2)->default(0);
            $table->decimal('change', 12, 2)->default(0);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['sold_at', 'payment_method']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
