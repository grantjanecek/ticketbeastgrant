<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.7.3/dist/alpine.js" defer></script>
</head>
<body>
<div class="bg-gray-100 font-sans text-gray-900 antialiased min-h-screen">
    <div class="max-w-3xl mx-auto">
        <div class="flex flex-row justify-between border-b py-4">
            <div>
                Order Summary
            </div>
            <div>
                {{$order->confirmation_number}}
            </div>
        </div>
        <div class="border-b py-4">
            <span class="font-bold"> Total: ${{number_format($order->amount/100, 2)}} </span><br>
            <span class="text-gray-500">Billed to Card #:**** **** **** {{$order->card_last_four}}</span>
        </div>
        <h1 class="mb-4">Your Tickets</h1>
        <div class="flex flex-col space-y-2">
            @foreach($order->tickets as $ticket)
                <div>
                    <div class="flex flex-row justify-between bg-gray-600 text-white px-4 py-2 rounded-t-lg">
                        <div>
                            <h2 class="text-lg font-bold">{{ $ticket->concert->title }}</h2>
                            <h3 class="text-md text-gray-300">{{ $ticket->concert->subtitle }}</h3>
                        </div>

                        <div class="text-md text-right">
                            <h3 class="font-bold">General Admission</h3>
                            <h4 class="text-gray-300">Admit One</h4>
                        </div>
                    </div>

                    <div class="bg-white grid grid-cols-2 px-4 py-2 border-b">
                        <div>
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M1 4c0-1.1.9-2 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4zm2 2v12h14V6H3zm2-6h2v2H5V0zm8 0h2v2h-2V0zM5 9h2v2H5V9zm0 4h2v2H5v-2zm4-4h2v2H9V9zm0 4h2v2H9v-2zm4-4h2v2h-2V9zm0 4h2v2h-2v-2z"/></svg>
                            <time datetime="{{ $ticket->concert->date->format('Y-m-d H:i')}}">
                                <p class="text-gray-700">{{ $ticket->concert->date->format('l, F jS, Y')}}</p>
                            </time>
                            <p class="text-gray-500">Doors at {{ $ticket->concert->date->format('g:ia')}}</p>
                        </div>

                        <div>
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M0 0l20 8-8 4-2 8z"/></svg>
                            <h3 class="text-gray-700">{{ $ticket->concert->venue}}</h3>
                            <h3 class="text-gray-500">{{ $ticket->concert->venue_address}}</h3>
                            <h3 class="text-gray-500">{{ $ticket->concert->city }}, {{ $ticket->concert->state }} {{ $ticket->concert->zip }}</h3>
                        </div>
                    </div>

                    <div>
                        <div class="bg-white flex flex-cols justify-between px-4 py-2 rounded-b-lg">
                            <p>{{ $ticket->code }}</p>
                            <p>{{ $order->email }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
</body>
</html>
