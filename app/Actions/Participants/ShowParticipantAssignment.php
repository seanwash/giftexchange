<?php

namespace App\Actions\Participants;

use App\Models\Participant;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowParticipantAssignment
{
    use AsAction;

    public function handle(string $accessToken): Participant
    {
        return Participant::where('access_token', $accessToken)
            ->with(['event', 'assignment.receiver.interests'])
            ->firstOrFail();
    }

    public function asController(string $accessToken): View|RedirectResponse
    {
        $participant = $this->handle($accessToken);

        if (! $participant->assignment) {
            return redirect()->route('participant.spin', $accessToken);
        }

        // Check if this is the first time viewing (before updating)
        $isFirstView = ! $participant->has_viewed_assignment;

        // Mark as viewed
        $participant->update(['has_viewed_assignment' => true]);

        return view('participants.assignment', [
            'participant' => $participant,
            'receiver' => $participant->assignment->receiver,
            'showConfetti' => $isFirstView,
        ]);
    }
}
