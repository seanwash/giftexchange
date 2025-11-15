<x-layout.guest :title="$participant->event->name" :userName="$participant->name" :theme="'theme-' . $participant->event->theme">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="text-center text-3xl font-bold tracking-tight text-gray-900">
            {{ $participant->event->name }}
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Welcome, {{ $participant->name }}!
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <x-ui.card>
            <form action="{{ route('participant.storeInterests', $participant->access_token) }}" method="POST" x-data="interestsForm()">
                @csrf

                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Share Your Interests</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Tell your gift giver a few things you're interested in. This is optional but helps them choose a great gift!
                        </p>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(interest, index) in interests" x-bind:key="index">
                            <div class="flex gap-2">
                                <div class="flex-1">
                                    <input
                                        type="text"
                                        x-bind:name="'interests[' + index + ']'"
                                        x-model="interests[index]"
                                        x-bind:placeholder="'Something you're interested in #' + (index + 1)"
                                        class="block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    >
                                </div>
                                <x-ui.button
                                    type="button"
                                    variant="secondary"
                                    size="sm"
                                    @click="removeInterest(index)"
                                    x-show="interests.length > 1"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </x-ui.button>
                            </div>
                        </template>

                        <x-ui.button
                            type="button"
                            variant="secondary"
                            size="sm"
                            @click="addInterest"
                            x-show="interests.length < 10"
                        >
                            Add Another
                        </x-ui.button>
                    </div>

                    @if($participant->event->event_date || $participant->event->max_gift_amount)
                        <div class="rounded-md bg-blue-50 p-4">
                            <div class="text-sm text-blue-700">
                                @if($participant->event->event_date)
                                    <p><strong>Event Date:</strong> {{ $participant->event->event_date->format('F j, Y') }}</p>
                                @endif
                                @if($participant->event->max_gift_amount)
                                    <p><strong>Gift Budget:</strong> ${{ number_format($participant->event->max_gift_amount / 100, 2) }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-col gap-2">
                        <x-ui.button type="submit" variant="primary" size="lg" class="w-full">
                            Continue to Drawing
                        </x-ui.button>
                        <p class="text-center text-xs text-gray-500">
                            You can skip adding interests if you prefer
                        </p>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </div>

    @php
        $defaultInterests = ['', '', '', '', ''];
    @endphp

    <script>
        function interestsForm() {
            return {
                interests: @json(old('interests', $defaultInterests)),

                addInterest() {
                    if (this.interests.length < 10) {
                        this.interests.push('');
                    }
                },

                removeInterest(index) {
                    if (this.interests.length > 1) {
                        this.interests.splice(index, 1);
                    }
                }
            }
        }
    </script>
</x-layout.guest>
