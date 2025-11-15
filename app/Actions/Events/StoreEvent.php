<?php

namespace App\Actions\Events;

use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreEvent
{
    use AsAction;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_date' => ['nullable', 'date'],
            'event_time' => ['nullable', 'date_format:H:i'],
            'max_gift_amount' => ['nullable', 'numeric', 'min:0'],
            'theme' => ['required', 'in:default,winter,christmas,valentine'],
            'participants' => ['required', 'array', 'min:3'],
            'participants.*' => ['required', 'string', 'max:255'],
        ];
    }

    public function handle(array $data): Event
    {
        // Convert max_gift_amount to cents
        if (isset($data['max_gift_amount'])) {
            $data['max_gift_amount'] = (int) ($data['max_gift_amount'] * 100);
        }

        // Create event with unique event token
        $event = Event::create([
            ...$data,
            'event_token' => Str::random(32),
        ]);

        // Create participants
        foreach ($data['participants'] as $participantName) {
            Participant::create([
                'event_id' => $event->id,
                'name' => $participantName,
                'access_token' => Str::random(32),
            ]);
        }

        return $event;
    }

    public function create(): View
    {
        return view('events.create');
    }

    public function asController(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $event = $this->handle($validated);

        return redirect()->route('events.show', $event->event_token)
            ->with('success', 'Event created successfully!');
    }
}
