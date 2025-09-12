@props([
    'size' => 12,
    'name' => 'datetime',
    'title' => 'Select Date & Time',
    'icon' => 'calendar-event',
    'value' => '',
    'date'=>true,
    'clock'=>false,
    'required' => false,
])
@php
    $required = filter_var($required, FILTER_VALIDATE_BOOLEAN);
    $date = filter_var($date, FILTER_VALIDATE_BOOLEAN);
    $clock = filter_var($clock, FILTER_VALIDATE_BOOLEAN);
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
        @if(trim($slot))
        <div class="compo-info" style="font-size:10px;">{{ $slot }}</div>
        @endif
    </div>
    <label class="error" for="{{ $name }}"></label>
</div>

@push('scripts')
<script>
    var dtp_{{ $name}} =  new tempusDominus.TempusDominus(document.getElementById('datetimepicker-{{ $name }}'), {
        defaultDate: '{{ old($name, $value) }}' || new Date(),
        display: {
            components: {
                calendar: {{$date ? 'true' : 'false' }},
                date: {{$date ? 'true' : 'false' }},
                month: {{$date ? 'true' : 'false' }},
                year: {{$date ? 'true' : 'false' }},
                decades: {{$date ? 'true' : 'false' }},
                clock: {{$clock ? 'true' : 'false' }},
                hours: {{$clock ? 'true' : 'false' }},
                minutes: {{$clock ? 'true' : 'false' }},
                seconds: false
            }
        },
        localization: {
            startOfTheWeek: 1, // Monday
            format: '{{ $clock ? "HH:mm" : dtfmt() }}'
        }
    });
    document.getElementById('{{ $name }}').addEventListener('change', function () {
        $(this).valid(); 
    });
    function set_dtp_{{ $name }}(vl) {
        let [day, month, year] = vl.split('-').map(Number);
        let tdDate = new tempusDominus.DateTime(year, month-1, day);
        dtp_{{ $name}}.dates.setValue(tdDate);
    }
</script>
@endpush
