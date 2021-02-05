<x-guest-layout>
    <div class="min-h-screen bg-gray-200 py-6 flex flex-col justify-center sm:py-12">
        <div class="relative px-4 py-5 bg-white shadow-lg sm:rounded-3xl sm:p-20 sm:max-w-xl sm:mx-auto">
            <div class="flex mx-auto">
                @if($concert->hasPoster())
                    <img src="{{ $concert->posterUrl() }}" />
                @endif

                <div class="text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                    <h1 class="text-3xl font-bold -mb-4">{{$concert->title}}</h1>
                    <h2 class="text-sm text-gray-600">{{$concert->subtitle}}</h2>
                    <h3>{{$concert->formatted_date}}</h3>
                    <h3>Doors at {{$concert->formatted_start_time}}</h3>
                    <h3>{{$concert->ticket_price_in_dollars}}</h3>
                    <h3>{{$concert->venue}}</h3>
                    <h3>{{$concert->venue_address}} <br>{{$concert->city}} {{$concert->state}} {{$concert->zip}}</h3>
                    <h3>{{$concert->additional_information}}</h3>
                </div>

            </div>
        </div>
    </div>
</x-guest-layout>
