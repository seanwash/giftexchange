<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Participant extends Model
{
    /** @use HasFactory<\Database\Factories\ParticipantFactory> */
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'access_token',
        'has_entered_interests',
        'has_viewed_assignment',
    ];

    protected function casts(): array
    {
        return [
            'has_entered_interests' => 'boolean',
            'has_viewed_assignment' => 'boolean',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function interests(): HasMany
    {
        return $this->hasMany(ParticipantInterest::class);
    }

    public function assignment(): HasOne
    {
        return $this->hasOne(Assignment::class, 'giver_id');
    }

    public function exclusions(): HasMany
    {
        return $this->hasMany(Exclusion::class);
    }
}
