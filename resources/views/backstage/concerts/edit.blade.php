<x-promoter-layout>
    <div></div>
    <div>
        <form action="/backstage/concerts/new" method="POST"  class="space-y-10">
            @csrf
            <!-- Header info -->
            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Profile</h3>
                    <p class="mt-1 text-sm leading-5 text-gray-500">
                        This information will be displayed publicly so be careful what you share.
                    </p>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2 space-y-6">
                        <div class="grid grid-cols-3 gap-6">
                            <div class="col-span-3 sm:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                <div class="mt-1">
                                    <input type="text" name="title" id="title" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Slayer" value="{{ old('title', $concert->title) }}">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-6">
                            <div class="col-span-3 sm:col-span-2">
                            <label for="subtitle" class="block text-sm font-medium text-gray-700">Subtitle</label>
                                <div class="mt-1">
                                    <input type="text" name="subtitle" id="subtitle" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="with Opensers (optional)" value="{{ old('subtitle', $concert->subtitle) }}">
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                        <label for="additional_information" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                            Additional Information
                        </label>
                        <div class="mt-1 sm:mt-0 sm:col-span-2">
                            <textarea id="additional_information" name="additional_information" rows="3" class="max-w-lg shadow-sm block w-full focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 rounded-md" placeholder="This concert is 19+" value="{{ old('additional_information', $concert->additional_information) }}"></textarea>
                        </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date info -->
            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Date & Time</h3>
                    <p class="mt-1 text-sm leading-5 text-gray-500">
                        True meatalheads only care about the obscure openers, so make sure that they get there on time
                    </p>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2 space-y-6">
                        <div class="flex space-x-10">
                            <div class="w-1/2">
                                <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                                <div class="mt-1">
                                    <input type="text" name="date" id="date" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="yyy-mm-dd" value="{{ old('date', $concert->date->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="w-1/2">
                                <label for="time" class="block text-sm font-medium text-gray-700">Time</label>
                                <div class="mt-1">
                                    <input type="text" name="time" id="time" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="7:00pm" value="{{ old('time', $concert->date->format('g:ia')) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Venue info -->
            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Venue Information</h3>
                    <p class="mt-1 text-sm leading-5 text-gray-500">
                        Wheres the show?
                    </p>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2 space-y-6">
                        <div class="grid grid-cols-3 gap-6">
                            <div class="col-span-3 sm:col-span-2">
                                <label for="venue" class="block text-sm font-medium text-gray-700">Venue</label>
                                <div class="mt-1">
                                    <input type="text" name="venue" id="venue" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="The Mosh Pit" value="{{ old('venue', $concert->venue) }}">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-6">
                            <div class="col-span-3 sm:col-span-2">
                                <label for="venue_address" class="block text-sm font-medium text-gray-700">Venue Address</label>
                                <div class="mt-1">
                                    <input type="text" name="venue_address" id="venue_address" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="123 Example Lane" value="{{ old('venue_address', $concert->venue_address) }}">
                                </div>
                            </div>
                        </div>

                        <div class="flex space-x-10">
                            <div class="w-1/2">
                                <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                                <div class="mt-1">
                                    <input type="text" name="city" id="city" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Laraville" value="{{ old('city', $concert->city) }}">
                                </div>
                            </div>
                            <div class="w-1/2">
                                <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                                <div class="mt-1">
                                    <input type="text" name="state" id="state" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="OH" value="{{ old('state', $concert->state) }}">
                                </div>
                            </div>
                            <div class="w-1/2">
                                <label for="zip" class="block text-sm font-medium text-gray-700">Zip</label>
                                <div class="mt-1">
                                    <input type="text" name="zip" id="zip" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="12345" value="{{ old('zip', $concert->zip) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ticket info -->
            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Ticket & Pricing</h3>
                    <p class="mt-1 text-sm leading-5 text-gray-500">
                        Set your ticket price
                    </p>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2 space-y-6">
                        <div class="flex space-x-10">
                            <div class="w-1/2">
                                <label for="ticket_price" class="block text-sm font-medium text-gray-700">Ticket Price</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">
                                        $
                                    </span>
                                    </div>
                                    <input type="text" name="ticket_price" id="ticket_price" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0.00" aria-describedby="price-currency" value="{{ old('ticket_price', $concert->ticket_price_in_dollars) }}">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm" id="price-currency">
                                        USD
                                    </span>
                                    </div>
                                </div>
                            </div>
                            <div class="w-1/2">
                                <label for="ticket_quantity" class="block text-sm font-medium text-gray-700">Ticket Quantity</label>
                                <div class="mt-1">
                                    <input type="text" name="ticket_quantity" id="ticket_quantity" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="250" value="{{ old('ticket_quantity', $concert->ticket_quantity) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error block -->
            <div>
                @if($errors->any())
                <ul>
                @foreach($errors->all() as $error)
                    <li class="text-center text-red-500">
                        {{ $error }}
                    </li>
                @endforeach
                </ul>
                @endif
            </div>

            <!-- buttons -->
            <div class="mt-8 border-t border-gray-200 pt-5">
                <div class="flex justify-end">
                <span class="ml-3 inline-flex rounded-md shadow-sm">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
                        Add Concert
                    </button>
                </span>
                </div>
            </div>
        </form>
    </div>
</x-promoter-layout>
