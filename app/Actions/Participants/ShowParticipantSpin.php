<?php

namespace App\Actions\Participants;

use App\Actions\Drawings\DrawNames;
use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowParticipantSpin
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

    public function asController(string $accessToken): View|RedirectResponse
    {
        $participant = $this->handle($accessToken);

        // Check if assignments need to be created
        $this->checkAndCreateAssignments($participant->event);

        // Reload to check if assignment exists
        $participant->load('assignment');

        if (! $participant->assignment) {
            // If participant has entered interests, show waiting message instead of redirecting
            // (to avoid redirect loop with enter method)
            if ($participant->has_entered_interests) {
                return view('participants.spin', [
                    'participant' => $participant,
                    'waiting' => true,
                ]);
            }

            // Only redirect to enter if they haven't entered interests yet
            return redirect()->route('participant.enter', $accessToken)
                ->with('error', 'Assignments are not ready yet. Please try again later.');
        }

        return view('participants.spin', [
            'participant' => $participant,
        ]);
    }
}
