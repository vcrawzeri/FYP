<div class="max-w-md mx-auto bg-white shadow-lg rounded-xl p-6 mt-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">
        Your Order <span class="text-blue-600">{{ $order->status }}</span>
    </h2>

    <p class="text-gray-600 mb-4">
        Link to your order:
        <a href="{{ route('order.view', $order) }}" class="text-blue-500 hover:text-blue-700 font-medium underline">
            Order #{{ $order->id }}
        </a>
    </p>
</div>
