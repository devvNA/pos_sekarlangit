<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashLedgerEntry extends Model
{
    use HasFactory;

    protected $table = 'cash_ledger_entries';

    protected $fillable = [
        'type',
        'amount',
        'description',
        'occurred_at',
        'reference',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'occurred_at' => 'datetime',
    ];
}
