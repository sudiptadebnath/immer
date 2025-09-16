@props([
    'size' => 12,
    'name' => 'txt',
    'title' => 'Enter content',
    'icon' => '',
    'value' => '',
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
        <input type="text"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            class="form-control"
            placeholder="{{ ucfirst($title) }}"
            title="{{ ucfirst($title) }}"
            @if($required) required @endif >
        @if(trim($slot))
        <div class="compo-info" style="font-size:10px;">{{ $slot }}</div>
        @endif
    </div>
    <label class="error" for="{{ $name }}"></label>
</div>

@push("scripts")
<script>
flatpickr("#{{ $name }}", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",   // 12-hour format with AM/PM
    minTime: "16:00",      // 4:00 PM
    maxTime: "23:59",      // 11:59 PM
    minuteIncrement: 1,   // generates 4:00, 4:30, 5:00, ... 11:30 PM
    time_24hr: true,    // switch to true if you want 24-hour format
    defaultDate: "16:00",
    disableMobile: true
});
</script>
@endpush