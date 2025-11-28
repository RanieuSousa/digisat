<x-authentication-layout>
    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-60 h-auto">


@if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif
    <!-- Form -->
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="space-y-4">
            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            </div>
            <div>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" type="password" name="password" required autocomplete="current-password" />
            </div>
        </div>
        <div class="flex items-center justify-between mt-6">

                <x-button class="ml-3 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 text-lg rounded-md w-full">
                    {{ __('Entrar') }}
                </x-button>


        </div>
    </form>
    <x-validation-errors class="mt-4" />
    <!-- Footer -->

</x-authentication-layout>
