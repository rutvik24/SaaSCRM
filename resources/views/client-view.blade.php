<x-layout>
    @if($clients->count())

    @foreach($clients as $client)
        <div class="flex self-center items-center">
            Name: {{ $client->name }}
        </div>
        <div class="flex self-center items-center">
            Email: {{ $client->email }}
        </div>
    @endforeach
    @endif
    <a href="/new/app">New App</a>
</x-layout>
