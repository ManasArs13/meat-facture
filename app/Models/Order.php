<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'description',
        'total_amount',
        'is_completed',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'is_completed' => 'boolean',
    ];

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
