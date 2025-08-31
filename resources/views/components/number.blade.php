@props([
    'size' => '12',
    'name' => 'txt',
    'title' => 'Enter content',
    'icon' => 'info-circle',
    'value' => '',
    'required' => false,
    'deci' => false,
    'digcount' => 10,
])

@php
    $required = filter_var($required, FILTER_VALIDATE_BOOLEAN);
@endphp

@if($size)
<div class="col-md-{{ $size }}">
@endif
    <div class="input-group">
        <span class="input-group-text">
            @if(!empty($icon))
                <i class="bi bi-{{ $icon }}"></i>
            @else
                {{ ucfirst($title) }}
            @endif
        </span>
        <input 
            type="text"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            class="form-control"
            placeholder="{{ ucfirst($title) }}"
            title="{{ ucfirst($title) }}"
            inputmode="numeric"
            @if($digcount) maxlength="{{ $digcount }}" @endif
            pattern="{{ $digcount ? '^\d{1,'.$digcount.'}$' : '^\d+$' }}"
            oninput="this.value=this.value.replace(/[^0-9]/g,'')@if($digcount).slice(0,{{ $digcount }})@endif"
            @if($required) required @endif
        >
        {{ $slot }}
    </div>
    <label class="error" for="{{ $name }}"></label>
@if($size)
</div>
@endif