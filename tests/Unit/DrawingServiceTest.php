<?php

use App\Actions\Drawings\DrawNames;
use App\Models\Event;
use App\Models\Participant;

test('drawing service creates assignments for all participants', function () {
    $event = Event::factory()->create();
    $participants = Participant::factory()
        ->for($event)
        ->count(5)
        ->create();

    DrawNames::run($event);

    $event->refresh();

    expect($event->drawing_completed_at)->not->toBeNull()
        ->and($event->assignments()->count())->toBe(5);

    // Each participant should be a giver exactly once
    foreach ($participants as $participant) {
        expect($participant->fresh()->assignment)->not->toBeNull();
    }
});

test('no participant is assigned to themselves', function () {
    $event = Event::factory()->create();
    Participant::factory()->for($event)->count(5)->create();

    DrawNames::run($event);

    $assignments = $event->assignments;

    foreach ($assignments as $assignment) {
        expect($assignment->giver_id)->not->toBe($assignment->receiver_id);
    }
});

test('drawing service forms a complete circle', function () {
    $event = Event::factory()->create();
    Participant::factory()->for($event)->count(4)->create();

    DrawNames::run($event);

    $assignments = $event->assignments;

    // Follow the chain and verify it forms a complete circle
    $visited = [];
    $current = $assignments->first();
    $visited[] = $current->giver_id;

    for ($i = 0; $i < $assignments->count(); $i++) {
        $next = $assignments->firstWhere('giver_id', $current->receiver_id);
        expect($next)->not->toBeNull();

        if (! in_array($next->giver_id, $visited)) {
            $visited[] = $next->giver_id;
        }

        $current = $next;
    }

    expect(count($visited))->toBe($assignments->count());
});

test('drawing service throws exception with less than 2 participants', function () {
    $event = Event::factory()->create();
    Participant::factory()->for($event)->create();

    expect(fn () => DrawNames::run($event))
        ->toThrow(RuntimeException::class, 'Need at least 2 participants');
});

test('drawing service respects exclusions', function () {
    $event = Event::factory()->create();
    $alice = Participant::factory()->for($event)->create(['name' => 'Alice']);
    $bob = Participant::factory()->for($event)->create(['name' => 'Bob']);
    $charlie = Participant::factory()->for($event)->create(['name' => 'Charlie']);
    $diana = Participant::factory()->for($event)->create(['name' => 'Diana']);

    // Alice cannot give to Bob
    $event->exclusions()->create([
        'participant_id' => $alice->id,
        'excluded_participant_id' => $bob->id,
    ]);

    DrawNames::run($event);

    $aliceAssignment = $alice->fresh()->assignment;

    expect($aliceAssignment->receiver_id)->not->toBe($bob->id);
});
