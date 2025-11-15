<?php

namespace App\Actions\Events;

use App\Models\Event;
use App\Notifications\TestPushNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class SendTestPushNotification
{
    use AsAction;

    public function handle(Event $event): void
    {
        // Use TestPushNotification which doesn't implement ShouldQueue
        // so it sends immediately for testing purposes
        $event->notify(new TestPushNotification($event));
    }

    public function asController(Request $request, string $eventToken): JsonResponse
    {
        $event = Event::where('event_token', $eventToken)->firstOrFail();

        $this->handle($event);

        return response()->json(['success' => true, 'message' => 'Test notification sent']);
    }
}
