<?php

namespace App\Actions\Events;

use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateEvent
{
    use AsAction;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_date' => ['nullable', 'date'],
            'event_time' => ['nullable', 'date_format:H:i'],
        ];
    }

    public function handle(Event $event, array $data): Event
    {
        $event->update($data);

        return $event;
    }

    public function show(string $eventToken): View
    {
        $event = Event::where('event_token', $eventToken)
            ->with(['participants.interests', 'participants.assignment', 'exclusions.participant', 'exclusions.excludedParticipant'])
            ->firstOrFail();

        $completedCount = $event->participants->filter(fn ($p) => $p->has_entered_interests)->count();
        $viewedCount = $event->participants->filter(fn ($p) => $p->has_viewed_assignment)->count();

        return view('events.admin', [
            'event' => $event,
            'completedCount' => $completedCount,
            'viewedCount' => $viewedCount,
        ]);
    }

    public function asController(Request $request, string $eventToken): RedirectResponse
    {
        $event = Event::where('event_token', $eventToken)->firstOrFail();

        $validated = $request->validate($this->rules());
        $this->handle($event, $validated);

        return redirect()->route('events.show', $eventToken)
            ->with('success', 'Event updated successfully!');
    }
}
