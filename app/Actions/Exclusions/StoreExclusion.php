<?php

namespace App\Actions\Exclusions;

use App\Models\Event;
use App\Models\Exclusion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreExclusion
{
    use AsAction;

    public function rules(): array
    {
        return [
            'participant_id' => ['required', 'exists:participants,id'],
            'excluded_participant_id' => ['required', 'exists:participants,id', 'different:participant_id'],
        ];
    }

    public function handle(Event $event, array $data): Exclusion
    {
        $participantId = $data['participant_id'];
        $excludedParticipantId = $data['excluded_participant_id'];

        // Ensure both participants belong to this event
        $participant = $event->participants()->findOrFail($participantId);
        $excludedParticipant = $event->participants()->findOrFail($excludedParticipantId);

        // Prevent self-exclusion
        if ($participantId === $excludedParticipantId) {
            throw ValidationException::withMessages([
                'excluded_participant_id' => 'A participant cannot exclude themselves.',
            ]);
        }

        // Check if exclusion already exists (either direction)
        $existingExclusion = $event->exclusions()
            ->where(function ($query) use ($participantId, $excludedParticipantId) {
                $query->where('participant_id', $participantId)
                    ->where('excluded_participant_id', $excludedParticipantId);
            })
            ->orWhere(function ($query) use ($participantId, $excludedParticipantId) {
                $query->where('participant_id', $excludedParticipantId)
                    ->where('excluded_participant_id', $participantId);
            })
            ->first();

        if ($existingExclusion) {
            throw ValidationException::withMessages([
                'excluded_participant_id' => 'This exclusion already exists.',
            ]);
        }

        // Create bidirectional exclusions automatically
        $exclusion = Exclusion::create([
            'event_id' => $event->id,
            'participant_id' => $participantId,
            'excluded_participant_id' => $excludedParticipantId,
        ]);

        // Create the reverse exclusion
        Exclusion::create([
            'event_id' => $event->id,
            'participant_id' => $excludedParticipantId,
            'excluded_participant_id' => $participantId,
        ]);

        return $exclusion;
    }

    public function asController(Request $request, string $eventToken): RedirectResponse
    {
        $event = Event::where('event_token', $eventToken)->firstOrFail();

        // Prevent adding exclusions after drawing is complete
        if ($event->drawing_completed_at) {
            return redirect()->route('events.show', $eventToken)
                ->with('error', 'Cannot add exclusions after the drawing is complete.');
        }

        $validated = $request->validate($this->rules());
        $this->handle($event, $validated);

        return redirect()->route('events.show', $eventToken)
            ->with('success', 'Exclusion added successfully!');
    }
}
