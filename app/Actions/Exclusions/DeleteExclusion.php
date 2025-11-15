<?php

namespace App\Actions\Exclusions;

use App\Models\Event;
use App\Models\Exclusion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteExclusion
{
    use AsAction;

    public function handle(Event $event, Exclusion $exclusion): bool
    {
        // Ensure the exclusion belongs to this event
        if ($exclusion->event_id !== $event->id) {
            abort(403, 'This exclusion does not belong to this event.');
        }

        // Delete both directions of the exclusion
        $event->exclusions()
            ->where(function ($query) use ($exclusion) {
                $query->where('participant_id', $exclusion->participant_id)
                    ->where('excluded_participant_id', $exclusion->excluded_participant_id);
            })
            ->orWhere(function ($query) use ($exclusion) {
                $query->where('participant_id', $exclusion->excluded_participant_id)
                    ->where('excluded_participant_id', $exclusion->participant_id);
            })
            ->delete();

        return true;
    }

    public function asController(Request $request, string $eventToken, Exclusion $exclusion): RedirectResponse
    {
        $event = Event::where('event_token', $eventToken)->firstOrFail();

        // Prevent removing exclusions after drawing is complete
        if ($event->drawing_completed_at) {
            return redirect()->route('events.show', $eventToken)
                ->with('error', 'Cannot remove exclusions after the drawing is complete.');
        }

        $this->handle($event, $exclusion);

        return redirect()->route('events.show', $eventToken)
            ->with('success', 'Exclusion removed successfully!');
    }
}
