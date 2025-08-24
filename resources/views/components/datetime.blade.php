@props([
    'size' => 12,
    'name' => 'datetime',
    'title' => 'Select Date & Time',
    'icon' => 'calendar-event',
    'value' => '',
    'date'=>'true',
    'clock'=>'false',
    'required' => false,
])
@php
    $required = filter_var($required, FILTER_VALIDATE_BOOLEAN);
@endphp

<div class="col-md-{{ $size }}">
    <div class="input-group" id="datetimepicker-{{ $name }}">
        <span class="input-group-text">
            @if(!empty($icon))
                <i class="bi bi-{{ $icon }}"></i>
            @else
                {{ ucfirst($title) }}
            @endif
        </span>
        <input type="text"
               id="{{ $name }}"
               name="{{ $name }}"
               value="{{ old($name, $value) }}"
               class="form-control"
               placeholder="{{ ucfirst($title) }}"
               title="{{ ucfirst($title) }}"
               autocomplete='off'
               @if($required) required @endif>
    </div>
    <label class="error" for="{{ $name }}"></label>
</div>

@push('scripts')
<script>
    new tempusDominus.TempusDominus(document.getElementById('datetimepicker-{{ $name }}'), {
        defaultDate: '{{ old($name, $value) }}' || new Date(),
        display: {
            components: {
                calendar: true,
                date: {{$date}},
                month: true,
                year: true,
                decades: true,
                clock: {{$clock}},
                hours: {{$clock}},
                minutes: {{$clock}},
                seconds: false
            }
        },
        localization: {
            startOfTheWeek: 1, // Monday
            format: '{{ filter_var($clock, FILTER_VALIDATE_BOOLEAN) ? dttmfmt() : dtfmt() }}'
        }
    });
    document.getElementById('{{ $name }}').addEventListener('change', function () {
        $(this).valid(); 
    });
</script>
@endpush
