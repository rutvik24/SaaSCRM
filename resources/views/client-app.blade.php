<x-layout>
    <div class="flex self-center items-center">
        {{ explode('.', request()->getHttpHost())[0] }}
    </div>
    <form action="/new/app" method="post">
        @csrf
        @if($errors->any())
            <p class="text-red-500 text-xs mt-2">{{ $errors->first() }}</p>
        @endif
        <div class="flex w-3/5 items-center mt-3">
            <label class="w-80" for="name">Name:</label>
            <input type="text" name="name" id="name"
                   class="border border-gray-200 rounded p-2 w-full" required placeholder="Enter Your Name"
                   value="{{ old('name') }}">
        </div>
        @error('name')
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
