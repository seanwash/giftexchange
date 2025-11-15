<x-layout.guest :title="$participant->event->name" :userName="$participant->name" :theme="'theme-' . $participant->event->theme">
    <div class="sm:mx-auto sm:w-full sm:max-w-3xl">
        <div class="text-center">
            <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                {{ $participant->event->name }}
            </h1>
            <p class="mt-4 text-lg text-gray-600">
                Time to discover your gift exchange assignment!
            </p>
        </div>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-3xl">
        <x-ui.card>
            @if(session('error'))
                <div class="mb-6">
                    <x-ui.alert variant="error" dismissible>
                        {{ session('error') }}
                    </x-ui.alert>
                </div>
            @endif

            @if(isset($waiting) && $waiting)
                <div class="py-8">
                    <div class="space-y-8 text-center">
                        <!-- Animated Clock Icon -->
                        <div class="flex justify-center">
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="h-24 w-24 rounded-full bg-indigo-100 opacity-75 animate-pulse"></div>
                                </div>
                                <svg class="relative h-24 w-24 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10" stroke-width="2" class="text-indigo-500"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2" class="text-indigo-600"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Heading and Description -->
                        <div class="space-y-3">
                            <h2 class="text-2xl font-bold text-gray-900">Waiting for Other Participants</h2>
                            <p class="mx-auto max-w-lg text-base leading-relaxed text-gray-600">
                                Thanks for sharing your interests! We're waiting for all participants to enter their interests before we can do the drawing. Once everyone has entered their interests, the drawing will happen automatically. You can refresh this page to check if your assignment is ready.
                            </p>
                        </div>

                        <!-- Refresh Button -->
                        <div class="pt-4">
                            <x-ui.button
                                variant="primary"
                                size="lg"
                                onclick="window.location.reload()"
                                class="min-w-[160px]"
                            >
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Refresh Page
                            </x-ui.button>
                        </div>
                    </div>
                </div>
            @else
                <div class="py-8" x-data="spinWheel()">
                    <div class="space-y-10">
                        <!-- Header Section -->
                        <div class="text-center space-y-2">
                            <h2 class="text-2xl font-bold text-gray-900">Spin the Wheel</h2>
                            <p class="text-base text-gray-600">
                                Click the button below to discover who you'll be giving a gift to
                            </p>
                        </div>

                        <!-- Spinning Wheel Component -->
                        <div class="flex justify-center">
                            <div class="relative">
                                <!-- Outer glow effect -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="h-80 w-80 rounded-full bg-indigo-200 opacity-20 blur-2xl"></div>
                                </div>
                                
                                <!-- Wheel Container with Segments -->
                                <div class="relative h-72 w-72">
                                    <!-- Segmented Wheel -->
                                    <div 
                                        class="wheel relative h-full w-full rounded-full border-8 border-indigo-600 shadow-2xl"
                                        :class="{ 'wheel-spinning': isSpinning }"
                                        :style="`transform: rotate(${rotation}deg);`"
                                    >
                                        <!-- Wheel segments using conic gradient -->
                                        <div class="absolute inset-0 rounded-full" style="background: conic-gradient(
                                            from 0deg,
                                            #6366f1 0deg 45deg,
                                            #8b5cf6 45deg 90deg,
                                            #6366f1 90deg 135deg,
                                            #8b5cf6 135deg 180deg,
                                            #6366f1 180deg 225deg,
                                            #8b5cf6 225deg 270deg,
                                            #6366f1 270deg 315deg,
                                            #8b5cf6 315deg 360deg
                                        );"></div>
                                        
                                        <!-- Segment dividers -->
                                        <svg class="absolute inset-0 h-full w-full" viewBox="0 0 100 100">
                                            <line x1="50" y1="50" x2="50" y2="0" stroke="rgba(99, 102, 241, 0.4)" stroke-width="0.8"/>
                                            <line x1="50" y1="50" x2="85.36" y2="14.64" stroke="rgba(99, 102, 241, 0.4)" stroke-width="0.8"/>
                                            <line x1="50" y1="50" x2="100" y2="50" stroke="rgba(99, 102, 241, 0.4)" stroke-width="0.8"/>
                                            <line x1="50" y1="50" x2="85.36" y2="85.36" stroke="rgba(99, 102, 241, 0.4)" stroke-width="0.8"/>
                                            <line x1="50" y1="50" x2="50" y2="100" stroke="rgba(99, 102, 241, 0.4)" stroke-width="0.8"/>
                                            <line x1="50" y1="50" x2="14.64" y2="85.36" stroke="rgba(99, 102, 241, 0.4)" stroke-width="0.8"/>
                                            <line x1="50" y1="50" x2="0" y2="50" stroke="rgba(99, 102, 241, 0.4)" stroke-width="0.8"/>
                                            <line x1="50" y1="50" x2="14.64" y2="14.64" stroke="rgba(99, 102, 241, 0.4)" stroke-width="0.8"/>
                                        </svg>
                                        
                                        <!-- Inner decorative ring -->
                                        <div class="absolute inset-8 rounded-full border-4 border-white/30 bg-gradient-to-br from-white/20 to-transparent"></div>
                                        <div class="absolute inset-16 rounded-full border-2 border-white/20"></div>
                                        
                                        <!-- Center content -->
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <div class="relative z-10 rounded-full bg-white/95 p-8 shadow-lg">
                                                <div class="text-center">
                                                    <div class="mb-3" x-show="!isSpinning">
                                                        <svg class="mx-auto h-20 w-20 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                                        </svg>
                                                    </div>
                                                    <p class="mt-2 text-sm font-medium text-indigo-600" x-show="!isSpinning">Ready to spin!</p>
                                                    <p class="text-sm font-semibold text-indigo-600" x-show="isSpinning">Spinning...</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Pointer indicator at top -->
                                    <div class="pointer-events-none absolute -top-4 left-1/2 z-20 -translate-x-1/2">
                                        <svg class="h-8 w-8 text-indigo-600 drop-shadow-lg" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 0L13.09 6.26L20 7.27L15 12.14L16.18 20.02L10 16.77L3.82 20.02L5 12.14L0 7.27L6.91 6.26L10 0Z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Spin Button -->
                        <div class="flex justify-center pt-4">
                            <form 
                                method="POST" 
                                action="{{ route('participant.doSpin', $participant->access_token) }}"
                                @submit.prevent="handleSpin"
                                x-ref="spinForm"
                            >
                                @csrf
                                <x-ui.button
                                    type="submit"
                                    variant="primary"
                                    size="lg"
                                    class="min-w-[200px] text-lg font-semibold shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                    x-bind:disabled="isSpinning"
                                >
                                    <svg class="mr-2 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!isSpinning">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    <svg class="mr-2 h-6 w-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="isSpinning">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    <span x-text="isSpinning ? 'Spinning...' : 'Spin the Wheel!'"></span>
                                </x-ui.button>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                    function spinWheel() {
                        return {
                            isSpinning: false,
                            rotation: 0,
                            
                            handleSpin() {
                                if (this.isSpinning) {
                                    return;
                                }
                                
                                this.isSpinning = true;
                                
                                // Calculate random spin (multiple full rotations + random angle)
                                const baseRotations = 5; // Minimum 5 full rotations
                                const randomRotations = Math.random() * 3; // Additional 0-3 rotations
                                const randomAngle = Math.random() * 360; // Final random angle
                                const totalRotation = (baseRotations + randomRotations) * 360 + randomAngle;
                                
                                // Add to current rotation
                                this.rotation += totalRotation;
                                
                                // Submit form after animation completes (2.5 seconds)
                                setTimeout(() => {
                                    this.$refs.spinForm.submit();
                                }, 2500);
                            }
                        }
                    }
                </script>
            @endif
        </x-ui.card>
    </div>
</x-layout.guest>
