@props([
    'event'
])

<x-ui.card x-data="{ editing: false }">
    <div class="px-4 py-5 sm:p-6">
        <div class="flex items-start justify-between">
            <div x-show="!editing" class="flex-1">
                <h3 class="text-lg font-medium leading-6 text-gray-900">{{ $event->name }}</h3>

                @if($event->description)
                    <p class="mt-1 text-sm text-gray-500">{{ $event->description }}</p>
                @endif
            </div>

            <div x-show="!editing" class="ml-4">
                <x-ui.button
                    type="button"
                    variant="secondary"
                    size="sm"
                    @click="editing = true"
                >
                    Edit
                </x-ui.button>
            </div>

            <div x-show="editing" class="flex-1">
                <form action="{{ route('events.update', $event->event_token) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-4">
                        <x-ui.input
                            name="name"
                            label="Event Name"
                            :value="old('name', $event->name)"
                            required
                            :error="$errors->first('name')"
                        />

                        <x-ui.textarea
                            name="description"
                            label="Description (Optional)"
                            :error="$errors->first('description')"
                        >{{ old('description', $event->description) }}</x-ui.textarea>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-ui.input
                                name="event_date"
                                type="date"
                                label="Event Date (Optional)"
                                :value="old('event_date', $event->event_date?->format('Y-m-d'))"
                                :error="$errors->first('event_date')"
                            />

                            <x-ui.input
                                name="event_time"
                                type="time"
                                label="Event Time (Optional)"
                                :value="old('event_time', $event->event_time)"
                                :error="$errors->first('event_time')"
                            />
                        </div>

                        <div class="flex justify-end gap-3">
                            <x-ui.button
                                type="button"
                                variant="secondary"
                                @click="editing = false"
                            >
                                Cancel
                            </x-ui.button>
                            <x-ui.button
                                type="submit"
                                variant="primary"
                            >
                                Save
                            </x-ui.button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3" x-show="!editing">
            @if($event->event_date)
                <div class="overflow-hidden rounded-lg bg-gray-50 px-4 py-5 shadow sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500">Event Date</dt>
                    <dd class="mt-1 text-sm font-semibold tracking-tight text-gray-900">
                        {{ $event->event_date->format('M j, Y') }}
                        @if($event->event_time)
                            at {{ $event->event_time }}
                        @endif
                    </dd>
                </div>
            @endif

            @if($event->max_gift_amount)
                <div class="overflow-hidden rounded-lg bg-gray-50 px-4 py-5 shadow sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500">Max Gift Amount</dt>
                    <dd class="mt-1 text-sm font-semibold tracking-tight text-gray-900">
                        ${{ number_format($event->max_gift_amount / 100, 2) }}
                    </dd>
                </div>
            @endif

            <div class="overflow-hidden rounded-lg bg-gray-50 px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Participants</dt>
                <dd class="mt-1 text-sm font-semibold tracking-tight text-gray-900">
                    {{ $event->participants->count() }}
                </dd>
            </div>
        </dl>
    </div>
</x-ui.card>
