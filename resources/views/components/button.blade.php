@props([
    'size' => 12,
    'name' => 'btn',
    'title' => 'Click Me',
    'icon' => 'check',
    'type' => 'button',   // can be button | submit | reset
    'variant' => 'primary', // Bootstrap button variant
    'outline' => false,   // outline style
])

<div class="col-md-{{ $size }}">
    <button 
        type="{{ $type }}" 
        name="{{ $name }}" 
        id="{{ $name }}" 
        {{ $attributes->merge(['class' => 'btn w-100 ' . ($outline ? 'btn-outline-' . $variant : 'btn-' . $variant)]) }}
    >
        @if(!empty($icon))
            <i class="bi bi-{{ $icon }} me-1"></i>
        @endif
        {{ ucfirst($title) }}
    </button>
</div>
