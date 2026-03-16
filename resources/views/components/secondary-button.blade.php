<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn-ghost px-8 py-4 text-[10px] tracking-[0.2em]']) }}>
    {{ $slot }}
</button>
