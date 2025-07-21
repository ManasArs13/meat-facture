<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'is_available',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    /**
     * Фильтры к запросу
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['price_from'] ?? null, fn($q, $price) => $q->where('price', '>=', $price))
            ->when($filters['price_to'] ?? null, fn($q, $price) => $q->where('price', '<=', $price))
            ->when($filters['is_available'] ?? null, fn($q, $inStock) => $q->where('is_available', (bool)$inStock))
            ->when($filters['name'] ?? null, fn($q, $name) => $q->where('name', 'like', "%{$name}%"));
    }

    /**
     * Сортировка к запросу
     */
    public function scopeSort(Builder $query, ?string $sortBy, ?string $sortDir): Builder
    {
        return $query->orderBy(
            in_array($sortBy, ['id', 'name', 'price', 'created_at']) ? $sortBy : 'id',
            $sortDir === 'desc' ? 'desc' : 'asc'
        );
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2, '.', ' ');
    }

    public function scopeActive($query)
    {
        return $query->where('is_available', 1);
    }

    function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)
            ->withPivot('quantity', 'total')
            ->withTimestamps();
    }
}
