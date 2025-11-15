<x-layout.guest title="Gift Exchange">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
            Gift Exchange
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Organize your gift exchange with ease
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <x-ui.card>
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Get Started</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Create a new gift exchange event and invite your friends, family, or coworkers.
                    </p>
                </div>

                <div class="space-y-4">
                    <x-ui.button
                        variant="primary"
                        size="lg"
                        class="w-full"
                        onclick="window.location.href='{{ route('events.create') }}'"
                    >
                        Create New Event
                    </x-ui.button>
                </div>

                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="bg-white px-2 text-gray-500">Features</span>
                    </div>
                </div>

                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start">
                        <svg class="mr-2 h-5 w-5 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                        </svg>
                        No accounts required
                    </li>
                    <li class="flex items-start">
                        <svg class="mr-2 h-5 w-5 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                        </svg>
                        Share unique links with participants
                    </li>
                    <li class="flex items-start">
                        <svg class="mr-2 h-5 w-5 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                        </svg>
                        Fun spinning wheel to reveal assignments
                    </li>
                    <li class="flex items-start">
                        <svg class="mr-2 h-5 w-5 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                        </svg>
                        Share wish lists with your gift giver
                    </li>
                </ul>
            </div>
        </x-ui.card>
    </div>
</x-layout.guest>
