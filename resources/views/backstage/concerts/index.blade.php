<x-promoter-layout>
    <div class="container mx-auto py-5">
        <div class="flex align-baseline justify-between">
            <p class="text-xl">Your concerts</p>
            <a href="/backstage/concerts/new" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Add Concert
            </a>
        </div>
        <div>
            <!-- published -->
            <div>
                <h2>Published</h2>
                <div class="grid grid-cols-3 gap-3">
                    @foreach($publishedConcerts as $concert)
                        <div class="bg-white rounded shadow p-3">
                            <h4 class="text-xl font-bold">{{ $concert->title }}</h4>
                            <p class="opacity-75">{{ $concert->subtitle }}</p>
                            <p class="text-sm mt-2">{{ $concert->venue }} - {{ $concert->city }} {{ $concert->state }}</p>
                            <p class="text-sm mt-2">{{$concert->formatted_date}} {{$concert->formatted_start_time}}</p>
                            <a href="/backstage/published-concerts/{{ $concert->id }}/orders" class="mt-2 inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Manage
                            </a>
                            <a href="/concerts/{{ $concert->id }}" class="mt-2 inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Get Ticket Link
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- drafts -->
            <div class="mt-20">
                <h2>Drafts</h2>
                <div class="grid grid-cols-3 gap-3">
                    @foreach($unpublishedConcerts as $concert)
                        <div class="bg-white rounded shadow p-3">
                            <h4 class="text-xl font-bold">{{ $concert->title }}</h4>
                            <p class="opacity-75">{{ $concert->subtitle }}</p>
                            <p class="text-sm mt-2">{{ $concert->venue }} - {{ $concert->city }} {{ $concert->state }}</p>
                            <p class="text-sm mt-2">{{$concert->formatted_date}} {{$concert->formatted_start_time}}</p>
                            <div class="flex space-x-2">
                                <a href="/backstage/concerts/{{$concert->id}}/edit" class="mt-2 inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Edit
                                </a>
                                <form class="inline-block" action="{{ route('backstage.publish-concert.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="concert_id" id="concert_id" value="{{ $concert->id }}">
                                    <button type="submit" class="mt-2 inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Publish
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-promoter-layout>
