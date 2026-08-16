<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookOrder extends Model
{
    protected $fillable = [
        'book_id',
        'buyer_id',
        'buyer_token',
        'buyer_name',
        'buyer_email',
        'buyer_phone',
        'message',
        'status',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Awaiting Seller',
            'accepted' => 'Purchase Accepted',
            'rejected' => 'Declined by Seller',
            'cancelled' => 'Cancelled by Buyer',
            default => ucfirst($this->status),
        };
    }
}