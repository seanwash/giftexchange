<?php

use App\Models\Event;
use App\Models\Participant;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('participant can access entry page with valid token', function () {
    $event = Event::factory()->create();
    $participant = Participant::factory()->for($event)->create();

    get(route('participant.enter', $participant->access_token))
        ->assertSuccessful()
        ->assertSee($event->name)
        ->assertSee($participant->name);
});

test('participant entry page shows 404 with invalid token', function () {
    get(route('participant.enter', 'invalid-token'))
        ->assertNotFound();
});

test('participant can submit interests', function () {
    $event = Event::factory()->create();
    $participant = Participant::factory()->for($event)->create();

    post(route('participant.storeInterests', $participant->access_token), [
        'interests' => ['Coffee', 'Books', 'Chocolate'],
    ])->assertRedirect(route('participant.spin', $participant->access_token));

    $participant->refresh();

    expect($participant->has_entered_interests)->toBeTrue()
        ->and($participant->interests()->count())->toBe(3)
        ->and($participant->interests->pluck('interest_text')->toArray())
        ->toContain('Coffee', 'Books', 'Chocolate');
});

test('participant can skip entering interests', function () {
    $event = Event::factory()->create();
    $participant = Participant::factory()->for($event)->create();

    post(route('participant.storeInterests', $participant->access_token), [])
        ->assertRedirect(route('participant.spin', $participant->access_token));

    $participant->refresh();

    expect($participant->has_entered_interests)->toBeTrue()
        ->and($participant->interests()->count())->toBe(0);
});

test('empty interests are filtered out', function () {
    $event = Event::factory()->create();
    $participant = Participant::factory()->for($event)->create();

    post(route('participant.storeInterests', $participant->access_token), [
        'interests' => ['Coffee', '', '  ', 'Books'],
    ]);

    expect($participant->interests()->count())->toBe(2);
});

test('participant who entered interests is redirected to spin page', function () {
    $event = Event::factory()->create();
    $participant = Participant::factory()->for($event)->create([
        'has_entered_interests' => true,
    ]);

    get(route('participant.enter', $participant->access_token))
        ->assertRedirect(route('participant.spin', $participant->access_token));
});

test('participant can view assignment page after assignment is created', function () {
    $event = Event::factory()->create(['drawing_completed_at' => now()]);
    $giver = Participant::factory()->for($event)->create();
    $receiver = Participant::factory()->for($event)->create();

    $giver->assignment()->create([
        'event_id' => $event->id,
        'receiver_id' => $receiver->id,
    ]);

    get(route('participant.assignment', $giver->access_token))
        ->assertSuccessful()
        ->assertSee($receiver->name);
});
