<?php

namespace App\Actions\Participants;

use App\Models\Participant;
use App\Models\ParticipantInterest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreParticipantInterests
{
    use AsAction;

    public function rules(): array
    {
        return [
            'interests' => ['nullable', 'array', 'max:10'],
            'interests.*' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function handle(Participant $participant, array $data): Participant
    {
        // Delete existing interests
        $participant->interests()->delete();

        // Create new interests (filter out empty ones)
        if (isset($data['interests'])) {
            $filteredInterests = array_filter($data['interests'], fn ($interest) => ! empty(trim($interest)));

            foreach ($filteredInterests as $interest) {
                ParticipantInterest::create([
                    'participant_id' => $participant->id,
                    'interest_text' => trim($interest),
                ]);
            }
        }

        // Mark as entered
        $participant->update(['has_entered_interests' => true]);

        return $participant;
    }

    public function asController(Request $request, string $accessToken): RedirectResponse
    {
        $participant = Participant::where('access_token', $accessToken)->firstOrFail();

        $validated = $request->validate($this->rules());
        $this->handle($participant, $validated);

        return redirect()->route('participant.spin', $accessToken)
            ->with('success', 'Thanks for sharing your interests!');
    }
}
