<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" crossorigin="anonymous"></script>
{{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"--}}
{{--      integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">--}}
<x-check-domain-layout>
    <div class="dark:bg-gray-800 py-10">
        <div class="flex-wrap justify-between mx-auto max-w-screen-xl">
            <div class="bg-gray-400 pl-20 pr-36 py-5 rounded-xl w-4/5">
                <h1 class="mb-16 mt-8 text-4xl font-bold text-blue-700">Sign Up</h1>
                <form action="{{ route('auth.store') }}" method="post">
                    @csrf
                    <div class="flex items-center mb-5">
                        <label class="w-80 text-xl" for="username">Username:</label>
                        <input type="username" name="username" id="username"
                               class="rounded-md p-2 w-full" required
                               placeholder="Enter Your Username"
                               value="{{ old('username') }}">
                    </div>
                    @error('username')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                    <div class="flex items-center mb-5">
                        <label class="w-80 text-xl" for="name">Name:</label>
                        <input type="text" name="name" id="name"
                               class="rounded-md p-2 w-full" required
                               placeholder="Enter Your Name"
                               value="{{ old('name') }}">
                    </div>
                    @error('name')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                    <div class="flex items-center mb-5">
                        <label class="w-80 text-xl" for="email">Email:</label>
                        <input type="text" name="email" id="email"
                               class="rounded-md p-2 w-full" required
                               placeholder="Enter Email Address"
                               value="{{ old('email') }}">
                    </div>
                    @error('email')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                    <div class="flex items-center mb-5">
                        <label class="w-80 text-xl" for="password">Password:</label>
                        <input type="password" name="password" id="password"
                               class="rounded-md p-2 w-full" required
                               placeholder="Enter Your Password"
                               value="{{ old('password') }}">
                    </div>
                    @error('password')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                    <div class="flex items-center mb-5">
                        <label class="w-80 text-xl" for="company_name">Company Name:</label>
                        <input type="text" name="company_name" id="company_name"
                               class="rounded-md p-2 w-full" required
                               placeholder="Enter Your Company Name"
                               value="{{ old('company_name') }}">
                    </div>
                    @error('company_name')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                    <div class="flex items-center mb-5">
                        <label class="w-80 text-xl" for="subdomain">Sub Domain:</label>
                        <input type="text" name="subdomain" id="subdomain"
                               class="rounded-md p-2 w-full" required
                               placeholder="Enter Your Sub Domain"
                               readonly
                               value="{{ $subdomain }}">
                    </div>
                    @error('subdomain')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                    <input type="text" name="planType" id="planType" hidden readonly value="{{ $planType }}">
                    <button type="submit" class="mt-10 text-xl text-white bg-blue-700 px-5 py-3 rounded-lg">Sign Up
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-check-domain-layout>
