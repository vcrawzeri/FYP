<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white py-2 rounded-md']) }}>
    {{ $slot }}
</button>
