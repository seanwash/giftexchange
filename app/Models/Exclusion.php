<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exclusion extends Model
{
    /** @use HasFactory<\Database\Factories\ExclusionFactory> */
    use HasFactory;

    protected $fillable = [
        'event_id',
        'participant_id',
        'excluded_participant_id',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function excludedParticipant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'excluded_participant_id');
    }
}
