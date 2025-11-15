<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assignment extends Model
{
    /** @use HasFactory<\Database\Factories\AssignmentFactory> */
    use HasFactory;

    protected $fillable = [
        'event_id',
        'giver_id',
        'receiver_id',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function giver(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'giver_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'receiver_id');
    }
}
