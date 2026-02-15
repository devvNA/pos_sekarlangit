<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Receivable extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'sale_id',
        'customer_name',
        'total',
        'remaining',
        'status',
        'due_date',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'remaining' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ReceivablePayment::class);
    }
}
