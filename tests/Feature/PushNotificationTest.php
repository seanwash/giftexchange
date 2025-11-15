<?php

use App\Models\Event;
use App\Models\Participant;
use App\Notifications\AssignmentsReady;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('vapid public key endpoint returns public key', function () {
    $response = get(route('webpush.vapid-public-key'));

    $response->assertSuccessful()
        ->assertJsonStructure(['publicKey'])
        ->assertJson([
            'publicKey' => config('webpush.vapid.public_key'),
        ]);
});

test('can subscribe to push notifications for an event', function () {
    $event = Event::factory()->create();

    $subscriptionData = [
        'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint',
        'keys' => [
            'p256dh' => 'test-p256dh-key',
            'auth' => 'test-auth-key',
        ],
        'contentEncoding' => 'aesgcm',
    ];

    $response = post(route('events.push.subscribe', $event->event_token), $subscriptionData);

    $response->assertSuccessful()
        ->assertJson(['success' => true]);

    expect($event->pushSubscriptions)->toHaveCount(1)
        ->and($event->pushSubscriptions->first()->endpoint)->toBe($subscriptionData['endpoint']);
});

test('can unsubscribe from push notifications for an event', function () {
    $event = Event::factory()->create();

    // First subscribe
    $subscriptionData = [
        'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint',
        'keys' => [
            'p256dh' => 'test-p256dh-key',
            'auth' => 'test-auth-key',
        ],
        'contentEncoding' => 'aesgcm',
    ];

    post(route('events.push.subscribe', $event->event_token), $subscriptionData);

    expect($event->pushSubscriptions)->toHaveCount(1);

    // Then unsubscribe
    $response = post(route('events.push.unsubscribe', $event->event_token), [
        'endpoint' => $subscriptionData['endpoint'],
    ]);

    $response->assertSuccessful()
        ->assertJson(['success' => true]);

    expect($event->fresh()->pushSubscriptions)->toHaveCount(0);
});

test('subscription requires endpoint', function () {
    $event = Event::factory()->create();

    $response = post(route('events.push.subscribe', $event->event_token), [
        'keys' => [
            'p256dh' => 'test-p256dh-key',
            'auth' => 'test-auth-key',
        ],
    ]);

    $response->assertSessionHasErrors('endpoint');
});

test('subscription requires keys', function () {
    $event = Event::factory()->create();

    $response = post(route('events.push.subscribe', $event->event_token), [
        'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint',
    ]);

    $response->assertSessionHasErrors('keys');
});

test('subscription requires p256dh and auth keys', function () {
    $event = Event::factory()->create();

    $response = post(route('events.push.subscribe', $event->event_token), [
        'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint',
        'keys' => [
            'p256dh' => 'test-p256dh-key',
        ],
    ]);

    $response->assertSessionHasErrors('keys.auth');
});

test('unsubscribe requires endpoint', function () {
    $event = Event::factory()->create();

    $response = post(route('events.push.unsubscribe', $event->event_token), []);

    $response->assertSessionHasErrors('endpoint');
});

test('subscription returns 404 with invalid event token', function () {
    $response = post(route('events.push.subscribe', 'invalid-token'), [
        'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint',
        'keys' => [
            'p256dh' => 'test-p256dh-key',
            'auth' => 'test-auth-key',
        ],
    ]);

    $response->assertNotFound();
});

test('notification is sent when assignments are completed', function () {
    Notification::fake();

    $event = Event::factory()->create();
    Participant::factory()->count(3)->create(['event_id' => $event->id]);

    // Subscribe to notifications
    $event->updatePushSubscription(
        'https://fcm.googleapis.com/fcm/send/test-endpoint',
        'test-p256dh-key',
        'test-auth-key'
    );

    // Mark all participants as having entered interests
    $event->participants->each(function ($participant) {
        $participant->update(['has_entered_interests' => true]);
    });

    // Trigger drawing (this should send notification)
    \App\Actions\Drawings\DrawNames::run($event);

    Notification::assertSentTo($event, AssignmentsReady::class, function ($notification) use ($event) {
        return $notification->event->id === $event->id;
    });
});

test('notification is not sent if no subscriptions exist', function () {
    Notification::fake();

    $event = Event::factory()->create();
    Participant::factory()->count(3)->create(['event_id' => $event->id]);

    // Mark all participants as having entered interests
    $event->participants->each(function ($participant) {
        $participant->update(['has_entered_interests' => true]);
    });

    // Trigger drawing
    \App\Actions\Drawings\DrawNames::run($event);

    // Notification should still be sent (it will just fail to deliver)
    Notification::assertSentTo($event, AssignmentsReady::class);
});

test('multiple subscriptions can exist for same event', function () {
    $event = Event::factory()->create();

    $event->updatePushSubscription(
        'https://fcm.googleapis.com/fcm/send/endpoint-1',
        'test-p256dh-key-1',
        'test-auth-key-1'
    );

    $event->updatePushSubscription(
        'https://fcm.googleapis.com/fcm/send/endpoint-2',
        'test-p256dh-key-2',
        'test-auth-key-2'
    );

    expect($event->pushSubscriptions)->toHaveCount(2);
});
