@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-ink2 border-muted2/20 focus:border-primary-500 focus:ring-1 focus:ring-primary-500/20 rounded-xl text-white font-mono text-sm placeholder:text-muted2/50 transition duration-300 w-full py-4 px-5']) }}>
