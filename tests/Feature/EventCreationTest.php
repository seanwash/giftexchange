<?php

use App\Models\Event;
use App\Models\Participant;

use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

test('create event page is accessible', function () {
    get(route('events.create'))
        ->assertSuccessful()
        ->assertSee('Create Gift Exchange Event');
});

test('event can be created with valid data', function () {
    $response = post(route('events.store'), [
        'name' => 'Holiday Gift Exchange 2025',
        'description' => 'Annual holiday gift exchange',
        'event_date' => '2025-12-25',
        'event_time' => '18:00',
        'max_gift_amount' => 50.00,
        'theme' => 'christmas',
        'participants' => ['Alice', 'Bob', 'Charlie'],
    ]);

    $response->assertRedirect();

    expect(Event::count())->toBe(1);

    $event = Event::first();
    expect($event->name)->toBe('Holiday Gift Exchange 2025')
        ->and($event->description)->toBe('Annual holiday gift exchange')
        ->and($event->max_gift_amount)->toBe(5000) // Stored in cents
        ->and($event->theme)->toBe('christmas')
        ->and($event->event_token)->toBeString()
        ->and($event->drawing_completed_at)->toBeNull();

    expect(Participant::count())->toBe(3);
});

test('event requires at least 3 participants', function () {
    post(route('events.store'), [
        'name' => 'Small Event',
        'theme' => 'default',
        'participants' => ['Alice', 'Bob'],
    ])->assertSessionHasErrors('participants');

    expect(Event::count())->toBe(0);
});

test('event requires a name', function () {
    post(route('events.store'), [
        'theme' => 'default',
        'participants' => ['Alice', 'Bob', 'Charlie'],
    ])->assertSessionHasErrors('name');
});

test('participants are created with unique access tokens', function () {
    post(route('events.store'), [
        'name' => 'Test Event',
        'theme' => 'default',
        'participants' => ['Alice', 'Bob', 'Charlie', 'Diana'],
    ]);

    $participants = Participant::all();
    $tokens = $participants->pluck('access_token');

    expect($tokens->unique()->count())->toBe(4)
        ->and($tokens->every(fn ($token) => strlen($token) === 32))->toBeTrue();
});

test('admin dashboard is accessible with valid token', function () {
    $event = Event::factory()->create();

    get(route('events.show', $event->event_token))
        ->assertSuccessful()
        ->assertSee($event->name);
});

test('admin dashboard shows 404 with invalid token', function () {
    get(route('events.show', 'invalid-token'))
        ->assertNotFound();
});

test('admin can update event name and description', function () {
    $event = Event::factory()->create([
        'name' => 'Original Name',
        'description' => 'Original Description',
    ]);

    $response = patch(route('events.update', $event->event_token), [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ]);

    $response->assertRedirect(route('events.show', $event->event_token))
        ->assertSessionHas('success', 'Event updated successfully!');

    $event->refresh();

    expect($event->name)->toBe('Updated Name')
        ->and($event->description)->toBe('Updated Description');
});

test('admin can update event name without description', function () {
    $event = Event::factory()->create([
        'name' => 'Original Name',
        'description' => 'Original Description',
    ]);

    patch(route('events.update', $event->event_token), [
        'name' => 'Updated Name',
        'description' => null,
    ]);

    $event->refresh();

    expect($event->name)->toBe('Updated Name')
        ->and($event->description)->toBeNull();
});

test('event update requires name', function () {
    $event = Event::factory()->create();

    patch(route('events.update', $event->event_token), [
        'description' => 'Some description',
    ])->assertSessionHasErrors('name');
});

test('event update validates name max length', function () {
    $event = Event::factory()->create();

    patch(route('events.update', $event->event_token), [
        'name' => str_repeat('a', 256),
        'description' => 'Some description',
    ])->assertSessionHasErrors('name');
});

test('admin can update event date and time', function () {
    $event = Event::factory()->create([
        'name' => 'Test Event',
        'event_date' => '2025-12-25',
        'event_time' => '18:00',
    ]);

    $response = patch(route('events.update', $event->event_token), [
        'name' => 'Test Event',
        'event_date' => '2025-12-31',
        'event_time' => '20:00',
    ]);

    $response->assertRedirect(route('events.show', $event->event_token))
        ->assertSessionHas('success', 'Event updated successfully!');

    $event->refresh();

    expect($event->event_date->format('Y-m-d'))->toBe('2025-12-31')
        ->and($event->event_time)->toBe('20:00');
});

test('admin can clear event date and time', function () {
    $event = Event::factory()->create([
        'name' => 'Test Event',
        'event_date' => '2025-12-25',
        'event_time' => '18:00',
    ]);

    patch(route('events.update', $event->event_token), [
        'name' => 'Test Event',
        'event_date' => null,
        'event_time' => null,
    ]);

    $event->refresh();

    expect($event->event_date)->toBeNull()
        ->and($event->event_time)->toBeNull();
});

test('event update returns 404 with invalid admin token', function () {
    patch(route('events.update', 'invalid-token'), [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ])->assertNotFound();
});
