@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-mono text-[10px] text-muted uppercase tracking-[0.2em] mb-2']) }}>
    {{ $value ?? $slot }}
</label>
