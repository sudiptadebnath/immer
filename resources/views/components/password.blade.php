@props([
    'size' => 12,
    'name' => 'password',
    'title' => 'password',
    'icon' => 'key',
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
        <input type="password"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            class="form-control"
            placeholder="{{ $title }}"
            title="{{ ucfirst($title) }}"
            autocomplete='off'
            @if($required) required @endif>
        <button type="button" class="btn btn-outline-secondary toggle-password" 
        onclick="togglePass('{{ $name }}', this)">
            <i class="bi bi-eye"></i>
        </button>
    </div>
    <label class="error" for="{{ $name }}"></label>
</div>

@once
@push('scripts')
    <script>
    function togglePass(id, btn) {
        const $input = $('#' + id);
        const $icon = $(btn).find('i');

        if ($input.length === 0 || $icon.length === 0) return;

        const isPassword = $input.attr('type') === 'password';
        $input.attr('type', isPassword ? 'text' : 'password');
        $icon.toggleClass('bi-eye bi-eye-slash');
    }
    </script>
@endpush
@endonce
