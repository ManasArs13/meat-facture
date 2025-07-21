<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Фильтры к запросу
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['name'] ?? null, fn($q, $name) => $q->where('name', 'like', "%{$name}%"));
    }

    /**
     * Сортировка к запросу
     */
    public function scopeSort(Builder $query, ?string $sortBy, ?string $sortDir): Builder
    {
        return $query->orderBy(
            in_array($sortBy, ['id', 'name', 'created_at']) ? $sortBy : 'id',
            $sortDir === 'desc' ? 'desc' : 'asc'
        );
    }

    function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
