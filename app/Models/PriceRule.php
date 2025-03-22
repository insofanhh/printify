<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PriceRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'paper_type_id',
        'print_option_id',
        'base_price',
        'min_quantity',
        'max_quantity',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }

    public function paperType(): BelongsTo
    {
        return $this->belongsTo(PaperType::class);
    }

    public function printOption(): BelongsTo
    {
        return $this->belongsTo(PrintOption::class);
    }
}
