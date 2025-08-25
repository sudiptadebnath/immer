@props([
    'sz' => '12',
    'title' => 'card',
    'style' => 'primary',
    'style2' => 'white',
    'icon' => 'box-arrow-in-right',
    'footer' => '',
])

<div class="card border-{{ $style }} col-md-{{ $sz}} p-0">
    <div class="card-header bg-{{ $style }} text-{{ $style2 }} py-1">
        <p class="card-title m-0 p-0">
            <i class="bi bi-{{ $icon }}"></i> {{ $title}}
        </p>
    </div>
    <div class="card-body p-3">
        {{ $slot }}
    </div>
    @if($footer)
    <div class="card-footer">
        {{ $footer }}
    </div>
    @endif
</div>

