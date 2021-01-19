<x-guest-layout>
    <div class="bg-gray-100 flex flex-col h-screen justify-between">
        @include('layouts.sections.nav')
        <div class="flex-grow">
            {{ $slot }}
        </div>
        @include('layouts.sections.footer')
    </div>
</x-guest-layout>
