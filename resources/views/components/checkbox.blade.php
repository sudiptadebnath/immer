@props([
    'size' => 12,
    'name' => 'agree',
    'title' => 'I agree',
    'checked' => false,
    'required' => false,
])

@php
    $required = filter_var($required, FILTER_VALIDATE_BOOLEAN);
    $checked = filter_var($checked, FILTER_VALIDATE_BOOLEAN);
@endphp

<div class="col-md-{{ $size }}">
    <div class="form-check d-flex align-items-center">
        <!-- Icon or label -->
        <input 
            class="form-check-input"
            type="checkbox" 
            id="{{ $name }}" 
            name="{{ $name }}"
            value="1"
            @if($checked) checked @endif
            @if($required) required @endif
        >
        
        <!-- Label -->
        <label class="form-check-label ms-1" for="{{ $name }}">
            {{ ucfirst($title) }}
        </label>
    </div>
    <label class="error" for="{{ $name }}"></label>
</div>
