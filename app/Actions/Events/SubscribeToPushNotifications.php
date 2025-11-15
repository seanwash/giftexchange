<?php

namespace App\Actions\Events;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;
use NotificationChannels\WebPush\PushSubscription;

class SubscribeToPushNotifications
{
    use AsAction;

    public function rules(): array
    {
        return [
            'endpoint' => ['required', 'string'],
            'keys' => ['required', 'array'],
            'keys.p256dh' => ['required', 'string'],
            'keys.auth' => ['required', 'string'],
            'contentEncoding' => ['nullable', 'string'],
        ];
    }

    public function handle(Event $event, array $subscriptionData): PushSubscription
    {
        return $event->updatePushSubscription(
            $subscriptionData['endpoint'],
            $subscriptionData['keys']['p256dh'],
            $subscriptionData['keys']['auth'],
            $subscriptionData['contentEncoding'] ?? 'aesgcm'
        );
    }

    public function asController(Request $request, string $eventToken): JsonResponse
    {
        $event = Event::where('event_token', $eventToken)->firstOrFail();

        $validated = $request->validate($this->rules());

        $this->handle($event, $validated);

        return response()->json(['success' => true]);
    }
}
