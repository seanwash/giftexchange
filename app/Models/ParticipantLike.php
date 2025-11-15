<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParticipantInterest extends Model
{
    /** @use HasFactory<\Database\Factories\ParticipantInterestFactory> */
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'interest_text',
    ];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }
}
