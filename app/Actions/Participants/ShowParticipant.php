<?php

namespace App\Actions\Participants;

use App\Models\Participant;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowParticipant
{
    use AsAction;

    public function handle(string $accessToken): Participant
    {
        return Participant::where('access_token', $accessToken)
            ->with(['event', 'interests'])
            ->firstOrFail();
    }

    public function asController(string $accessToken): View|RedirectResponse
    {
        $participant = $this->handle($accessToken);

        // If they've already entered interests, redirect to spin
        if ($participant->has_entered_interests) {
            return redirect()->route('participant.spin', $accessToken);
        }

        return view('participants.enter', [
            'participant' => $participant,
        ]);
    }
}
