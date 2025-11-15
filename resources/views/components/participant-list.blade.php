@props([
    'participants',
    'showLinks' => false,
    'event' => null
])

<x-ui.card>
    <x-slot name="header">
        <h3 class="text-base font-semibold leading-6 text-gray-900">Participants</h3>
    </x-slot>

    <div>
        <ul class="divide-y divide-gray-200">
            @forelse($participants as $participant)
                <li class="py-4">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex min-w-0 flex-1 items-center">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-gray-900">{{ $participant->name }}</p>
                                @if($showLinks)
                                    <p class="mt-1 truncate text-xs text-gray-500">{{ route('participant.enter', $participant->access_token) }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex shrink-0 items-center gap-2">
                            @if($participant->has_entered_interests)
                                <x-ui.badge variant="success">Has entered interests</x-ui.badge>
                            @else
                                <x-ui.badge variant="warning">Pending</x-ui.badge>
                            @endif

                            @if($event && $event->drawing_completed_at)
                                @if($participant->has_viewed_assignment)
                                    <x-ui.badge variant="success">Viewed assignment</x-ui.badge>
                                @else
                                    <x-ui.badge variant="info">Not viewed</x-ui.badge>
                                @endif
                            @endif

                            @if($showLinks)
                                <x-ui.copy-button
                                    :text="route('participant.enter', $participant->access_token)"
                                    label="Copy Link"
                                    size="sm"
                                />
                            @endif
                        </div>
                    </div>
                </li>
            @empty
                <li class="py-4 text-center text-sm text-gray-500">
                    No participants yet.
                </li>
            @endforelse
        </ul>
    </div>
</x-ui.card>
