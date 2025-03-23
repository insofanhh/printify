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
        'uuid',
        'paper_type_id',
        'print_option_id',
        'price_per_page',
        'min_quantity',
        'max_quantity',
        'is_active',
    ];

    protected $casts = [
        'price_per_page' => 'decimal:2',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    /**
     * Get the paper type that owns the price rule.
     */
    public function paperType(): BelongsTo
    {
        return $this->belongsTo(PaperType::class);
    }

    /**
     * Get the print option that owns the price rule.
     */
    public function printOption(): BelongsTo
    {
        return $this->belongsTo(PrintOption::class);
    }

    /**
     * Scope a query to only include active price rules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Find the appropriate price rule for the given paper type, print option, and quantity.
     */
    public static function findPriceRule($paperTypeId, $printOptionId, $quantity)
    {
        return self::where('paper_type_id', $paperTypeId)
            ->where('print_option_id', $printOptionId)
            ->where('min_quantity', '<=', $quantity)
            ->where(function ($query) use ($quantity) {
                $query->where('max_quantity', '>=', $quantity)
                    ->orWhereNull('max_quantity');
            })
            ->where('is_active', true)
            ->orderBy('min_quantity', 'desc')
            ->first();
    }
}
