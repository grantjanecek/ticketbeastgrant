<x-promoter-layout>
    <div class="container mx-auto py-5">
        <div class="flex align-baseline justify-between">
            <div class="flex align-middle space-x-2">
                <p class="text-xl font-bold">{{ $concert->title }}</p>
                /
                <p class="text-sm text-gray-500">{{ $concert->formatted_date }}</p>
            </div>
            <div class="flex align-middle space-x-2">
                <p class="text-xl font-bold">Orders</p>

                <a
                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="/backstage/concerts/{{ $concert->id }}/messages/new"
                >
                    Send Message</a>
            </div>
        </div>

        <div class="flex align-middle justify-center mt-5">
            <div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200 w-1/3">
                <div class="px-4 py-5 sm:px-6">
                    New Message
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <form action="/backstage/concerts/{{ $concert->id }}/messages" method="post" class="space-y-5">
                        @csrf
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                            <div class="mt-1">
                                <input
                                    type="text"
                                    name="subject"
                                    id="subject"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Subject"
                                    value="{{ old('subject') }}"
                                >
                            </div>
                            @if($errors->has('subject'))
                            <p class="text-sm text-red-500">
                                {{ $errors->first('subject') }}
                            </p>
                            @endif
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                                Message
                            </label>
                            <div class="mt-1 sm:mt-0 sm:col-span-2">
                                <textarea
                                    id="message"
                                    name="message"
                                    rows="5"
                                    class="max-w-lg shadow-sm block w-full focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 rounded-md"
                                    value="{{ old('message') }}"
                                ></textarea>
                            </div>
                            @if($errors->has('subject'))
                            <p class="text-sm text-red-500">
                                {{ $errors->first('subject') }}
                            </p>
                            @endif
                        </div>

                        <div>
                            <button class=" w-full justify-center inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Send Now
                                <svg class="ml-2 -mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-promoter-layout>
