<x-layout>
    <form action="/new" method="post">
        @csrf
        <div class="flex w-3/5 items-center">
            <label class="w-80" for="subdomain">Sub Domain:</label>
            <input type="text" name="subdomain" id="subdomain"
                   class="border border-gray-200 rounded p-2 w-full" required placeholder="Enter Project Name"
                   value="{{ old('subdomain') }}">
        </div>
        @error('subdomain')
        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
        @enderror
        <div class="flex w-3/5 items-center mt-3">
            <label class="w-80" for="db_username">Database User Name:</label>
            <input type="text" name="db_username" id="db_username"
                   class="border border-gray-200 rounded p-2 w-full" required placeholder="Enter Database User Name"
                   value="{{ old('db_username') }}">
        </div>
        @error('db_username')
        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
        @enderror
        <div class="flex w-3/5 items-center mt-3">
            <label class="w-80" for="email">Email:</label>
            <input type="text" name="email" id="email"
                   class="border border-gray-200 rounded p-2 w-full" required placeholder="Enter Email"
                   value="{{ old('email') }}">
        </div>
        @error('email')
        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
        @enderror
        <button type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-6">
            Submit
        </button>
    </form>
</x-layout>
