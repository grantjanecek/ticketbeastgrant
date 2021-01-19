<x-promoter-layout>
    <div class="container mx-auto py-5">
        <div class="flex align-baseline justify-between">
            <div class="flex align-middle space-y-2">
                <p class="text-xl font-bold">{{ $concert->title }}</p>
                /
                <p class="text-sm text-gray-500">{{ $concert->formatted_date }}</p>
            </div>
            <p class="text-xl font-bold">Orders</p>
        </div>
        <div class="mt-10">
            <p class="text-2xl text-gray-600">Overview</p>
            <div class="grid grid-cols-3 bg-white rounded shadow">
                <div class="col-span-3 p-4 border-b">
                    <p>
                        This show is {{ $concert->percentSoldOut() }}% sold out
                    </p>
                    <progress class="w-full rounded shadow overflow-hidden mt-2" max="{{ $concert->totalTickets() }}" value="{{ $concert->ticketsSold() }}">{{ $concert->percentSoldOut() }}%</progress>
                </div>
                <div class="col-span-1 p-4 border-r">
                    <h3 class="text-gray-600">
                        Total Tickets Remaining
                    </h3>
                    <div class="text-3xl font-bold">
                        {{ $concert->ticketsRemaining() }}
                    </div>
                </div>
                <div class="col-span-1 p-4 border-r">
                    <h3 class="text-gray-600">
                        Total Tickets Sold
                    </h3>
                    <div class="text-3xl font-bold">
                        {{ $concert->ticketsSold() }}
                    </div>
                </div>
                <div class="col-span-1 p-4">
                    <h3 class="text-gray-600">
                        Total Revenue
                    </h3>
                    <div class="text-3xl font-bold">
                        ${{ $concert->revenueInDollars() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-10">
            <p class="text-2xl text-gray-600">Recent Orders</p>
            <div>
                @include('backstage.published-concert-orders._orders')
            </div>
        </div>
    </div>
</x-promoter-layout>
