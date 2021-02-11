<x-promoter-layout>
    <div class="container mx-auto py-5">
        <!-- Header text -->
        <div class="flex align-baseline justify-between">
            <p class="text-4xl">New Concert</p>
            <a href="/backstage/concerts" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                All Concerts
            </a>
        </div>
        <div>
            <form action="/backstage/concerts/new" method="POST"  class="space-y-10" enctype="multipart/form-data">
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
                                        <input type="text" name="title" id="title" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Slayer" value="{{ old('title') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-6">
                                <div class="col-span-3 sm:col-span-2">
                                <label for="subtitle" class="block text-sm font-medium text-gray-700">Subtitle</label>
                                    <div class="mt-1">
                                        <input type="text" name="subtitle" id="subtitle" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="with Opensers (optional)" value="{{ old('subtitle') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                            <label for="additional_information" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                                Additional Information
                            </label>
                            <div class="mt-1 sm:mt-0 sm:col-span-2">
                                <textarea id="additional_information" name="additional_information" rows="3" class="max-w-lg shadow-sm block w-full focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 rounded-md" placeholder="This concert is 19+" value="{{ old('additional_information') }}"></textarea>
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
                                        <input type="text" name="date" id="date" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="yyy-mm-dd" value="{{ old('date') }}">
                                    </div>
                                </div>
                                <div class="w-1/2">
                                    <label for="time" class="block text-sm font-medium text-gray-700">Time</label>
                                    <div class="mt-1">
                                        <input type="text" name="time" id="time" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="7:00pm" value="{{ old('time') }}">
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
                                        <input type="text" name="venue" id="venue" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="The Mosh Pit" value="{{ old('venue') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-6">
                                <div class="col-span-3 sm:col-span-2">
                                    <label for="venue_address" class="block text-sm font-medium text-gray-700">Venue Address</label>
                                    <div class="mt-1">
                                        <input type="text" name="venue_address" id="venue_address" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="123 Example Lane" value="{{ old('venue_address') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="flex space-x-10">
                                <div class="w-1/2">
                                    <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                                    <div class="mt-1">
                                        <input type="text" name="city" id="city" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Laraville" value="{{ old('city') }}">
                                    </div>
                                </div>
                                <div class="w-1/2">
                                    <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                                    <div class="mt-1">
                                        <input type="text" name="state" id="state" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="OH" value="{{ old('state') }}">
                                    </div>
                                </div>
                                <div class="w-1/2">
                                    <label for="zip" class="block text-sm font-medium text-gray-700">Zip</label>
                                    <div class="mt-1">
                                        <input type="text" name="zip" id="zip" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="12345" value="{{ old('zip') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Concert Poster info -->
                <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                    <div class="md:grid md:grid-cols-3 md:gap-6">
                        <div class="md:col-span-1">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Concert Poster</h3>
                            <p class="mt-1 text-sm leading-5 text-gray-500">
                                Post a cool photo of the up coming concert.
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div
                                    x-data="{
                                        fileUrl: null,

                                        loadFile(file){
                                            this.fileUrl = URL.createObjectURL(file);
                                        },

                                        removeFile(){
                                            this.fileUrl = null;
                                            this.$refs.poster_image.value = null;
                                        }
                                    }"
                                    class="space-y-1 text-center"
                                >
                                    <div x-show="! fileUrl">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class=" text-center text-sm text-gray-600">
                                            <label for="poster_image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>Upload a file</span>
                                                <input x-ref="poster_image" @input="loadFile($event.target.files[0])" id="poster_image" name="poster_image" type="file" class="sr-only">
                                            </label>
                                        </div>
                                    </div>
                                    <template x-if="fileUrl">
                                        <div class="relative">
                                            <img :src="fileUrl" alt="" class="h-20 w-auto mx-auto">
                                            <button type="button" class="absolute top-0 right-0 -mr-2 -mt-2" @click="removeFile">
                                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
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
                                        <input type="text" name="ticket_price" id="ticket_price" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0.00" aria-describedby="price-currency"  value="{{ old('ticket_price') }}">
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
                                        <input type="text" name="ticket_quantity" id="ticket_quantity" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="250" value="{{ old('ticket_quantity') }}">
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
    </div>
</x-promoter-layout>
