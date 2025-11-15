<?php

namespace App\Actions\Events;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class UnsubscribeFromPushNotifications
{
    use AsAction;

    public function rules(): array
    {
        return [
            'endpoint' => ['required', 'string'],
        ];
    }

    public function handle(Event $event, string $endpoint): bool
    {
        $event->deletePushSubscription($endpoint);

        return true;
    }

    public function asController(Request $request, string $eventToken): JsonResponse
    {
        $event = Event::where('event_token', $eventToken)->firstOrFail();

        $validated = $request->validate($this->rules());

        $this->handle($event, $validated['endpoint']);

        return response()->json(['success' => true]);
    }
}
