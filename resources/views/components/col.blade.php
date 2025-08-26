@props([
'sz' => null, // col
'lg' => null, // col-lg
'md' => null, // col-md
'xs' => null, // col-sm (or extra small fallback)
])

@php
// Build class string dynamically
$classes = [
$sz ? "col-{$sz}" : null,
$lg ? "col-lg-{$lg}" : null,
$md ? "col-md-{$md}" : null,
$xs ? "col-sm-{$xs}" : null,
];
@endphp

<div {{ $attributes->merge(['class' => implode(' ', array_filter($classes))]) }}>
    {{ $slot }}
</div>