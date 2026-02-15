@props(['color' => 'primary', 'size' => 'md', 'icon' => false])
<button {{ $attributes->class([
        'btn',
        'btn-$color' => $color,
        'btn-$size' => $size,
        'btn-icon' => $icon,
    ]) }}>
    {{ $slot }}
