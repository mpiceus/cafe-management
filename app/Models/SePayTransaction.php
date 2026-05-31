<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SePayTransaction extends Model
{
    protected $table = 'sepay_transactions';

    protected $fillable = [
        'ma_hoa_don',
        'sepay_id',
        'gateway',
        'transaction_date',
        'account_number',
        'sub_account',
        'transfer_type',
        'amount_in',
        'amount_out',
        'accumulated',
        'code',
        'transaction_content',
        'reference_number',
        'description',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'datetime',
            'amount_in' => 'decimal:2',
            'amount_out' => 'decimal:2',
            'accumulated' => 'decimal:2',
            'payload' => 'array',
        ];
    }

    public function hoaDon(): BelongsTo
    {
        return $this->belongsTo(HoaDon::class, 'ma_hoa_don', 'ma_hoa_don');
    }
}
