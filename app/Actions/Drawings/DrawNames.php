<?php

namespace App\Actions\Drawings;

use App\Models\Assignment;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;
use RuntimeException;

class DrawNames
{
    use AsAction;

    public function handle(Event $event): void
    {
        DB::transaction(function () use ($event) {
            $participants = $event->participants()->get();

            if ($participants->count() < 2) {
                throw new RuntimeException('Need at least 2 participants to draw names');
            }

            $exclusions = $event->exclusions()
                ->get()
                ->groupBy('participant_id')
                ->map(fn ($group) => $group->pluck('excluded_participant_id')->toArray())
                ->toArray();

            // Try to create valid assignments (with retry logic)
            $maxAttempts = 100;
            $assignments = null;

            for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
                $assignments = $this->attemptDrawing($participants, $exclusions);

                if ($assignments !== null) {
                    break;
                }
            }

            if ($assignments === null) {
                throw new RuntimeException('Could not create valid assignments after '.$maxAttempts.' attempts. Check exclusions.');
            }

            foreach ($assignments as $giverId => $receiverId) {
                Assignment::create([
                    'event_id' => $event->id,
                    'giver_id' => $giverId,
                    'receiver_id' => $receiverId,
                ]);
            }

            $event->update(['drawing_completed_at' => now()]);
        });
    }

    protected function attemptDrawing($participants, array $exclusions): ?array
    {
        $participantIds = $participants->pluck('id')->toArray();
        $shuffled = $participants->shuffle()->pluck('id')->toArray();
        $assignments = [];

        for ($i = 0; $i < count($shuffled); $i++) {
            $giverId = $shuffled[$i];
            $receiverId = $shuffled[($i + 1) % count($shuffled)];

            if ($this->isValidAssignment($giverId, $receiverId, $exclusions)) {
                $assignments[$giverId] = $receiverId;
            } else {
                return null;
            }
        }

        return $assignments;
    }

    protected function isValidAssignment(int $giverId, int $receiverId, array $exclusions): bool
    {
        if ($giverId === $receiverId) {
            return false;
        }

        if (isset($exclusions[$giverId]) && in_array($receiverId, $exclusions[$giverId])) {
            return false;
        }

        return true;
    }
}
