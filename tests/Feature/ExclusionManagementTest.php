<?php

use App\Models\Event;
use App\Models\Exclusion;
use App\Models\Participant;

use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('admin can create an exclusion', function () {
    $event = Event::factory()->create();
    $alice = Participant::factory()->for($event)->create(['name' => 'Alice']);
    $bob = Participant::factory()->for($event)->create(['name' => 'Bob']);

    $response = post(route('exclusions.store', $event->event_token), [
        'participant_id' => $alice->id,
        'excluded_participant_id' => $bob->id,
    ]);

    $response->assertRedirect(route('events.show', $event->event_token))
        ->assertSessionHas('success', 'Exclusion added successfully!');

    // Should create bidirectional exclusions
    expect(Exclusion::count())->toBe(2)
        ->and(Exclusion::where('participant_id', $alice->id)->where('excluded_participant_id', $bob->id)->exists())->toBeTrue()
        ->and(Exclusion::where('participant_id', $bob->id)->where('excluded_participant_id', $alice->id)->exists())->toBeTrue();
});

test('admin cannot create duplicate exclusions', function () {
    $event = Event::factory()->create();
    $alice = Participant::factory()->for($event)->create(['name' => 'Alice']);
    $bob = Participant::factory()->for($event)->create(['name' => 'Bob']);

    // Create bidirectional exclusions
    Exclusion::factory()->for($event)->create([
        'participant_id' => $alice->id,
        'excluded_participant_id' => $bob->id,
    ]);
    Exclusion::factory()->for($event)->create([
        'participant_id' => $bob->id,
        'excluded_participant_id' => $alice->id,
    ]);

    $response = post(route('exclusions.store', $event->event_token), [
        'participant_id' => $alice->id,
        'excluded_participant_id' => $bob->id,
    ]);

    $response->assertSessionHasErrors('excluded_participant_id');
    expect(Exclusion::count())->toBe(2);
});

test('admin cannot create exclusion with same participant', function () {
    $event = Event::factory()->create();
    $alice = Participant::factory()->for($event)->create(['name' => 'Alice']);

    $response = post(route('exclusions.store', $event->event_token), [
        'participant_id' => $alice->id,
        'excluded_participant_id' => $alice->id,
    ]);

    $response->assertSessionHasErrors('excluded_participant_id');
    expect(Exclusion::count())->toBe(0);
});

test('admin cannot create exclusion after drawing is complete', function () {
    $event = Event::factory()->create(['drawing_completed_at' => now()]);
    $alice = Participant::factory()->for($event)->create(['name' => 'Alice']);
    $bob = Participant::factory()->for($event)->create(['name' => 'Bob']);

    $response = post(route('exclusions.store', $event->event_token), [
        'participant_id' => $alice->id,
        'excluded_participant_id' => $bob->id,
    ]);

    $response->assertRedirect(route('events.show', $event->event_token))
        ->assertSessionHas('error', 'Cannot add exclusions after the drawing is complete.');

    expect(Exclusion::count())->toBe(0);
});

test('admin can delete an exclusion', function () {
    $event = Event::factory()->create();
    $alice = Participant::factory()->for($event)->create(['name' => 'Alice']);
    $bob = Participant::factory()->for($event)->create(['name' => 'Bob']);

    // Create bidirectional exclusions
    $exclusion = Exclusion::factory()->for($event)->create([
        'participant_id' => $alice->id,
        'excluded_participant_id' => $bob->id,
    ]);
    Exclusion::factory()->for($event)->create([
        'participant_id' => $bob->id,
        'excluded_participant_id' => $alice->id,
    ]);

    $response = delete(route('exclusions.destroy', [$event->event_token, $exclusion]));

    $response->assertRedirect(route('events.show', $event->event_token))
        ->assertSessionHas('success', 'Exclusion removed successfully!');

    // Should delete both directions
    expect(Exclusion::count())->toBe(0);
});

