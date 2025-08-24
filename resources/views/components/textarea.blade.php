@props([
'size' => 12,
'name' => 'txt',
'title' => 'Enter content',
'icon' => 'info-circle',
'value' => '',
'required' => false,
])
@php
    $required = filter_var($required, FILTER_VALIDATE_BOOLEAN);
@endphp

<div class="col-md-{{ $size }}">
    <div class="input-group">
        <span class="input-group-text">
            @if(!empty($icon))
            <i class="bi bi-{{ $icon }}"></i>
            @else
            {{ ucfirst($title) }}
            @endif
        </span>
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            class="form-control"
            placeholder="{{ ucfirst($title) }}"
            title="{{ ucfirst($title) }}"
            rows="4"
            @if($required) required @endif>{{ old($name, $value) }}</textarea>
    </div>
    <label class="error" for="{{ $name }}"></label>
</div>