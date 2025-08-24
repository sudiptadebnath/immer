@props([
    'size' => 12,
    'name' => 'password',
    'title' => 'password',
    'icon' => 'person',
    'value' => [],
])
<div class="col-md-{{$size}}">
    <div class="input-group">
        <span class="input-group-text">
            @if(!empty($icon))
            <i class="bi bi-{{ $icon }}"></i>
            @else
            {{ ucfirst($title) }}
            @endif
        </span>
        <select name="{{$name}}" id="{{$name}}" class="form-select" title="{{ ucfirst($title) }}" required>
        <option value="">Select {{$title}}</option>
        @foreach ($value as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
        @endforeach
        </select>
    </div>
    <label class="error" for="{{$name}}"></label>
</div>

