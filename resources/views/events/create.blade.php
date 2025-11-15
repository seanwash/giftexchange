<x-layout.app title="Create Gift Exchange Event">
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Create Gift Exchange Event</h1>
            <p class="mt-2 text-sm text-gray-600">Set up your gift exchange event and invite participants</p>
        </div>

        <form action="{{ route('events.store') }}" method="POST" x-data="eventForm()">
            @csrf

            <div class="space-y-6">
                <!-- Event Details Card -->
                <x-ui.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Event Details</h3>
                    </x-slot>

                    <div class="space-y-4">
                        <x-ui.input
                            name="name"
                            label="Event Name"
                            placeholder="Holiday Gift Exchange 2025"
                            required
                            :error="$errors->first('name')"
                            :value="old('name')"
                        />

                        <x-ui.textarea
                            name="description"
                            label="Description (Optional)"
                            placeholder="A fun gift exchange for the holidays!"
                            :error="$errors->first('description')"
                        >{{ old('description') }}</x-ui.textarea>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-ui.input
                                name="event_date"
                                type="date"
                                label="Event Date (Optional)"
                                :error="$errors->first('event_date')"
                                :value="old('event_date')"
                            />

                            <x-ui.input
                                name="event_time"
                                type="time"
                                label="Event Time (Optional)"
                                :error="$errors->first('event_time')"
                                :value="old('event_time')"
                            />
                        </div>

                        <x-ui.input
                            name="max_gift_amount"
                            type="number"
                            step="0.01"
                            label="Max Gift Amount (Optional)"
                            placeholder="25.00"
                            :error="$errors->first('max_gift_amount')"
                            :value="old('max_gift_amount')"
                            help="Set a maximum spending limit for gifts"
                        />

                        <x-ui.select
                            name="theme"
                            label="Theme"
                            required
                            :error="$errors->first('theme')"
                        >
                            <option value="default" {{ old('theme', 'default') === 'default' ? 'selected' : '' }}>Default</option>
                            <option value="winter" {{ old('theme') === 'winter' ? 'selected' : '' }}>Winter</option>
                            <option value="christmas" {{ old('theme') === 'christmas' ? 'selected' : '' }}>Christmas</option>
                            <option value="valentine" {{ old('theme') === 'valentine' ? 'selected' : '' }}>Valentine</option>
                        </x-ui.select>
                    </div>
                </x-ui.card>

                <!-- Participants Card -->
                <x-ui.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Participants</h3>
                        <p class="mt-1 text-sm text-gray-500">Add at least 3 participants</p>
                    </x-slot>

                    <div class="space-y-4">
                        <template x-for="(participant, index) in participants" x-bind:key="index">
                            <div class="flex gap-2">
                                <div class="flex-1">
                                    <input
                                        type="text"
                                        x-bind:name="'participants[' + index + ']'"
                                        x-model="participants[index]"
                                        placeholder="Participant name"
                                        required
                                        class="block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    >
                                </div>
                                <x-ui.button
                                    type="button"
                                    variant="danger"
                                    @click="removeParticipant(index)"
                                    x-show="participants.length > 3"
                                >
                                    Remove
                                </x-ui.button>
                            </div>
                        </template>

                        @error('participants')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <x-ui.button
                            type="button"
                            variant="secondary"
                            @click="addParticipant"
                        >
                            Add Participant
                        </x-ui.button>
                    </div>
                </x-ui.card>

                <!-- Submit -->
                <div class="flex justify-end gap-3">
                    <x-ui.button type="submit" variant="primary" size="lg">
                        Create Event
                    </x-ui.button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function eventForm() {
            return {
                participants: @json(old('participants') ?: ['', '', '']),

                addParticipant() {
                    this.participants.push('');
                },

                removeParticipant(index) {
                    if (this.participants.length > 3) {
                        this.participants.splice(index, 1);
                    }
                }
            }
        }
    </script>
</x-layout.app>
