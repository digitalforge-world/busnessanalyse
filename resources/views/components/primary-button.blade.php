<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-primary px-8 py-4 text-[10px] tracking-[0.2em]']) }}>
    {{ $slot }}
</button>
