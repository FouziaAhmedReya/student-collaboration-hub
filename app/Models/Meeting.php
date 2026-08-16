<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meeting extends Model
{
    protected $fillable = [
        'chat_group_id',
        'title',
        'meeting_time',
        'google_calendar_event_id',
    ];

    protected function casts(): array
    {
        return [
            'meeting_time' => 'datetime',
        ];
    }

    public function chatGroup(): BelongsTo
    {
        return $this->belongsTo(ChatGroup::class);
    }
}
