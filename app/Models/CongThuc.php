<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CongThuc extends Model
{
    protected $table = 'cong_thuc';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['ma_mon', 'ma_nguyen_lieu', 'so_luong'];

    protected function casts(): array
    {
        return ['so_luong' => 'decimal:2'];
    }

    public function mon(): BelongsTo
    {
        return $this->belongsTo(Mon::class, 'ma_mon', 'ma_mon');
    }

    public function nguyenLieu(): BelongsTo
    {
        return $this->belongsTo(NguyenLieu::class, 'ma_nguyen_lieu', 'ma_nguyen_lieu');
    }
}
