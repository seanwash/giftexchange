<x-layout.admin :title="$event->name . ' - Admin'" :eventName="$event->name" :theme="'theme-' . $event->theme">
    <x-slot name="nav">
        <x-theme-switcher :currentTheme="$event->theme" />
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <x-ui.alert variant="success" dismissible>
                {{ session('success') }}
            </x-ui.alert>
        @endif

        @if(session('error'))
            <x-ui.alert variant="error" dismissible>
                {{ session('error') }}
            </x-ui.alert>
        @endif

        <!-- Event Info -->
        <x-event-info-card :event="$event" />

        <!-- Exclusions Management -->
        <x-ui.card>
            <x-slot name="header">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Exclusions</h3>
                <p class="mt-1 text-sm text-gray-500">Prevent certain participants from being assigned to each other (e.g., spouses)</p>
            </x-slot>

            @if($event->drawing_completed_at)
                <x-ui.alert variant="info" class="mb-4">
                    Exclusions cannot be modified after the drawing is complete.
                </x-ui.alert>
            @endif

            <!-- Existing Exclusions -->
            @php
                // Filter to show only unique pairs (one direction per pair)
                $uniqueExclusions = $event->exclusions->filter(function ($exclusion) {
                    return $exclusion->participant_id < $exclusion->excluded_participant_id;
                });
            @endphp
            @if($uniqueExclusions->count() > 0)
                <div class="mb-6">
                    <h4 class="mb-3 text-sm font-medium text-gray-900">Current Exclusions</h4>
                    <div class="space-y-2">
                        @foreach($uniqueExclusions as $exclusion)
                            <div class="flex items-center justify-between rounded-md border border-gray-200 bg-gray-50 px-4 py-2">
                                <span class="text-sm text-gray-700">
                                    <strong>{{ $exclusion->participant->name }}</strong> and <strong>{{ $exclusion->excludedParticipant->name }}</strong> cannot be assigned to each other
                                </span>
                                @if(!$event->drawing_completed_at)
                                    <form action="{{ route('exclusions.destroy', [$event->event_token, $exclusion]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" variant="danger" size="sm">
                                            Remove
                                        </x-ui.button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="mb-6 rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
                    <p class="text-sm text-gray-600">No exclusions set. All participants can be assigned to each other.</p>
                </div>
            @endif

            <!-- Add Exclusion Form -->
            @if(!$event->drawing_completed_at)
                <div>
                    <h4 class="mb-3 text-sm font-medium text-gray-900">Add Exclusion</h4>
                    <form action="{{ route('exclusions.store', $event->event_token) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-ui.select
                                name="participant_id"
                                label="Participant"
                                required
                                :error="$errors->first('participant_id')"
                            >
                                <option value="">Select a participant...</option>
                                @foreach($event->participants as $participant)
                                    <option value="{{ $participant->id }}" {{ old('participant_id') == $participant->id ? 'selected' : '' }}>
                                        {{ $participant->name }}
                                    </option>
                                @endforeach
                            </x-ui.select>

                            <x-ui.select
                                name="excluded_participant_id"
                                label="Cannot be assigned to"
                                required
                                :error="$errors->first('excluded_participant_id')"
                            >
                                <option value="">Select a participant...</option>
                                @foreach($event->participants as $participant)
                                    <option value="{{ $participant->id }}" {{ old('excluded_participant_id') == $participant->id ? 'selected' : '' }}>
                                        {{ $participant->name }}
                                    </option>
                                @endforeach
                            </x-ui.select>
                        </div>

                        <div class="mt-4">
                            <x-ui.button type="submit" variant="primary">
                                Add Exclusion
                            </x-ui.button>
                        </div>
                    </form>
                </div>
            @endif
        </x-ui.card>

        <!-- Participants List -->
        <x-participant-list
            :participants="$event->participants"
            :showLinks="true"
            :event="$event"
        />

        <!-- Progress Indicator -->
        <x-ui.card>
            <x-slot name="header">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Participation Progress</h3>
            </x-slot>

            <div class="space-y-6">
                <!-- Step 1: Enter Interests -->
                <div>
                    <div class="mb-3 flex items-center gap-2">
                        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full {{ $completedCount === $event->participants->count() ? 'bg-indigo-600' : 'bg-gray-300' }}">
                            @if($completedCount === $event->participants->count())
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            @else
                                <span class="text-xs font-semibold text-white">1</span>
                            @endif
                        </div>
                        <h4 class="text-sm font-medium text-gray-900">Enter Interests</h4>
                    </div>
                    <div class="ml-8">
                        <x-progress-indicator
                            :total="$event->participants->count()"
                            :completed="$completedCount"
                            label="Participants who have entered their interests"
                        />
                    </div>
                </div>

                <!-- Drawing Status -->
                @if($completedCount === $event->participants->count() && !$event->drawing_completed_at)
                    <div class="ml-8">
                        <x-ui.alert variant="success" class="text-sm">
                            <strong>Ready for drawing!</strong> The drawing will happen automatically when everyone has viewed their assignment link.
                        </x-ui.alert>
                    </div>
                @endif

                <!-- Step 2: View Assignment -->
                @if($event->drawing_completed_at)
                    <div>
                        <div class="mb-3 flex items-center gap-2">
                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full {{ $viewedCount === $event->participants->count() ? 'bg-indigo-600' : 'bg-gray-300' }}">
                                @if($viewedCount === $event->participants->count())
                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                @else
                                    <span class="text-xs font-semibold text-white">2</span>
                                @endif
                            </div>
                            <h4 class="text-sm font-medium text-gray-900">View Assignment</h4>
                        </div>
                        <div class="ml-8">
                            <x-progress-indicator
                                :total="$event->participants->count()"
                                :completed="$viewedCount"
                                label="Participants who have viewed their assignment"
                            />
                        </div>
                    </div>
                @endif
            </div>
        </x-ui.card>

        <!-- Admin Instructions -->
        <x-ui.card>
            <x-slot name="header">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Admin Instructions</h3>
                </div>
            </x-slot>

            <div class="space-y-6">
                <div>
                    <h4 class="mb-3 text-sm font-medium text-gray-900">Getting Started</h4>
                    <ol class="space-y-2.5 text-sm text-gray-700">
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-700">1</span>
                            <span class="pt-0.5">Set up exclusions if needed (e.g., prevent spouses from being assigned to each other)</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-700">2</span>
                            <span class="pt-0.5">Copy and share each participant's unique link with them</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-700">3</span>
                            <span class="pt-0.5">Participants will enter their interests (optional but encouraged)</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-700">4</span>
                            <span class="pt-0.5">Participants will be automatically assigned once all have visited</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-700">5</span>
                            <span class="pt-0.5">Each participant can only see who they're assigned to give a gift to</span>
                        </li>
                    </ol>
                </div>

                <div class="rounded-lg border border-indigo-100 bg-indigo-50 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 shrink-0 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <div class="flex-1">
                            <p class="font-medium text-indigo-900">Your Admin Link</p>
                            <p class="mt-1 text-sm text-indigo-700">Save this link to access your admin dashboard later</p>
                            <div class="mt-3 flex items-center gap-2">
                                <code class="block flex-1 rounded-md bg-white border border-indigo-200 px-3 py-2 text-sm text-gray-900 font-mono">
                                    {{ route('events.show', $event->event_token) }}
                                </code>
                                <x-ui.copy-button
                                    :text="route('events.show', $event->event_token)"
                                    label="Copy"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>
</x-layout.admin>
