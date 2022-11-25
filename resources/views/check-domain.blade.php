<x-check-domain-layout>
    <div class="dark:bg-gray-800 py-10">
        <div class="flex-wrap justify-between mx-auto max-w-screen-xl">
            <div class="bg-gray-400 pl-20 pr-36 py-5 rounded-xl w-4/5">
                <h1 class="mb-16 mt-8 text-4xl font-bold text-blue-700">Check availability of domain</h1>
                <form action="{{ route('check-availability') }}" method="post">
                    @csrf
                    <div class="flex items-center">
                        <label class="w-80 text-xl" for="subdomain">Sub Domain Name:</label>
                        <input type="text" name="subdomain" id="subdomain"
                               class="rounded-md p-2 w-full" required
                               placeholder="Enter Sub Domain Name"
                               value="{{ old('subdomain') }}">
                    </div>
                    @error('subdomain')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                    <input type="text" name="planType" id="planType" hidden readonly value="{{ $planType }}">
                    <button type="submit" class="mt-10 text-xl text-white bg-blue-700 px-5 py-3 rounded-lg">Check Availability</button>
                </form>
            </div>
        </div>
    </div>
</x-check-domain-layout>
