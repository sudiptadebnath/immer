@props([
    'size' => '12',
    'name' => 'btn',
    'title' => 'Click Me',
    'icon' => 'check',
    'type' => 'button',   // button|submit|reset|link
    'style' => 'primary',
    'outline' => false,
    'href' => '#',        // only used if type=link
])
@php
    $isTitleOnly = str_starts_with($title, '!');
    $titleText = $isTitleOnly ? ltrim($title, '!') : $title;
@endphp

@if($size)
<div class="col-md-{{ $size }}">
@endif
    <{{ $type === 'link' ? 'a' : 'button' }}
        @if($type !== 'link')
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
        @else
            href="{{ $href }}"
            id="{{ $name }}"
        @endif
        title="{{ ucfirst($titleText) }}"
        {{ $attributes->class([
            'btn',
            $size ? 'w-100' : '',
            $outline ? 'btn-outline-' . $style : 'btn-' . $style,
        ]) }}
    >
        @if(!empty($icon))
            <i class="bi bi-{{ $icon }} me-1"></i>
        @endif
        @unless($isTitleOnly)
            {{ ucfirst($titleText) }}
        @endunless
    </{{ $type === 'link' ? 'a' : 'button' }}>
@if($size)
</div>
@endif
