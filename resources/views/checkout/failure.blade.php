<x-app-layout>
    <div class="w-[400px] mx-auto bg-red-600 text-white p-4 rounded">
        <strong>Payment Failed</strong><br>
        {{ $error ?? 'Something went wrong. Please try again.' }}
    </div>
</x-app-layout>
