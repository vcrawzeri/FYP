<x-app-layout>
    <div class="w-[400px] mx-auto bg-emerald-500 py-2 px-3 text-white rounded">
        @if($customer)
            {{ $customer->name ?? $customer->email }}, your order has been completed!
        @else
            Your order has been completed successfully!
        @endif
    </div>
</x-app-layout>