test('admin cannot delete exclusion after drawing is complete', function () {
    $event = Event::factory()->create(['drawing_completed_at' => now()]);
    $alice = Participant::factory()->for($event)->create(['name' => 'Alice']);
    $bob = Participant::factory()->for($event)->create(['name' => 'Bob']);

    // Create bidirectional exclusions
    $exclusion = Exclusion::factory()->for($event)->create([
        'participant_id' => $alice->id,
        'excluded_participant_id' => $bob->id,
    ]);
    Exclusion::factory()->for($event)->create([
        'participant_id' => $bob->id,
        'excluded_participant_id' => $alice->id,
    ]);

    $response = delete(route('exclusions.destroy', [$event->event_token, $exclusion]));

    $response->assertRedirect(route('events.show', $event->event_token))
        ->assertSessionHas('error', 'Cannot remove exclusions after the drawing is complete.');

    expect(Exclusion::count())->toBe(2);
});

test('admin dashboard shows exclusions', function () {
    $event = Event::factory()->create();
    $alice = Participant::factory()->for($event)->create(['name' => 'Alice']);
    $bob = Participant::factory()->for($event)->create(['name' => 'Bob']);

    // Create bidirectional exclusions
    Exclusion::factory()->for($event)->create([
        'participant_id' => $alice->id,
        'excluded_participant_id' => $bob->id,
    ]);
    Exclusion::factory()->for($event)->create([
        'participant_id' => $bob->id,
        'excluded_participant_id' => $alice->id,
    ]);

    $response = get(route('events.show', $event->event_token));

    $response->assertSuccessful()
        ->assertSee('Exclusions')
        ->assertSee('Alice')
        ->assertSee('Bob')
        ->assertSee('cannot be assigned to each other');
});

test('admin dashboard shows no exclusions message when empty', function () {
    $event = Event::factory()->create();

    $response = get(route('events.show', $event->event_token));

    $response->assertSuccessful()
        ->assertSee('No exclusions set')
        ->assertSee('All participants can be assigned to each other');
});

test('exclusion creation requires participant_id', function () {
    $event = Event::factory()->create();
    $bob = Participant::factory()->for($event)->create(['name' => 'Bob']);

    $response = post(route('exclusions.store', $event->event_token), [
        'excluded_participant_id' => $bob->id,
    ]);

    $response->assertSessionHasErrors('participant_id');
});

test('exclusion creation requires excluded_participant_id', function () {
    $event = Event::factory()->create();
    $alice = Participant::factory()->for($event)->create(['name' => 'Alice']);

    $response = post(route('exclusions.store', $event->event_token), [
        'participant_id' => $alice->id,
    ]);

    $response->assertSessionHasErrors('excluded_participant_id');
});

test('exclusion creation validates participant belongs to event', function () {
    $event = Event::factory()->create();
    $otherEvent = Event::factory()->create();
    $alice = Participant::factory()->for($event)->create(['name' => 'Alice']);
    $bob = Participant::factory()->for($otherEvent)->create(['name' => 'Bob']);

    $response = post(route('exclusions.store', $event->event_token), [
        'participant_id' => $alice->id,
        'excluded_participant_id' => $bob->id,
    ]);

    $response->assertNotFound();
});

test('creating exclusion in reverse direction also prevents duplicates', function () {
    $event = Event::factory()->create();
    $alice = Participant::factory()->for($event)->create(['name' => 'Alice']);
    $bob = Participant::factory()->for($event)->create(['name' => 'Bob']);

    // Create exclusion Alice -> Bob
    Exclusion::factory()->for($event)->create([
        'participant_id' => $alice->id,
        'excluded_participant_id' => $bob->id,
    ]);
    Exclusion::factory()->for($event)->create([
        'participant_id' => $bob->id,
        'excluded_participant_id' => $alice->id,
    ]);

    // Try to create Bob -> Alice (reverse)
    $response = post(route('exclusions.store', $event->event_token), [
        'participant_id' => $bob->id,
        'excluded_participant_id' => $alice->id,
    ]);

    $response->assertSessionHasErrors('excluded_participant_id');
    expect(Exclusion::count())->toBe(2);
});
