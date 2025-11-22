@props([
    'participants',
    'showLinks' => false,
    'event' => null
])

<x-ui.card :padding="false">
    <x-slot name="header">
        <h3 class="text-base font-semibold leading-6 text-gray-900">Participants</h3>
    </x-slot>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        Name
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        Status
                    </th>
                    @if($showLinks)
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                            Actions
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($participants as $participant)
                    <tr>
                        <td class="whitespace-nowrap px-6 py-3 text-sm font-medium text-gray-900">
                            {{ $participant->name }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-3 text-sm text-gray-500">
                            <div class="flex items-center gap-2">
                                @if($participant->has_entered_interests)
                                    <x-ui.badge variant="success">Has entered interests</x-ui.badge>
                                @else
                                    <x-ui.badge variant="warning">Pending interests</x-ui.badge>
                                @endif

                                @if($event && $event->drawing_completed_at)
                                    @if($participant->has_viewed_assignment)
                                        <x-ui.badge variant="success">Viewed assignment</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="info">Not viewed</x-ui.badge>
                                    @endif
                                @endif
                            </div>
                        </td>
                        @if($showLinks)
                            <td class="whitespace-nowrap px-6 py-3 text-right text-sm font-medium">
                                <x-ui.copy-button
                                    :text="route('participant.enter', $participant->access_token)"
                                    label="Copy Link"
                                    size="sm"
                                />
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $showLinks ? '3' : '2' }}" class="px-6 py-8 text-center text-sm text-gray-500">
                            No participants yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-ui.card>
