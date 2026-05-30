<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SePayRefund extends Model
{
    protected $table = 'sepay_refunds';

    protected $fillable = [
        'ma_hoa_don',
        'sepay_transaction_id',
        'amount',
        'status',
        'reason',
        'response',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'response' => 'array',
        ];
    }

    public function hoaDon(): BelongsTo
    {
        return $this->belongsTo(HoaDon::class, 'ma_hoa_don', 'ma_hoa_don');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(SePayTransaction::class, 'sepay_transaction_id');
    }
}
