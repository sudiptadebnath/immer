@props([
    'size' => 12,
    'name' => 'txt',
    'title' => 'Enter content',
    'icon' => '',
    'value' => '',
    'typ' => 'text',
    'required' => false,
])
@php
    $required = filter_var($required, FILTER_VALIDATE_BOOLEAN);
@endphp

<div class="col-md-{{ $size }}">
    <div class="input-group">
        @if($icon != "!")
        <span class="input-group-text">
            @if(!empty($icon))
            <i class="bi bi-{{ $icon }}"></i>
            @else
            {{ ucfirst($title) }}
            @endif
        </span>
        @endif
        <input type="{{ $typ }}"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            class="form-control"
            placeholder="{{ ucfirst($title) }}"
            title="{{ ucfirst($title) }}"
            @if($required) required @endif {{ $attributes }}>
        @if(trim($slot))
        <div class="compo-info" style="font-size:10px;">{{ $slot }}</div>
        @endif
    </div>
    <label class="error" for="{{ $name }}"></label>
</div>
