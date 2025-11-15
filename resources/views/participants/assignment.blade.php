<x-layout.guest :title="$participant->event->name" :userName="$participant->name" :theme="'theme-' . $participant->event->theme">
    <div class="sm:mx-auto sm:w-full sm:max-w-2xl">
        <h2 class="text-center text-3xl font-bold tracking-tight text-gray-900">
            {{ $participant->event->name }}
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Your Gift Exchange Assignment
        </p>
    </div>

    <div class="mt-8 space-y-6 sm:mx-auto sm:w-full sm:max-w-2xl">
        <!-- Assignment Info -->
        <x-ui.card>
            <div class="text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                    </svg>
                </div>
                <h3 class="mt-4 text-2xl font-bold text-gray-900">You're giving a gift to:</h3>
                <p class="mt-2 text-4xl font-extrabold text-indigo-600">{{ $receiver->name }}</p>

                @if($participant->event->event_date || $participant->event->max_gift_amount)
                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @if($participant->event->event_date)
                            <div class="rounded-lg bg-blue-50 p-4">
                                <p class="text-sm font-medium text-blue-900">Event Date</p>
                                <p class="mt-1 text-lg font-semibold text-blue-600">
                                    {{ $participant->event->event_date->format('M j, Y') }}
                                    @if($participant->event->event_time)
                                        at {{ $participant->event->event_time }}
                                    @endif
                                </p>
                            </div>
                        @endif
                        @if($participant->event->max_gift_amount)
                            <div class="rounded-lg bg-green-50 p-4">
                                <p class="text-sm font-medium text-green-900">Gift Budget</p>
                                <p class="mt-1 text-lg font-semibold text-green-600">
                                    ${{ number_format($participant->event->max_gift_amount / 100, 2) }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </x-ui.card>

        <!-- Receiver's Interests -->
        @if($receiver->interests->isNotEmpty())
            <x-ui.card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $receiver->name }}'s Interests</h3>
                    <p class="mt-1 text-sm text-gray-500">Here are some things they're interested in</p>
                </x-slot>

                <ul class="space-y-3">
                    @foreach($receiver->interests as $interest)
                        <li class="flex items-start">
                            <svg class="mr-3 h-6 w-6 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">{{ $interest->interest_text }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-ui.card>
        @else
            <x-ui.card>
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No interests shared yet</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $receiver->name }} hasn't shared their interests yet, but you can still surprise them with something thoughtful!
                    </p>
                </div>
            </x-ui.card>
        @endif

        <!-- Important Note -->
        <x-ui.alert variant="info">
            <div class="text-sm">
                <strong>Important:</strong> Remember to keep this a secret! Don't tell anyone who you're giving a gift to.
                Save this page or bookmark it to come back and review {{ $receiver->name }}'s interests anytime.
            </div>
        </x-ui.alert>
    </div>

    @if(isset($showConfetti) && $showConfetti)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Trigger confetti celebration
                const duration = 3000;
                const animationEnd = Date.now() + duration;
                const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };

                function randomInRange(min, max) {
                    return Math.random() * (max - min) + min;
                }

                const interval = setInterval(function() {
                    const timeLeft = animationEnd - Date.now();

                    if (timeLeft <= 0) {
                        return clearInterval(interval);
                    }

                    const particleCount = 50 * (timeLeft / duration);
                    
                    // Launch confetti from left
                    window.confetti({
                        ...defaults,
                        particleCount,
                        origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 }
                    });
                    
                    // Launch confetti from right
                    window.confetti({
                        ...defaults,
                        particleCount,
                        origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 }
                    });
                }, 250);
            });
        </script>
    @endif
</x-layout.guest>
