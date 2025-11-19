<x-app-layout>
    <form action="{{ route('password.email') }}" method="POST" class="w-[400px] mx-auto p-6 my-16 rounded-md">
        @csrf

        <h2 class="text-2xl font-semibold text-center mb-5">
          Enter your email to reset password
        </h2>

        <p class="text-center text-gray-500 mb-6">
          or
          <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-500">
            login with existing account
          </a>
        </p>

        <!-- Display session status -->
        @if (session('status'))
            <div class="text-center mb-4 text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <!-- Email input -->
        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                placeholder="Your email address"
                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
            @if ($errors->has('email'))
                <p class="text-red-500 text-sm mt-1 text-center">{{ $errors->first('email') }}</p>
            @endif
        </div>

        <button type="submit"
            class="w-full bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white py-2 rounded-md">
            Email Password Reset Link
        </button>
    </form>
</x-app-layout>
