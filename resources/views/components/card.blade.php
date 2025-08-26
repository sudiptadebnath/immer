@props([
    'title' => 'card',
    'style' => 'primary',
    'style2' => 'white',
    'icon' => 'box-arrow-in-right',
    'footer' => '',
])

<div class="card border-{{ $style }} w-100 p-0">
    <div class="card-header bg-{{ $style }} text-{{ $style2 }} py-1">
        <p class="card-title m-0 p-0">
            @if($icon)<i class="bi bi-{{ $icon }} me-2"></i>@endif{{ $title}}
        </p>
    </div>
    <div class="card-body p-3">
        {{ $slot }}
    </div>
    @if($footer)
    <div class="card-footer p-0">
        {{ $footer }}
    </div>
    @endif
</div>

