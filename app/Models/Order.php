<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;

    public const MAX_PRODUCTS_PER_ORDER = 20;

    protected $fillable = [
        'user_id',
        'comment',
        'total_amount',
        'is_completed',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'is_completed' => 'boolean',
    ];

    /**
     * Фильтры к запросу
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['total_amount_from'] ?? null, fn($q, $total_amount) => $q->where('total_amount', '>=', $total_amount))
            ->when($filters['total_amount_to'] ?? null, fn($q, $total_amount) => $q->where('total_amount', '<=', $total_amount))
            ->when($filters['is_completed'] ?? null, fn($q, $is_completed) => $q->where('is_completed', (bool)$is_completed));
    }

    /**
     * Сортировка к запросу
     */
    public function scopeSort(Builder $query, ?string $sortBy, ?string $sortDir): Builder
    {
        return $query->orderBy(
            in_array($sortBy, ['id', 'is_completed', 'total_amount', 'created_at']) ? $sortBy : 'id',
            $sortDir === 'desc' ? 'desc' : 'asc'
        );
    }

    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_amount, 2, '.', ' ') . ' ' . $this->currency;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity', 'total')
            ->withTimestamps();
    }
}
