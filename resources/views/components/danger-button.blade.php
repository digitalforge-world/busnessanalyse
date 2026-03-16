<button {{ $attributes->merge(['type' => 'submit', 'class' => 'px-8 py-4 bg-red-500/10 border border-red-500/20 rounded-xl font-mono text-[10px] text-red-500 uppercase tracking-[0.2em] hover:bg-red-500 hover:text-white transition-all duration-300 shadow-[0_0_15px_rgba(239,68,68,0.1)]']) }}>
    {{ $slot }}
</button>
