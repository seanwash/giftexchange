<?php

namespace App\Actions\Participants;

use App\Actions\Drawings\DrawNames;
use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateParticipantSpin
{
    use AsAction;

    public function handle(string $accessToken): Participant
    {
        return Participant::where('access_token', $accessToken)
            ->with(['event.participants'])
            ->firstOrFail();
    }

    protected function checkAndCreateAssignments(Event $event): void
    {
        // Only create assignments if all participants have entered (or skipped) interests
        if ($event->drawing_completed_at) {
            return;
        }

        $allParticipantsReady = $event->participants->every(fn ($p) => $p->has_entered_interests);

        if (! $allParticipantsReady) {
            return;
        }

        // Create assignments using the drawing action
        DrawNames::run($event);
    }

    public function asController(string $accessToken): RedirectResponse
    {
        $participant = $this->handle($accessToken);

        // Check if assignments need to be created
        $this->checkAndCreateAssignments($participant->event);

        // Reload to get assignment
        $participant->load('assignment');

        if (! $participant->assignment) {
            return redirect()->route('participant.spin', $accessToken)
                ->with('error', 'Assignments are not ready yet. Please try again later.');
        }

        // Redirect to assignment page
        return redirect()->route('participant.assignment', $accessToken);
    }
}
