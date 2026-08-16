<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupMessage extends Model
{
    protected $fillable = [
        'chat_group_id',
        'sender_name',
        'message',
    ];

    public function chatGroup(): BelongsTo
    {
        return $this->belongsTo(ChatGroup::class);
    }
}
