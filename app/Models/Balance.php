<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Balance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedAmountAttribute(): string 
    {
        return 'R$ ' . number_format($this->amount / 100, 2, ',', '.');
    }

    public function getBalanceProperty()
    {
        return Balance::where('user_id', $this->user->id)->value('amount') ?? 0;
    }


}
